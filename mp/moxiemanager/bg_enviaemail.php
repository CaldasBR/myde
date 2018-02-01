<?php
    ini_set('display_errors','on');
	error_reporting(E_ALL);

    header('Content-Type: text/html; charset=utf-8');
	include("/var/www/mp/bg_conexao_bd.php");
    include("/var/www/mp/bg_funcoes_genericas.php");

	//  Configurações do Script
    $funcao = "estoque";
	$erro = 0;
	$msg = "Nada foi executado!";
	$opt = "";
    $opt2 = "";

    //Verifica se usuário está Logado
    include("/var/www/mp/bg_protege_php.php");
    $status_log = status_login($resposta);

    if(isset($_GET["fn"]) || isset($_POST["fn"])){
        if(isset($_GET["fn"])){
            $fn = addslashes($_GET["fn"]);
        }else{
            $fn = addslashes($_POST["fn"]);
        }

        switch ($fn){
            case 'esqueci':
                if(isset($_GET["email"])){
                    $email = addslashes($_GET["email"]);
                    //$email = 'filipe_caldas@msn.com';
                    $sql = "SELECT id,nome, email, token from tb_usuarios where email='".$email."';";
                    include("/var/www/mp/bg_conexao_bd.php");
                    $query = mysqli_query($GLOBALS['con'],$sql);
                    if(mysqli_num_rows($query)>0){
                        $row = mysqli_fetch_row($query);
                        $txtAssunto = "Instruções para restaurar seu acesso";
                        $txtPara = array($email);
                        $txtMensagem =
                            '<head>
                                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
                            </head>
                            <h1 id="mcetoc_1bgs8lahu0" style="text-align: left;"><img src="http://queromarita.com.br/media/imagens/Marita_logo-1-100x100.png" alt="Logo_Marita" width="100" height="100" /> &nbsp;<strong><span style="color: #3d1e04;">Quero Marita</span></strong></h1>
                            <p>&nbsp;</p>
                            <p><span style="color: #575757;"><strong>Este email foi enviado para voc&ecirc; atrav&eacute;s do nosso site para dar instru&ccedil;&otilde;es de recupera&ccedil;&atilde;o do seu acesso.</strong></span></p>
                            <p><strong><span style="color: #3d1e04;"><span style="color: #575757;">Caso voc&ecirc; n&atilde;o tenha solicitado esse procedimento clique</span> <span style="color: #0000ff;"><a style="color: #0000ff;" href="http://queromarita.com.br/esqueci.php?fn=naofui&amp;id='.$row[0].'">aqui</a></span>.</span></strong></p>
                            <p><span style="color: #575757;"><strong>Para cadastrar uma nova senha, acesse o link abaixo:</strong></span></p>
                            <p><span style="color: #0000ff; background-color: #d5f7f4;"><a style="color: #0000ff; background-color: #d5f7f4;" href="http://queromarita.com.br/esqueci.php?fn=reset&amp;id='.$row[0].'&amp;token='.md5($row[3]).'"><strong>RESTAURAR ACESSO</strong></a></span></p>
                            <p><strong><span style="color: #575757;">Estamos sempre dispon&iacute;veis para atender voc&ecirc; atrav&eacute;s do email <span style="color: #0000ff;"><a style="color: #0000ff;" href="mailto:contato@myde.com.br">contato@myde.com.br</a></span>, ou atrav&eacute;s do nosso chat em nossa</span><span style="color: #575757;"><span style="color: #0000ff;"> <a style="color: #0000ff;" href="https://queromarita.com.br/">p&aacute;gina inicial.</a></span></span></strong></p>
                            <p>&nbsp;</p>
                            <p><span style="color: #575757;"><strong>Ateciosamente,</strong></span></p>
                            <p><span style="color: #575757;"><strong>Equipe QueroMarita</strong></span></p>
                            <p>&nbsp;</p>
                            <p>&nbsp;</p>
                            <p><span style="color: #999999;"><strong>Propriedade exclusiva da empresa&nbsp;</strong></span><strong><span style="color: #999999;">&nbsp; </span><br /></strong></p>
                            <p><strong><img src="http://queromarita.com.br/media/imagens/myde_texto.png" alt="Myde" width="104" height="33" /></strong></p>
                            <p>&nbsp;</p>';

                        $envio = enviar_email($txtPara, $txtAssunto, $txtMensagem);
                        if($envio==true){
                            $erro = 0;
                            $msg = "Você receberá um email com as instruções.";
                            $opt = "Toast";
                        }else{
                            $erro = 1;
                            $msg = "Erro no envio do email.";
                            $opt = "Toast";
                        }
                    }else{
                        $erro = 1;
                        $msg = "Este email não está cadastrado em nosso site.";
                        $opt = "Toast";
                    }
                //}else{
                //    $erro = 1;
                //    $msg = "Solicitação incorreta.";
                //    $opt = "Toast";
                //}

            break;
        }
    }else{
        $erro = 1;
        $msg = "Informar dados completos.";
    }


    function enviar_email($para, $assunto, $corpo){
        /* Extender a classe do phpmailer para envio do email*/
        require_once("PHPMailer/class.phpmailer.php");
        
        $assunto = '=?UTF-8?B?'.base64_encode($assunto).'?=';

        /* Definir Usuário e Senha do Gmail de onde partirá os emails*/
        $de = 'contato@myde.com.br';
        $nomeRemetente = 'QueroMarita (MYDE)';
        define('GUSER', 'contato@myde.com.br'); 
        define('GPWD', 'MAILqsenha123');

        global $error;
        $mail = new PHPMailer();
        /* Montando o Email*/
        $mail->IsSMTP();            /* Ativar SMTP*/
        $mail->SMTPDebug = 0;        /* Debugar: 1 = erros e mensagens, 2 = mensagens apenas*/
        $mail->SMTPAuth = true;        /* Autenticação ativada    */
        $mail->SMTPSecure = 'tls';    /* TLS REQUERIDO pelo GMail*/
        //$mail->SMTPSecure = 'ssl';    /* TLS REQUERIDO pelo GMail*/
        $mail->Host = 'smtp.gmail.com';    /* SMTP utilizado*/
        $mail->Port = 587;             /* A porta 587 deverá estar aberta em seu servidor*/
        //$mail->Port = 465;            /* A porta 587 deverá estar aberta em seu servidor*/
        $mail->Username = GUSER;
        $mail->Password = GPWD;
        $mail->SetFrom($de, $nomeRemetente);
        //$mail->AddReplyTo($de);
        $mail->Subject = $assunto;
        $mail->Body = $corpo;
        foreach ($para as $destinatario){
            $mail->AddAddress($destinatario);
        }
        $mail->IsHTML(true);

        /* Função Responsável por Enviar o Email*/
        if(!$mail->Send()){
            //$error = "<font color='red'><b>Mail error: </b></font>".$mail->ErrorInfo; 
            return false;
        }else{
            //$error = "<font color='blue'><b>Mensagem enviada com Sucesso!</b></font>";
            return true;
        }
    }

    //$msg = utf8_encode($msg);
	$envia_resposta = array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt,"opt2"=>$opt2);
	print json_encode($envia_resposta);
?>