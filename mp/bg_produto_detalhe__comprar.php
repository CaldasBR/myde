<?php

//ini_set('display_errors','on');
//error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8');
session_start();
include("/var/www/mp/bg_conexao_bd.php");

$msg = "Nada foi executado!";
$funcao = "produto_detalhe";
$opt = "produto_detalhe__comprar";
$msg_erro_qtde="Ops, você adicionou produtos demais no carrinho. Você pode comprar no máximo 10 produtos iguais por vez!";
$msg_sucesso="Sucesso!!!";
$erro = 0;

//Verifica se os campos que são obrigatórios estão preenchidos
if(isset($_GET["id"])){
	$ID= addslashes($_GET["id"]);
}else{
	$erro = 1;
	$msg = "Nenhum ID de Produto foi recebido";
}


if(isset($_GET["qtde"])){
	   $QTDE = addslashes($_GET["qtde"]);
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
        $sql="select sum(qtde) from tb_carrinho where ID_USER = ". $user_id . " and ID_PRODUTO = " . $ID . ";";
        $consulta_sql = mysqli_query($GLOBALS['con'],$sql);
        if(mysqli_num_rows($consulta_sql)>0)
        {
            $dados_sql = mysqli_fetch_row($consulta_sql);
            if($dados_sql[0]==0)
            {
                if($total>10)
                {
                    $erro = 1;
                    $msg = $msg_erro_qtde;
                }
                else
                {
                    $sql_consulta = "insert into tb_carrinho (ID_USER, ID_DISTRIBUIDOR, ID_STATUS, ID_PRODUTO, QTDE) value ('". $user_id . "', '" . $user_id_distribuidor . "', 1, '" . $ID . "', '" . $QTDE . "');"; 
                    $msg = $msg_sucesso;
                }
            }
            else
            {
                $total=$QTDE + $dados_sql[0];
                if($total>10)
                {
                    $erro = 1;
                    $msg = $msg_erro_qtde;
                }
                else
                {
                    $sql_consulta = "UPDATE tb_carrinho SET QTDE=" . $total . " WHERE ID_USER = ". $user_id . " and ID_DISTRIBUIDOR = " . $user_id_distribuidor . ";";       
                    $msg = $msg_sucesso;
                }
            }
        }
        else
        {
            if($total>10)
            {
                $erro = 1;
                $msg = $msg_erro_qtde;
            }
            else
            {
                $sql_consulta = "insert into tb_carrinho (ID_USER, ID_DISTRIBUIDOR, ID_STATUS, ID_PRODUTO, QTDE) value ('". $user_id . "', '" . $user_id_distribuidor . "', 1, '" . $ID . "', '" . $QTDE . "');"; 
                $msg = $msg_sucesso;
            }
        }
        mysqli_query($GLOBALS['con'],$sql_consulta);        
    }
mysqli_close($GLOBALS['con']);
$envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao, "opt"=>$opt);
print json_encode($envia_resposta, JSON_PRETTY_PRINT);
?>