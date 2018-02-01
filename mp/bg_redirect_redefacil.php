<?php
    //ini_set('display_errors','on');
    //error_reporting(E_ALL);

    session_start();
	header('Content-Type: text/html; charset=utf-8');

    $funcao = "redir_redefacil";
    $erro = 0;
    $msg = "Nada foi executado!";
    $opt = "";

    require_once ('sdk-mercadopago/lib/mercadopago.php');

    if(isset($_POST['id'])){
        $sql = "SELECT email from  tb_usuarios where id=".$_POST['id'].";";
        //echo "SQL: " . $sql . "<br>";
        include("/var/www/mp/bg_conexao_bd.php");
        $query = mysqli_query($GLOBALS['con'],$sql);

        if(mysqli_num_rows($query)>0){
            $row = mysqli_fetch_row($query);
            $sql = "UPDATE tb_usuarios SET id_distribuidor=".$_POST['id']." where id=".$_SESSION['user_id'].";";
            $_SESSION['user_id_distrobuidor'] = $_POST['id'];
            //echo "SQL: " . $sql . "<br>";
            include("/var/www/mp/bg_conexao_bd.php");
            $query = mysqli_query($GLOBALS['con'],$sql);
            //echo "email_distr: " . $row[0] . "<br>";
            //$goTo = 'Location: "https://www.redefacilbrasil.com.br/web/cadastro/?p='.$row[0].'"';
            //echo "GoTo: ".$goTo;
            //header($goTo);
            $msg = $row[0];
            $dados = array("1"=>1);
            include("/var/www/mp/bg_enviaemail.php");
            $resposta = define_envia('check_list',$dados);
        }else{
            //header("Location: https://queromarita.com.br");
            $msg = 'https://queromarita.com.br';
        }
    }else{
        //header("Location: https://queromarita.com.br");
        $msg = 'https://queromarita.com.br';
    }

    $envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt);
	print json_encode($envia_resposta);
?>
