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
    //include("/var/www/mp/bg_protege_php.php");
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
            case "contato":
                if(isset($_POST["nome"])&&isset($_POST["email"])&&isset($_POST["fone"])&&isset($_POST["nome"])&&isset($_POST["texto"])){
                    
                    $nome = addslashes($_POST["nome"]);
                    $email = addslashes($_POST["email"]);
                    $fone = addslashes($_POST["fone"]);
                    $texto = addslashes($_POST["texto"]);
                    $autorizo = addslashes($_POST["autorizo"]);
                    
                    $dados = array("nome"=>$nome,"email"=>$email,"fone"=>$fone,"texto"=>$texto,"autorizo"=>$autorizo);
                    
                    include("/var/www/mp/bg_enviaemail.php");
                    $resposta = define_envia('contato',$dados);
                    $resposta = json_decode($resposta);
                    //var_dump($resposta);
                    $erro = $resposta -> erro;
                    $msg = $resposta -> msg;
                    $opt = $resposta -> opt;
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