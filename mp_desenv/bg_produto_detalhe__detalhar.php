<?php
//ini_set('display_errors','on');
//error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8');
session_start();
include("/var/www/mp_desenv/bg_conexao_bd.php");

$msg = "Nada foi executado!";
$funcao = "produto_detalhe";
$opt = "produto_detalhe__detalhar";
$erro = 0;
$titulo =  "";
$descricao_titulo =  "";
$detalhe_texto1 =  "";
$detalhe_texto2 =  "";
$imagem1 =  "";
$detalhe_preco =  0;
$parcelas =  1;

//Verifica se os campos que são obrigatórios estão preenchidos
if(isset($_GET["id"])){
	$id = addslashes($_GET["id"]);
	//echo "ID: ".$id;
}else{
	$erro = 1;
	$msg = "Nenhum ID foi recebido";
}
//Verifica se distribuidor está definido
if(isset($_SESSION['user_id_distribuidor'])){
	$id_distribuidor = addslashes($_SESSION["user_id_distribuidor"]);
	//echo "ID: ".$id;
}else{
	$erro = 1;
	$msg = "Nenhum distribuidor selecionado";
}

//Caso os campos obrigatórios estejam preenchidos pesquisa no banco de dados
    if($erro==0){
        //Exibe o detalhe de um determinado anúncio
        $sql_consulta1 = "select t1.titulo, t1.descricao_texto1, t1.descricao_texto2, t1.imagem1, t2.valor,  t1.descricao_texto3, t1.imagem2, t1.imagem3 from  tb_produto_base t1 left join tb_prod_distrib t2 on(t1.id=t2.id and t2.id_distribuidor=".$_SESSION["user_id_distribuidor"].") where t1.id = ". $id . ";";
        //echo "Essa é a consulta: " . $sql_consulta1 . "<br>";
        $consulta1 = mysqli_query($GLOBALS['con'],$sql_consulta1);        
        
        if(mysqli_num_rows($consulta1)>0){
            //Se existir um anúncio com esse ID faz as demais consultas...

            //$envia_dados = array();
            for($i=0;$i<mysqli_num_rows($consulta1);$i++){
                $dados1 = mysqli_fetch_row($consulta1);
                $msg = "Sucesso!";
                $titulo =  utf8_encode($dados1[0]);
                $detalhe_texto1 =  utf8_encode($dados1[1]);
                $detalhe_texto2 =  utf8_encode($dados1[2]);
                $imagem1 = utf8_encode($dados1[3]);
                $detalhe_preco =  utf8_encode($dados1[4]);
                $detalhe_texto3 =  utf8_encode($dados1[5]);
                $imagem2 = utf8_encode($dados1[6]);
                $imagem3 = utf8_encode($dados1[7]);                
            }			
        }else{
        $erro = 1;     
    }
}
$msg = array("titulo"=>$titulo, "detalhe_texto1"=>$detalhe_texto1, "detalhe_texto2"=>$detalhe_texto2, "imagem1" => $imagem1, "detalhe_preco" => $detalhe_preco, "detalhe_texto3"=>$detalhe_texto3, "imagem2" => $imagem2, "imagem3" => $imagem3);

mysqli_close($GLOBALS['con']);
$envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao, "opt"=>$opt);
print json_encode($envia_resposta, JSON_PRETTY_PRINT);
?>