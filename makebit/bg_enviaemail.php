<?php
    ini_set('display_errors','on');
	error_reporting(E_ALL);
    //header('Content-Type: text/html; charset=utf-8');

    function define_envia($fn,$cotacao,$stop){
        switch ($fn){
            case 'stop_loss':
                $txtAssunto = "[MakeBit] ALERTA: Stop LOSS de R$ ".$stop." foi alcançado, cotação atual em R$ ".$cotacao;
                $txtMensagem =
                    '<head>
                        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
                    </head>
                    <body>
                    </body>';

                $envio = enviar_email(['fcaldas@gmail.com', 'fabiodonizettisantos@gmail.com' , 'l.willian16@gmail.com',' lecaodf@hotmail.com','fcapeleto@gmail.com','william.wakizaka@gmail.com'], $txtAssunto, $txtMensagem);
                if($envio==true){
                    $erro = 0;
                    $msg = "Email enviado.";
                    $opt = "Toast";
                }else{
                    $erro = 1;
                    $msg = "Erro no envio do email.";
                    $opt = "Toast";
                }

                $funcao = $fn;
                $envia_resposta = json_encode(array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt));
                return $envia_resposta;
            break;
            case 'stop_gain':
                $txtAssunto = "[MakeBit] ALERTA: Stop GAIN de R$ ".$stop." foi alcançado, cotação atual em R$ ".$cotacao;
                $txtMensagem =
                    '<head>
                        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
                    </head>
                    <body>
                    </body>';

                $envio = enviar_email(['fcaldas@gmail.com', 'fabiodonizettisantos@gmail.com' , 'l.willian16@gmail.com',' lecaodf@hotmail.com', 'fcapeleto@gmail.com','william.wakizaka@gmail.com'], $txtAssunto, $txtMensagem);
                if($envio==true){
                    $erro = 0;
                    $msg = "Email enviado.";
                    $opt = "Toast";
                }else{
                    $erro = 1;
                    $msg = "Erro no envio do email.";
                    $opt = "Toast";
                }

                $funcao = $fn;
                $envia_resposta = json_encode(array("erro"=>$erro,"msg"=>$msg,"funcao"=>$funcao,"opt"=>$opt));
                return $envia_resposta;
            break;
        }
    }

    function enviar_email($para, $assunto, $corpo){
        // Extender a classe do phpmailer para envio do email
        require_once("PHPMailer/class.phpmailer.php");

        $assunto = '=?UTF-8?B?'.base64_encode($assunto).'?=';

        // Definir Usuário e Senha do Gmail de onde partirá os emails
        $de = 'contato@myde.com.br';
        $nomeRemetente = 'MakeBit (MYDE)';
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
        //$mail->AddBCC("contato@myde.com.br", "Controle MYDE");
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
