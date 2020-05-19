<?php

include('./config.php');
require_once('./custom/dt_function.php');


http_response_code(200);

date_default_timezone_set('Asia/Bangkok');
$current_datetime = date("Y-m-d H:i:s");
$content = file_get_contents('php://input');



$events = json_decode($content, true);


foreach ($events['events'] as $event) {

    $userID = $event['source']['userId'];
    $groupID = $event['source']['groupId'];


    if ($event['type'] == 'message' && $event['message']['type'] == 'text') {

        $text = $event['message']['text'];
        $text = str_replace(' ', '', $text);
        $text = preg_replace('~[\r\n]+~', '', $text);
        $replyToken = $event['replyToken'];
        $text = iconv_substr($text, 0);
        $text_forcheck_string = $text;
        $text_forcheck_number = $text;


        //get displayname 
        $user_displayname = linedisplayname($groupID, $userID);


        // if(!isset($userID)){

        //     $messages = [
        //         'type' => 'text',
        //         'text' => 'account ของท่านไม่สามารถใช้งานได้ '.$user_displayname.' uid '.$userID.' gid '.$groupID
        //       ];

        // }else{

        //     $messages = [
        //         'type' => 'text',
        //         'text' => 'ยินดีต้อนรับ '.$user_displayname
        //       ];

        // }



        $split_slash_count = substr_count($text, "/");

        if ($split_slash_count == 0) {

            $bet_type = "single";

            $bet_string = checkbetstring($text);
            $bet_value = checkbetvalue($text);


            if (!$bet_string) {

                $messages = [
                    'type' => 'text',
                    'text' => $user_displayname . " รูปแบบการเดิมพันของท่านไม่ถูก"
                ];
            } else if (!is_numeric($bet_value)) {

                $messages = [
                    'type' => 'text',
                    'text' => $user_displayname . " ยอดเงินเดิมพันไม่ถูกต้อง"
                ];
            } else if ($bet_string == "ข้อมูล") {

                $messages = [
                    'type' => 'text',
                    'text' => $userID
                ];
            } else {

                $messages = [
                    'type' => 'text',
                    'text' => $user_displayname . ' แทง ' . $bet_string . " จำนวน " . $bet_value
                ];
            }
        } else if ($split_slash_count > 0) {

            $reponse_bet = '';
            $bet_type = "multiple";
            $arrKeywords = explode("/", $text);
            $i = 0;
            foreach ($arrKeywords as $element) {

                $i++;
                $bet_string = checkbetstring($element);
                $bet_value = checkbetvalue($element);


                if (!$bet_string) {

                    $element_reponse = '# ' . $i . ' รูปแบบการเดิมพันของท่านไม่ถูกต้อง';
                } else if (!is_numeric($bet_value)) {


                    $element_reponse = '# ' . $i . ' ยอดเงินเดิมพันไม่ถูกต้อง';
                } else {

                    $element_reponse = '# ' . $i . ' แทง > ' . $bet_string . " จำนวน " . $bet_value;
                }


                $reponse_bet = $reponse_bet . "\n" . $element_reponse;
            }


            $messages = [
                'type' => 'text',
                'text' => $user_displayname . " " . $reponse_bet
            ];
        }
    }






    $url = 'https://api.line.me/v2/bot/message/reply';
    $data = [
        'replyToken' => $replyToken,
        'messages' => [$messages],
    ];
    $post = json_encode($data);
    $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $result = curl_exec($ch);
    curl_close($ch);

    echo $result . "\r\n";
}

echo "OK";
