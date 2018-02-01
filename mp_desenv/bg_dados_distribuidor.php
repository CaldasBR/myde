<?php
//ini_set('display_errors','on');
//error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8');
session_start();
include("/var/www/mp_desenv/bg_conexao_bd.php");

$msg = "Nada foi executado!";
$funcao = "dados_distribuidor";
$opt = "detalhar__dados_distribuidor";
$erro = 0;
 include("/var/www/mp_desenv/bg_protege_php.php");
  
    $status_log = status_login($resposta); //acredite que $resposta existe, ele está no bg_protege_php
    //echo "status_log: ".$status_log."<br>";
    
    //Se estiver logado executa o código
    if($status_log=="mantem"){
        //Verifica se distribuidor está definido
        if(isset($_SESSION['user_id_distribuidor'])){
            $id_distribuidor = addslashes($_SESSION["user_id_distribuidor"]);
            //echo "ID: ".$id;
        }else{
            $erro = 1;
            $msg = "Nenhum distribuidor selecionado";
        }
        //Exibe o detalhe de um determinado anúncio
        $sql_consulta1 = 
            "select
                nome_completo
                , email
                , cel
                , pg_facebook
                , IMAGEM
                , txt_apres
            from 
                tb_usuarios 
            where 
                ID=".$id_distribuidor.";";
        //echo "Essa é a consulta: " . $sql_consulta1 . "<br>";
        $consulta1 = mysqli_query($GLOBALS['con'],$sql_consulta1);        
        
        if(mysqli_num_rows($consulta1)>0){
            //Se existir um anúncio com esse ID faz as demais consultas...

            //$envia_dados = array();
            $msg="";
            for($i=0;$i<mysqli_num_rows($consulta1);$i++)
            {
                $dados1 = mysqli_fetch_row($consulta1);
                $msg = array("nome"=> $dados1[0], "email"=> $dados1[1], "cel"=> $dados1[2], "face"=> $dados1[3], "imagem"=> $dados1[4], "txt_apres"=>$dados1[5]);
            }
        }
        
    }else{
        $erro = 1;
        $opt = "popup";
    }
    mysqli_close($GLOBALS['con']);
    $envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao, "opt"=>$opt);
    print json_encode($envia_resposta, JSON_PRETTY_PRINT);
?>