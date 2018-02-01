<?php

//ini_set('display_errors','on');
//error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8');
session_start();
include("/var/www/mp/bg_conexao_bd.php");

$msg = "Nada foi executado!";
$funcao = "sacola";
$opt = "sacola__atualizar_qtde";
$msg_erro_qtde="Ops, você adicionou produtos demais no carrinho. Você pode comprar no máximo 10 produtos iguais por vez!";
$msg_sucesso="Sucesso!!!";
$erro = 0;

//Verifica se os campos que são obrigatórios estão preenchidos
if(isset($_GET["id"])){
	$id= addslashes($_GET["id"]);
}else{
	$erro = 1;
	$msg = "Nenhum ID de Produto foi recebido";
}


if(isset($_GET["qtde"])){
	   $qtde = addslashes($_GET["qtde"]);
}else{
	$erro = 1;
	$msg = "Nenhuma quantidade foi inserida";
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


//Caso os campos obrigatórios estejam preenchidos pesquisa no banco de dados
    if($erro==0){
        $sql_consulta = "UPDATE tb_carrinho SET QTDE=" . $qtde . " WHERE ID_USER = ". $user_id . " and ID_DISTRIBUIDOR = " . $user_id_distribuidor . " and ID_produto = " . $id . ";";       
        $msg = $msg_sucesso;
        mysqli_query($GLOBALS['con'],$sql_consulta);        
    }
mysqli_close($GLOBALS['con']);
$envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao, "opt"=>$opt);
print json_encode($envia_resposta, JSON_PRETTY_PRINT);
?>