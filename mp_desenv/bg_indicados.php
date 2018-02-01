<?php
    //ini_set('display_errors','on');
	//error_reporting(E_ALL);

    //session_start();
	header('Content-Type: text/html; charset=utf-8');
	include("/var/www/mp_desenv/bg_conexao_bd.php");
    include("/var/www/mp_desenv/bg_funcoes_genericas.php");

	//  Configurações do Script
    $funcao = "indicacao";
	$erro = 0;
	$msg = "Nada foi executado!";
	$opt = "";

    //Verifica se usuário está Logado
    include("/var/www/mp_desenv/bg_protege_php.php");
    $status_log = status_login($resposta);

    //echo "Status_log: " . $status_log . "<br><br>";
    //echo "get fn: " . $_GET["fn"] . "<br>";

    if($status_log=="mantem"){
        if(isset($_GET["fn"]) || isset($_POST["fn"])){
            if(isset($_GET["fn"])){
                $fn = addslashes($_GET["fn"]);
            }else{
                $fn = addslashes($_POST["fn"]);
            }
            
            if($_SESSION["user_access"] == 'vendedor' || $_SESSION["user_access"] == 'administrador'){
                switch ($fn){
                    case 'carregar_indicados':
                        ler_indicados();
                        $opt = 'exibir_indicados';
                    break;
                    case 'cadastrar_unico':
                        cadastrar_unico();
                    break;
                    case 'remover_indicados':
                        $mail=$_GET["mail"];
                        $id=$_GET["id"];
                        remover_indicados($mail, $id);
                    break;
                }
            }else{
                $erro = 0;
                $msg = "Você não possui autorização para ver essa página.";
                $opt = "expulsa";
            }
        }else{
            $erro = 1;
            $msg = "Informar dados completos.";
        }
    }else{
        $erro = 1;
        $msg = "Usuário não autenticado.";
        $opt = "expulsa";
    }

    function ler_indicados(){
        global $erro,$msg,$opt;
        
        $sql = "select
                    a.nome
                    ,a.email
                    ,a.celular
                    ,case when b.id >= 1 then 'Cadastrado'
                        else 'Aguardando'
                    end as status_indic
                from
                    tb_indicacao a
                left join tb_usuarios b
                on (a.email=b.email)
                where a.ID_DISTRIBUIDOR = ".$_SESSION["user_id"]."
                order by
                    a.nome
                    ,a.email;";

        //echo "SQL: " . $sql . "<br><br>";
        include("/var/www/mp_desenv/bg_conexao_bd.php");
        $query = mysqli_query($GLOBALS['con'],$sql);

        $msg = "";
        if(mysqli_num_rows($query)>0){
            $erro = 0;
            for($i=0;$i<mysqli_num_rows($query);$i++){
                $row = mysqli_fetch_row($query);

                $msg = $msg . '
                    <tr>
                        <td>'.$row[0].'</td>
                        <td>'.$row[1].'</td>
                        <td>'.$row[2].'</td>
                        <td>'.$row[3].'</td>
                        <td><a href="javascript:void(0);" onclick="remover_indicacao('.$row[1].','.$_SESSION["user_id"].');"><i class="fa fa-trash fa-lg" aria-hidden="true"></i></a></td>
                    </tr>';
            }
        }else{
            $erro=1;
            $msg = "Não encontrei nenhum indicado no banco de dados.";
            $opt = "Toast";
        }
    }

    function cadastrar_unico(){
        global $erro,$msg,$opt;
        
        if(isset($_GET["nome"]) && isset($_GET["email"]) && isset($_GET["celular"])){
            $nome = $_GET["nome"];
            $email = $_GET["email"];
            $celular = $_GET["celular"];
            
            $sql = "select
                        id_distribuidor
                        ,email
                    from
                        tb_indicacao
                    where email ='".$email."';";
            include("/var/www/mp_desenv/bg_conexao_bd.php");
            $query = mysqli_query($GLOBALS['con'],$sql);
            
            if(mysqli_num_rows($query)>0){
                $erro = 1;
                $msg = "Esta indicação ja foi efetuada por você ou por outro distribuidor.";
                $opt = "Toast";
            }else{
                $sql = "INSERT INTO tb_indicacao
                        (id_distribuidor,nome,email,celular)
                        VALUES (".$_SESSION["user_id"].",'".$nome."','".$email."','".$celular."');";
                include("/var/www/mp_desenv/bg_conexao_bd.php");
                $query = mysqli_query($GLOBALS['con'],$sql);
                
                $sql2 = "SELECT
                        id_distribuidor
                        ,email
                    FROM
                        tb_indicacao
                    WHERE email ='".$email."' and id_distribuidor=".$_SESSION['user_id'].";";
                
                include("/var/www/mp_desenv/bg_conexao_bd.php");
                $query2 = mysqli_query($GLOBALS['con'],$sql2);
                
                //echo "consulta: " . $sql2 . "<br>";
                if(mysqli_num_rows($query2)>0){
                    //Enviar email de indicação
                    $dados = array("nome"=>$nome,"email"=>$email);
                    include_once("/var/www/mp_desenv/bg_enviaemail.php");
                    $resposta = define_envia('indicacao',$dados);
                                        
                    $erro = 0;
                    $msg = "Indicação de ".$nome." feita com sucesso!";
                    $opt = "cadastro_efetuado";
                }else{
                    $erro = 1;
                    $msg = "Houve erro na inclusão no banco de dados.";
                    $opt = "Toast"; 
                }
            }
        }else{
            $erro = 1;
            $msg = "Requisição incompleta";
            $opt = "Toast";
        }
    }
function remover_indicados($mail, $id){
    
     $sql = "delete from tb_indicacao where email = ".$mail." and ID_DISTRIBUIDOR=".$id.";";
                include("/var/www/mp_desenv/bg_conexao_bd.php");
                mysqli_query($GLOBALS['con'],$sql);
    
}

	$envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt);
	print json_encode($envia_resposta);
?>