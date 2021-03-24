<?php

namespace core\classes;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class SendEmail{

    // ============================================================
    public function sendEmailCheckNewClient($email_client, $purl){

        // Constroi o PURL (link para validacao do email)
        $link = BASE_URL . '?a=confirm_email&purl=' . $purl;

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
            $mail->Subject = APP_NAME . ' - Confirmação do email e ativação da conta de cliente';

            // Mensagem
            $html = '<p>Seja bem vindo à nossa loja ' . APP_NAME . '.</p>';
            $html .= '<p>Para poder entrar na nossa loja, necessita de autenticar o seu email.</p>';
            $html .= '<p>Para efectuar a confirmação do seu email, clique no link abaixo:</p>';
            $html .= '<p><a href="' . $link . '">Confirmar email</a></p>';
            $html .= '<p><i><small>' . APP_NAME . ' - ' . APP_VERSION . '</small></i></p>';
            $mail->Body = $html;

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    // ============================================================
    public function sendEmailCheckingOrder($email_client, $dados_encomenda){

        // Envia email para o novo cliente para confirmaçao da encomenda
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
            $mail->Subject = APP_NAME . ' - Confirmação da sua encomenda - ' . $dados_encomenda['dados_pagamento']['order_code'];

            // Mensagem
            $html = '<p>Este e-mail serve para confirmar a sua encomenda.</p>';
            $html .= '<h3>Dados da encomenda: </h3>';

            // Lista dos produtos do e-mail
            $html .= '<ul>';
            foreach ($dados_encomenda['lista_produtos'] as $produto) {
                $html .= '<li>' . $produto . '</li>';
            }
            $html .= '</ul>';

            // Total da encomenda do e-mail
            $html .= '<p>Total: <strong>' . $dados_encomenda['total'] . '</strong></p>';

            // Dados de pagamento
            $html .= '<hr>';
            $html .= '<h3>Dados de pagamento: </h3>';
            $html .= '<p>Número da conta: <strong>' . $dados_encomenda['dados_pagamento']['numero_da_conta'] . ' </strong></p>';
            $html .= '<p>Código da encomenda: <strong>' . $dados_encomenda['dados_pagamento']['order_code'] . ' </strong></p>';
            $html .= '<p>Valor a pagar: <strong>' . $dados_encomenda['dados_pagamento']['total'] . ' </strong></p>';
            $html .= '<hr>';

            $html .= '<p>Nota: A sua encomenda só será processada após pagamento.</p>';
            $html .= '<p>Muito obrigado pela sua preferência.</p>';
            $mail->Body = $html;

            $mail->send();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
