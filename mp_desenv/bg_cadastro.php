<?php
    ini_set('display_errors','on');
	error_reporting(E_ALL);

	header('Content-Type: text/html; charset=utf-8');

    $funcao = "cadastro";
    $erro = 0;
    $msg = "Nada foi executado!";
    $opt = "";
    $opt2 = "";

    //Verifica se usuário está Logado
    include("/var/www/mp_desenv/bg_protege_php.php");
    $status_log = status_login($resposta); //acredite que $resposta existe, ele está no bg_protege_php
    //echo "status_log: ".$status_log."<br>";
    
    //Se estiver logado executa o código
    if($status_log=="mantem"){
        if(isset($_GET["fn"]) || isset($_POST["fn"])){
            if(isset($_GET["fn"])){
                $fn = addslashes($_GET["fn"]);
            }else{
                $fn = addslashes($_POST["fn"]);
            }
            
            switch ($fn){
                case 'le_cadastro':
                    $sql = "SELECT id, nome_completo, email, cep, uf, cidade, bairro, end_cobranca, num_imovel, complemento, cel, pg_facebook, txt_apres, cpf, imagem  from tb_usuarios where id = ". $_SESSION['user_id'].";";
                    include("/var/www/mp_desenv/bg_conexao_bd.php");
                    $query = mysqli_query($GLOBALS['con'],$sql);
        
                    if(mysqli_num_rows($query)>0){
                        $row = mysqli_fetch_row($query);
                        $erro = 0;
                        $msg = array(
                            "id" => $row[0]
                            ,"nome_completo" => $row[1]
                            ,"email" => $row[2]
                            ,"cep" => $row[3]
                            ,"uf" => $row[4]
                            ,"cidade" => $row[5]
                            ,"bairro" => $row[6]
                            ,"end_cobranca" => $row[7]
                            ,"num_imovel" => $row[8]
                            ,"complemento" => $row[9]
                            ,"cel" => $row[10]
                            ,"pg_facebook" => $row[11]
                            ,"txt_apres" => $row[12]
                            ,"cpf" => $row[13]
                            ,"foto" => $row[14]
                        );
                        $opt = "le_cadastro";
                    }else{
                        $erro = 1;
                        $msg = "Cadastro não localizado";
                        $opt = "Toast";
                    }
                break;
                case "salva_cadastro":
                    if(isset($_POST["nome"]) && isset($_POST["sobrenome"]) && isset($_POST["nome_completo"]) && isset($_POST["cep"]) && isset($_POST["uf"]) && isset($_POST["cidade"]) && isset($_POST["bairro"]) && isset($_POST["end_cobranca"]) && isset($_POST["num_imovel"]) && isset($_POST["complemento"]) && isset($_POST["pais"]) && isset($_POST["pg_facebook"]) && isset($_POST["cel"]) && isset($_POST["txt_apres"])){
                        
                        $nome = addslashes($_POST["nome"]);
                        $sobrenome = addslashes($_POST["sobrenome"]);
                        $nome_completo = addslashes($_POST["nome_completo"]);
                        $cep = addslashes($_POST["cep"]);
                        $uf = addslashes($_POST["uf"]);
                        $cidade = addslashes($_POST["cidade"]);
                        $bairro = addslashes($_POST["bairro"]);
                        $end_cobranca = addslashes($_POST["end_cobranca"]);
                        $num_imovel = addslashes($_POST["num_imovel"]);
                        $complemento = addslashes($_POST["complemento"]);
                        $pais = addslashes($_POST["pais"]);
                        $pg_facebook = addslashes($_POST["pg_facebook"]);
                        $cel = addslashes($_POST["cel"]);
                        $txt_apres = addslashes($_POST["txt_apres"]);
                        
                        $sql = "UPDATE tb_usuarios SET nome='".$nome."', sobrenome='".$sobrenome."', nome_completo='".$nome_completo."', cep='".$cep."', uf='".$uf."', cidade='".$cidade."', bairro='".$bairro."', end_cobranca='".$end_cobranca."', num_imovel='".$num_imovel."', complemento='".$complemento."', pais='".$pais."', pg_facebook='".$pg_facebook."', cel='".$cel."', txt_apres='".$txt_apres."' WHERE id=".$_SESSION["user_id"].";";
                        
                        //echo "SQL: " . $sql . "<br>";
                        
                        include("/var/www/mp_desenv/bg_conexao_bd.php");
                        $query = mysqli_query($GLOBALS['con'],$sql);
                        
                        $erro = 0;
                        $msg = "Dados salvos com sucesso!";
                        $opt = "Toast";
                        
                    }else{
                        $erro = 1;
                        $msg = "Os dados para cadastro estão incompletos";
                        $opt = "Toast";
                    }                    
                break;
                case "salva_senha":
                    if(isset($_POST["atual"]) && isset($_POST["nova"])){
                        $senha = addslashes($_POST["atual"]);
                        $nova = addslashes($_POST["nova"]);
                        
                        $sql = "select id,NOME,SOBRENOME,NOME_COMPLETO,CEL,access_group,id_distribuidor from tb_usuarios where id=". $_SESSION["user_id"]." and  senha ='". $senha ."';";
                        
                        include("/var/www/mp_desenv/bg_conexao_bd.php");
                        $query = mysqli_query($GLOBALS['con'],$sql);
                        
                        if(mysqli_num_rows($query)>0){
                            $sql = "UPDATE tb_usuarios SET senha ='". $nova ."' where id=".$_SESSION["user_id"].";";
                            include("/var/www/mp_desenv/bg_conexao_bd.php");
                            $query = mysqli_query($GLOBALS['con'],$sql);
                            
                            $erro = 0;
                            $msg = 'A sua senha foi alterada com sucesso!';
                            $opt = 'Toast';
                            $opt2 = 'refresh';
                        }else{
                            $erro = 1;
                            $msg = 'A senha atual está incorreta, por favor tente novamente.';
                            $opt = 'Toast';
                        }                       
                    }
                break;
            }
        }else{
            $erro = 1;
            $msg = "Solicitação Incorreta.";
            $opt = "expulsa";
        }
    }else{
        $erro = 1;
        $msg = "Usuário não autenticado.";
        $opt = "expulsa";
    }

    $envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt,"opt2"=>$opt2);
    print json_encode($envia_resposta, JSON_PRETTY_PRINT);
?>