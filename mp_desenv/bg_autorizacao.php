<?php
    //ini_set('display_errors','on');
	//error_reporting(E_ALL);

    session_start();
	header('Content-Type: text/html; charset=utf-8');
	include("/var/www/mp_desenv/bg_conexao_bd.php");

    $funcao = "busca_user_id";
    $erro = 0;
    $msg = "Nada foi executado!";
    $opt = "";

    //Verifica se usuário está Logado
    include("/var/www/mp_desenv/bg_protege_php.php");
    $status_log = status_login($resposta); //acredite que $resposta existe, ele está no bg_protege_php
    //echo "status_log: ".$status_log."<br>";
    
    //Se estiver logado executa o código
    if($status_log=="mantem"){
        if(isset($_POST["fn"])){
            $fn = addslashes($_POST["fn"]);
            $funcao = $fn;
            switch ($fn){
                case 'busca_user_id':
                    $id = $_SESSION['user_id'];                        
                    $erro = 0;
                    $msg = $id;
                    $opt = "achei_id";
                    break;
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

    $envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt);
    //echo "conteudo envia_resposta:<br>";
    //var_dump($envia_resposta);
    print json_encode($envia_resposta, JSON_PRETTY_PRINT);
?>