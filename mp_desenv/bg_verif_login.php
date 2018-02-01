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
    include("/var/www/mp_desenv/bg_protege_php.php");
    $status_log = status_login($resposta);
    
    //Se estiver logado executa o código
    if($status_log=="mantem"){
        $msg = "Ir para loja";
        $erro = 0;
        $opt = "exibir_loja";
    }else{
        $msg = "Exibir a tela de login";
        $erro = 0;
        $opt = "exibir_login";
    }

	$envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt);
	print json_encode($envia_resposta);
?>