<?php

include "library/library.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);
//header('Content-type: text/html; charset=utf-8');
mb_internal_encoding("UTF-8");

$delay = 0; // Задержка между прыжками
$max_jumping = 500; //Максимальное количество прыжков

$last_url = '';
$last_id_from_file = 0;
$last_id = 9999999999;
$count = 0;
$msg_body = '';
$lastid_content = file_get_contents('cfg/lastid.txt');
$words_for_seach = explode(',', file_get_contents('cfg/words_for_search.txt'));


if ($lastid_content != null) $last_id_from_file = $lastid_content;

while ($last_id > $last_id_from_file) {
    $url = "https://otvet.mail.ru/api/v2/questlist" . $last_url;
    echo 'last_id=' . $last_id . '<br>';;
    //echo 'last_id_from_file=' . $last_id_from_file . '<br>';
    echo $url . '<br>';
    $http = http_request_mailru($url);
    $json = json_decode($http);

    $count++;
    if ($count == $max_jumping) {
        echo '<br> Превышено максимальное количество прыжков <br>';
        break;
    }

    if ($last_id == 9999999999) $last_id = $json->{'qst'}[0]->{'id'};

    foreach ($json->{'qst'} as $answers) {
        $question = $answers->{'qtext'};
        if ($question != null) {
            foreach ($words_for_seach as $line) {
                if ((mb_strpos(mb_strtoupper($question), mb_strtoupper($line), 0, 'UTF-8') == true)
                    && mb_strpos(mb_strtoupper($msg_body), mb_strtoupper($question), 0, 'UTF-8') != true
                ) {
                    $msg_body = $msg_body . '<br />';
                    $msg_body = $msg_body . 'Вопрос: ' . $question . '<br />';
                    $msg_body = $msg_body . 'Категория: ' . $answers->{'catname'} . '<br />';
                    $msg_body = $msg_body . 'Link: https://otvet.mail.ru/question/' . $answers->{'id'} . '<br />';
                    $msg_body = $msg_body . '<br />';

                    /*echo '<br />';
                    echo 'Вопрос: ' . $answers->{'qtext'} . '<br />';
                    echo 'Категория: ' . $answers->{'catname'} . '<br />';
                    echo 'Link: https://otvet.mail.ru/question/' . $answers->{'id'} . '<br />';*/
                }
            }
        }
    }
    if ($count == 1) {
        file_put_contents('cfg/lastid.txt', $last_id);
        $last_id_ = $last_id;
    }
    $last_id = intval($last_id) - 110;
    //echo 'test2=' . $last_id . '<br>';
    $last_url = '?lastid=' . (intval($last_id_)) . 'ajax_id=' . $count . '&n=100&state=A&p=' . intval(100) * $count;
    if ($delay > 0) sleep($delay);
}

if ($msg_body <> '') {
    echo '<br>----------------------------------RESULT---------------------------------- <br>';
    echo $msg_body;
    MailSmtp($msg_body);
}
