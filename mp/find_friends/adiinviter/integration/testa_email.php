<?php
    ini_set('display_errors','on');
	error_reporting(E_ALL);
    //header('Content-Type: text/html; charset=utf-8');

    $txtAssunto = "QueroMarita: Instruções para restaurar seu acesso";
    $txtPara = array("filipe_caldas@msn.com","contato@myde.com.br");
    $txtMensagem = 'TESTEEEEEEEEEEEEEEEE';

    enviar_email($txtPara,$txtAssunto,$txtMensagem);

    function enviar_email($para, $assunto, $corpo){
        // Extender a classe do phpmailer para envio do email
        require "PHPMailer/PHPMailerAutoload.php";

        $assunto = '=?UTF-8?B?'.base64_encode($assunto).'?=';

        // Definir Usuário e Senha do Gmail de onde partirá os emails
        $de = 'contato@myde.com.br';
        $nomeRemetente = 'QueroMarita (MYDE)';
        define('GUSER', 'contato@myde.com.br');
        define('GPWD', 'MAILqsenha123');

        global $error;
        $mail = new PHPMailer();
        // Montando o Email
        $mail->IsSMTP();            // Ativar SMTP
        $mail->SMTPDebug = 0;        // Debugar: 1 = erros e mensagens, 2 = mensagens apenas
        $mail->SMTPAuth = true;        // Autenticação ativada
        $mail->SMTPSecure = 'tls';    // TLS REQUERIDO pelo GMail
        //$mail->SMTPSecure = 'ssl';    // TLS REQUERIDO pelo GMail
        $mail->Host = 'smtp.gmail.com';    // SMTP utilizado
        $mail->Port = 587;             // A porta 587 deverá estar aberta em seu servidor
        //$mail->Port = 465;            // A porta 587 deverá estar aberta em seu servidor
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

        // Função Responsável por Enviar o Email
        if(!$mail->Send()){
            //$error = "<font color='red'><b>Mail error: </b></font>".$mail->ErrorInfo;
            return false;
        }else{
            //$error = "<font color='blue'><b>Mensagem enviada com Sucesso!</b></font>";
            return true;
        }
    }

    //$msg = utf8_encode($msg);
	//print json_encode($envia_resposta);
?>
