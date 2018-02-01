<?php

//ini_set('display_errors','on');
//error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8');
session_start();
include("/var/www/mp/bg_conexao_bd.php");

$msg = "Nada foi executado!";
$funcao = "sacola";
$opt = "sacola__remover";
$msg_sucesso="Sucesso!!!";
$erro = 0;
//Verifica se os campos que são obrigatórios estão preenchidos
if(isset($_GET["id"])){
	$ID= addslashes($_GET["id"]);
}else{
	$erro = 1;
	$msg = "Nenhum ID de Produto foi recebido";
}

//Verifica se distribuidor está definido
if(isset($_SESSION['user_id_distribuidor'])){
	$user_id_distribuidor = addslashes($_SESSION["user_id_distribuidor"]);
}else{
	$erro = 1;
	$msg = "Nenhum distribuidor selecionado";
}

if(isset($_SESSION['user_id'])){
	$user_id = addslashes($_SESSION["user_id"]);
}else{
	$erro = 1;
	$msg = "Nenhum usuário selecionado";
}

include("/var/www/mp/bg_protege_php.php");
$status_log = status_login($resposta); 
if($status_log=="mantem"){
        $sql="DELETE FROM tb_carrinho where ID_USER = ". $user_id . " and ID_PRODUTO = " . $ID . ";";
        mysqli_query($GLOBALS['con'],$sql);
        $msg=$msg_sucesso;
        
}else{
    
        $erro = 1;
        $opt = "popup";
        
}

mysqli_close($GLOBALS['con']);
$envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao, "opt"=>$opt);
print json_encode($envia_resposta, JSON_PRETTY_PRINT);
?>