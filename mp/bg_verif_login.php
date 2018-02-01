<?php
    //ini_set('display_errors','on');
	//error_reporting(E_ALL);

    session_start();
	header('Content-Type: text/html; charset=utf-8');

	//Configurações do Script
    $funcao = "status_login";
	$erro = 0;
	$msg = "Nada foi executado!";
	$opt = "";

    //Verifica se usuário está Logado
    include("/var/www/mp/bg_protege_php.php");
    $status_log = status_login($resposta);

    //Se estiver logado executa o código
    if($status_log=="mantem"){
        $msg = "Ir para loja";
        $erro = 0;
        $opt = "exibir_loja";
    }else{
        if(isset($_GET['d'])){
            $par_d = $_GET['d'];
            $sql_consulta = "select id, email from tb_usuarios where email='".$par_d."' limit 1;";
            $consulta = mysqli_query($GLOBALS['con'],$sql_consulta);

            if(mysqli_num_rows($consulta)>0){
                $row = mysqli_fetch_row($consulta);
                $msg = array("id"=>$row[0],"email"=>$row[1]);
                $erro = 0;
                $opt = "exibir_login";
            }else{
                $msg = "Exibir a tela de login";
                $erro = 0;
                $opt = "exibir_login";
            }
        }else{
            $msg = "Exibir a tela de login";
            $erro = 0;
            $opt = "exibir_login";
        }
    } 

	$envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt);
	print json_encode($envia_resposta);
?>
