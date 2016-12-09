<?php
require_once "library/SendMailSmtpClass.php";

function http_request_mailru($url)
{
    $result = FALSE;
    $header = array('Content-Type: application/json',
        'Cache-Control: no-cache');

    if (!empty($url)) {
        if (function_exists('curl_init')) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($curl, CURLOPT_FRESH_CONNECT, TRUE);
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.99 Safari/537.36');
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
            $result = curl_exec($curl);
            $info = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if (curl_errno($curl)) {
                print curl_error($curl) . '<br>';
            }
            curl_close($curl);
            if (($result <> null) and ($info = 200)) {
                //echo('<br>Запрос успешно выполен<br>');
                return $result;
            } else {
                echo('<br>Запрос не выполен!!!<br>');
                return $result;
            }
        }
    }
}

function MailSmtp($content)
{

    include_once "cfg/mail.cfg.php";


    $mailSMTP = new SendMailSmtpClass($smtp_user, $smtp_password, $smtp_server, 'ParserMailRuAnswers', $smtp_port);


    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=utf-8\r\n";
    $headers .= "From: ParserMailRuAnswers\r\n";
    $result = $mailSMTP->send($reciever, $subject, $content, $headers);

    if ($result === true) {
        echo "Письмо успешно отправлено";
    } else {
        echo "Письмо не отправлено. Ошибка: " . $result;
    }
}


