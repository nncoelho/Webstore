<?php

namespace core\classes;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class SendEmail{

    // ============================================================
    public function sendEmailCheckNewClient($email_client, $purl){

        // Constroi o PURL (link para validacao do email)
        $link = BASE_URL.'?a=confirm_email&purl='.$purl;

        // Envia email para o novo cliente para confirmaçao do email e ativacao da conta
        $mail = new PHPMailer(true);

        try {
            // Configuracoes do servidor
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
            $mail->isSMTP();
            $mail->Host       = EMAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = EMAIL_FROM;
            $mail->Password   = EMAIL_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = EMAIL_PORT;
            $mail->CharSet    = 'UTF-8';

            // Emissor e receptor
            $mail->setFrom(EMAIL_FROM, APP_NAME);
            $mail->addAddress($email_client);

            // Assunto
            $mail->isHTML(true);
            $mail->Subject = APP_NAME.' - Confirmação do email e ativação da conta de cliente';

            // Mensagem
            $html = '<p>Seja bem vindo à nossa loja '.APP_NAME.'.</p>';
            $html .= '<p>Para poder entrar na nossa loja, necessita de autenticar o seu email.</p>';
            $html .= '<p>Para efectuar a confirmação do seu email, clique no link abaixo:</p>';
            $html .= '<p><a href="'.$link.'">Confirmar email</a></p>';
            $html .= '<p><i><small>'.APP_NAME.' - '.APP_VERSION.'</small></i></p>';
            $mail->Body = $html;

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}