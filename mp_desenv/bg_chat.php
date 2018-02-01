<?php
    ini_set('display_errors','on');
	error_reporting(E_ALL);

	header('Content-Type: text/html; charset=utf-8');

    $funcao = "chat";
    $erro = 0;
    $msg = "Nada foi executado!";
    $opt = "";
    $opt2 = "";

    //Verifica se usuário está Logado
    include("/var/www/mp_desenv/bg_protege_php.php");
    $status_log = status_login($resposta); //acredite que $resposta existe, ele está no bg_protege_php
    //echo "status_log: ".$status_log."<br>";
    
    //Se estiver logado executa o código
    if(isset($_GET["fn"]) || isset($_POST["fn"])){
        if(isset($_GET["fn"])){
            $fn = addslashes($_GET["fn"]);
        }else{
            $fn = addslashes($_POST["fn"]);
        }

        switch ($fn){
            case 'carregar_chat':
                if(isset($_SESSION["user_nome_compl"]) and isset($_SESSION["user_email"])){
                    $usr_nome = $_SESSION["user_nome_compl"];
                    $usr_email = $_SESSION["user_email"];

                    $sql = "SELECT id_chat, key_chat, name, email from tb_chat_keys where id_distrib=".$_SESSION["user_id_distribuidor"].";";

                    include("/var/www/mp_desenv/bg_conexao_bd.php");
                    $query = mysqli_query($GLOBALS['con'],$sql);

                    if(mysqli_num_rows($query)>0){
                        $row = mysqli_fetch_row($query);
                        $id_chat = $row[0];
                        $key_chat = $row[1];
                        $adm_nome = $row[2];
                        $adm_email = $row[3];
                    }else{
                        $id_chat = '592e4b484374a471e7c50a78';
                        $key_chat = 'b52fb10a5ae0e868a898023c32abf5039ebef38b';
                        $adm_nome = 'MYDE';
                        $adm_email = 'contato@myde.com.br';
                    }
                    $hash = hash_hmac("sha256", $usr_email, $key_chat);
                }else{
                    $usr_nome = '';
                    $usr_email = '';
                    $id_chat = '592e4b484374a471e7c50a78';
                    $key_chat = 'b52fb10a5ae0e868a898023c32abf5039ebef38b';
                    $adm_nome = 'MYDE';
                    $adm_email = 'contato@myde.com.br';
                    $hash = hash_hmac("sha256", $usr_email, $key_chat);
                }
                $erro = 0;
                $msg = array('usr_nome'=>$usr_nome,'usr_email'=>$usr_email,'id_chat'=>$id_chat,'hash'=>$hash);
                $opt = "exibir_chat";
            break;
        }
    }else{
        $erro = 1;
        $msg = "Solictação Incorreta";
        $opt = "expulsa";
    }

    $envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt,"opt2"=>$opt2);
    print json_encode($envia_resposta, JSON_PRETTY_PRINT);
?>