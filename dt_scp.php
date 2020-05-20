<?php
$curlurl ="http://e-sport.in.th/ssdev/dt/dashboard/auth"; // url เพื่อเรียก ไปที่ curl_service ที่อยู่ใน server ที่สามารรถติดต่อดาต้าเบสได้

function reg_login($username,$password)
{
global $curlurl;
$params = "username=admin&password=admin";



$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_POST,1); // method ที่เราจะส่ง เป็น get หรือ post
curl_setopt($ch, CURLOPT_POSTFIELDS,$params); // paremeter สำหรับส่งไปยังไฟล์ ที่กำหนด
curl_setopt($ch, CURLOPT_URL,$curlurl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

$result = curl_exec($ch); // ผลการ execute กลับมาเป็น ข้อมูลใน url ที่เรา ส่งคำร้องขอไป
curl_close ($ch);
return $result;
}


include('./config.php');
require_once('./custom/dt_function.php');


http_response_code(200);

date_default_timezone_set('Asia/Bangkok');
$current_datetime = date("Y-m-d H:i:s");
$content = file_get_contents('php://input');



$events = json_decode($content, true);


foreach ($events['events'] as $event) {

    $userID = $event['source']['userId'];
    $line_id = $event['source']['userId'];
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

            if ($bet_string == "ข้อมูล") {
                $messages = [
                    'type' => 'text',
                    'text' => "UserID : " . $userID . "\r\n" . "GroupID : " . $groupID
                ];
            } else if ($bet_string == "คงเหลือ") {
                $messages = [
                    'type' => 'text',
                    'text' => "Username : " . $user_displayname . "\r\n" . "UserID : " . $userID . "\r\n" . "ยอดเงินคงเหลือ : "
                ];
            } else if ($bet_string == "ยกเลิก") {
                $messages = [
                    'type' => 'text',
                    'text' => "Username : " . $user_displayname . "\r\n" . "ยกเลิกการเดิมพันทั้งหมด"
                ];
            } else if ($bet_string == "สมัคร") {
                $data = array(
                    "user_displayname" => $user_displayname,
                    "fullname" => $user_displayname,
                    "user_lineid" => $userID,
                );
                $data_string = json_encode($data);

                $ch = curl_init('http://e-sport.in.th/ssdev/dt/dashboard/api/user_test/register');

                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

                $result = curl_exec($ch);
                curl_close($ch);
                $dataresult = json_decode($result,true);
                
                $messages = [
                    'type' => 'text',
                    'text' => "Username : " . $result . "\r\n" . $data_string . $dataresult
                ];
            } else {
                if (!$bet_string) {

                    $messages = [
                        'type' => 'text',
                        'text' => $user_displayname . " รูปแบบการเดิมพันของท่านไม่ถูกต้อง"
                    ];
                } else if (!is_numeric($bet_value)) {

                    $messages = [
                        'type' => 'text',
                        'text' => $user_displayname . " ยอดเงินเดิมพันไม่ถูกต้อง"
                    ];
                } else {

                    $messages = [
                        'type' => 'text',
                        'text' => $user_displayname . ' แทง ' . $bet_string . " จำนวน " . $bet_value
                    ];
                }
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

                echo $bet_string;
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

    // echo $result . "\r\n";
}

// echo "OK";
