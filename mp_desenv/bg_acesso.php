<?php
    //ini_set('display_errors','on');
	// error_reporting(E_ALL);

	header('Content-Type: text/html; charset=utf-8');

    $funcao = "reset_senha";
    $erro = 0;
    $msg = "Nada foi executado!";
    $opt = "";
    $opt2 = "";

    //Verifica se usuário está Logado
    //include("/var/www/mp_desenv/bg_protege_php.php");
    //$status_log = status_login($resposta); //acredite que $resposta existe, ele está no bg_protege_php
    //echo "status_log: ".$status_log."<br>";
    
    //Se estiver logado executa o código
    if(isset($_GET["fn"]) || isset($_POST["fn"])){
        if(isset($_GET["fn"])){
            $fn = addslashes($_GET["fn"]);
        }else{
            $fn = addslashes($_POST["fn"]);
        }

        switch ($fn){
            case 'busca_user':
                if(isset($_POST['id'])){
                    $id = $_POST['id'];
                    $sql = "SELECT nome, token from tb_usuarios where id = ".$id.";";

                    include("/var/www/mp_desenv/bg_conexao_bd.php");
                    $query = mysqli_query($GLOBALS['con'],$sql);

                    if(mysqli_num_rows($query)>0){
                        $row = mysqli_fetch_row($query);
                        $erro = 0;
                        $msg = $row[0];
                        $opt = "mostra_nome";
                    }else{
                        $erro = 1;
                        $msg = "Cadastro não localizado";
                        $opt = "Toast";
                    }
                }else{
                    $erro = 1;
                    $msg = "Solicitação incompleta";
                    $opt = "Toast";
                }
            break;
            case "reset":
                if(isset($_POST["token"]) && isset($_POST["nova"]) && isset($_POST["id"])){
                    $id = addslashes($_POST["id"]);
                    $token = addslashes($_POST["token"]);
                    $nova = addslashes($_POST["nova"]);

                    $sql = "select id,token from tb_usuarios where id=".$id.";";

                    include("/var/www/mp_desenv/bg_conexao_bd.php");
                    $query = mysqli_query($GLOBALS['con'],$sql);
                    $row = mysqli_fetch_row($query);

                    if(mysqli_num_rows($query)>0 && $token==md5($row[1])){
                        $novo_token = rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9);

                        $sql = "UPDATE tb_usuarios SET senha ='". $nova ."', token='".$novo_token."' where id=".$id.";";
                        include("/var/www/mp_desenv/bg_conexao_bd.php");
                        $query = mysqli_query($GLOBALS['con'],$sql);

                        $sql = "INSERT INTO tb_esqueci_senha (user_id,acao) VALUES (".$id.",'alteracao');";
                        include("/var/www/mp_desenv/bg_conexao_bd.php");
                        $query = mysqli_query($GLOBALS['con'],$sql);

                        $erro = 0;
                        $msg = 'A sua senha foi alterada com sucesso!';
                        $opt = 'sucesso';
                    }else{
                        $erro = 1;
                        $msg = 'ID ou token incorretos. Esse link só pode ser utilizado uma vez.';
                        $opt = 'Toast';
                    }                       
                }
            break;
            case "naofui":
                if(isset($_POST["id"])){
                    $id = addslashes($_POST["id"]);
                    
                    $sql = "SELECT nome, token from tb_usuarios where id = ".$id.";";

                    include("/var/www/mp_desenv/bg_conexao_bd.php");
                    $query = mysqli_query($GLOBALS['con'],$sql);

                    if(mysqli_num_rows($query)>0){
                        $row = mysqli_fetch_row($query);
                        
                        $sql = "INSERT INTO tb_esqueci_senha (user_id,acao) VALUES (".$id.",'Não fui eu');";
                        include("/var/www/mp_desenv/bg_conexao_bd.php");
                        $query = mysqli_query($GLOBALS['con'],$sql);

                        $erro = 0;
                        $msg = 'Iremos monitorar ainda mais seu acesso a fim de garantir sua segurança. Obrigado!';
                        $opt = 'sucesso';
                    }else{
                        $erro = 1;
                        $msg = "Cadastro não localizado";
                        $opt = "Toast";
                    }
                }else{
                    $erro = 1;
                    $msg = 'Solicitação incompleta';
                    $opt = 'Toast';
                }
            break;
            case "existe_email":
                if(isset($_GET["email"])){
                    $email = addslashes($_GET["email"]);
                    $sql = "SELECT id, email from tb_usuarios where email = '".$email."';";
                    
                    //echo "sql: " . $sql . "<br>";
                    
                    include("/var/www/mp_desenv/bg_conexao_bd.php");
                    $query = mysqli_query($GLOBALS['con'],$sql);
                    $row = mysqli_fetch_row($query);

                    include("/var/www/mp_desenv/bg_enviaemail.php");
                    if(mysqli_num_rows($query)>0){
                        $resposta = define_envia('esqueci',$row[1]);
                        $resposta = json_decode($resposta);
                        //var_dump($resposta);
                        $erro = $resposta -> erro;
                        $msg = $resposta -> msg;
                        $opt = $resposta -> opt;
                    }else{
                        $erro = 1;
                        $msg = "Cadastro não localizado";
                        $opt = "Toast";
                    }
                }
            break;
        }
    }else{
        $erro = 1;
        $msg = "Solicitação Incorreta.";
        $opt = "expulsa";
    }

    $envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt,"opt2"=>$opt2);
    print json_encode($envia_resposta, JSON_PRETTY_PRINT);
?>