<?php
// $url = "http://e-sport.in.th/ssdev/dt/dashboard/auth";


// function reg_login($username, $password)
// {
//     global $url;
//     $data = "username=admin&password=admin";

//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
//     curl_setopt($ch, CURLOPT_HEADER, 0);
//     curl_setopt($ch, CURLOPT_POST, 1);
//     curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//     curl_setopt($ch, CURLOPT_URL, $url);
//     // curl_setopt($ch, CURLOPT_COOKIEJAR, "file.txt");
//     // curl_setopt($ch, CURLOPT_COOKIEFILE, "file.txt");
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

//     $result = curl_exec($ch);
//     curl_close($ch);
//     return $result;
// }




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
    $text = $event['message']['text'];
    $text = str_replace(' ', '', $text);
    $text = preg_replace('~[\r\n]+~', '', $text);
    $replyToken = $event['replyToken'];
    $text = iconv_substr($text, 0);
    $text_forcheck_string = $text;
    $text_forcheck_number = $text;
    $user_displayname = linedisplayname($groupID, $userID);

    if ($event['type'] == 'follow') {
        $messages = [
            'type' => 'text',
            'text' => "🧐 เริ่มการเดิมพันพิมพ์ : play " . "\r\n" . "💰 เช็กยอดคงเหลือพิมพ์ : id " . "\r\n" . "🤩 วิธีการเดิมพันพิมพ์ : step " . "\r\n" . "⛔️ ยกเลิกการเดิมพันพิมพ์ : x " . "\r\n" . "📑 ประวัติการเดิมพันพิมพ์ : c "
        ];
    }
    if ($event['type'] == 'memberJoined') {
        $messages = [
            'type' => 'text',
            'text' => "ยินดีต้อนรับ : " . $user_displayname . "\r\n" . "🧐 เริ่มการเดิมพันพิมพ์ : play " . "\r\n" . "💰 เช็กยอดคงเหลือพิมพ์ : id " . "\r\n" . "🤩 วิธีการเดิมพันพิมพ์ : step " . "\r\n" . "⛔️ ยกเลิกการเดิมพันพิมพ์ : x " . "\r\n" . "📑 ประวัติการเดิมพันพิมพ์ : c "
        ];
    }
    if ($event['type'] == 'join') {
        $messages = [
            'type' => 'text',
            'text' => "🧐 เริ่มการเดิมพันพิมพ์ : play " . "\r\n" . "💰 เช็กยอดคงเหลือพิมพ์ : id " . "\r\n" . "🤩 วิธีการเดิมพันพิมพ์ : step " . "\r\n" . "⛔️ ยกเลิกการเดิมพันพิมพ์ : x " . "\r\n" . "📑 ประวัติการเดิมพันพิมพ์ : c "
        ];
    }
    if ($event['type'] == 'message' && $event['message']['type'] == 'text') {

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
                $ch = curl_init('http://e-sport.in.th/ssdev/dt/dashboard/api/user_test/profile/' . $userID);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',));
                $result = curl_exec($ch);
                curl_close($ch);
                $resultData = json_decode($result, true);
                $data = $resultData['data'];
                $line_id = $data['user_lineid'];
                $credit = $data['credit'];

                if ($line_id == $userID) {
                    $messages = [
                        'type' => 'text',
                        'text' => "ชื่อผู้ใช้งาน : " . $user_displayname . "\r\n" . "UserID : " . $userID . "\r\n" . "💰ยอดเงินคงเหลือ : " . $credit . " บาท"
                    ];
                } else {
                    $messages = [
                        'type' => 'text',
                        'text' => "ชื่อผู้ใช้งาน : " . $user_displayname . "\r\n" . "🥺 ท่านยังไม่ได้ทำการสมัครสมาชิก" . "\r\n" . "📝 สมัครสมาชิกพิมพ์ : play",
                        "quickReply" => [
                            "items" => [
                                [
                                    "type" => "action",
                                    "action" => [
                                        "type" => "message",
                                        "label" => "👉 สมัครสมาชิก",
                                        "text" => "play"
                                    ]
                                ]
                            ]
                        ]
                    ];
                }
            } else if ($bet_string == "ยกเลิก") {
                $messages = [
                    'type' => 'text',
                    'text' => "ชื่อผู้ใช้งาน : " . $user_displayname . "\r\n" . " ❌ ยกเลิกการเดิมพันทั้งหมด ❌"
                ];
            } else if ($bet_string == "การเล่น") {
                $messages = [
                    'type' => 'text',
                    'text' => "\" วิธีการเดิมพัน \"" . "\r\n" . "พิมพ์ : ส = เสือ" . "\r\n" . "พิมพ์ : ม = มังกร" . "\r\n" . "พิมพ์ : ค = คู่" . "\r\n" . "พิมพ์ : สม = เสมอ" . "\r\n" . "พิมพ์ : สคู่ = เสือเลขคู่" . "\r\n" . "พิมพ์ : สคี่ = เสือเลขคี่" . "\r\n" . "พิมพ์ : มคู่ = มังกรเลขคู่" . "\r\n" . "พิมพ์ : มคี่ = มังกรเลขคู่" . "\r\n" . "พิมพ์ : สดำ = เสือดำ" . "\r\n" . "พิมพ์ : สแดง = เสือแดง" . "\r\n" . "พิมพ์ : มดำ = มังกรดำ" . "\r\n" . "พิมพ์ : มแดง = มังกรแดง" . "\r\n" . "\r\n" . "\" รูปแบบการเดิมพัน \"" . "\r\n" . "พิมพ์ : ส1000" . "\r\n" . "เท่ากับ : แทงเสือ1000บาท" . "\r\n" . "\r\n" . "\" การเดิมพันแบบซ้อนทับ \"" . "\r\n" . "พิมพ์ : ส1000/ม5000/สดำ1000" . "\r\n" . "เท่ากับ : แทงเสือ1000บาท แทงมังกร5000บาท แทงเสือดำ1000บาท"
                ];
            } else if ($bet_string == "สมัคร") {
                $ch = curl_init('http://e-sport.in.th/ssdev/dt/dashboard/api/user_test/profile/' . $userID);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',));
                $result = curl_exec($ch);
                curl_close($ch);
                $resultData = json_decode($result, true);
                $data = $resultData['data'];
                $line_id = $data['user_lineid'];
                if ($line_id == $userID) {
                    $messages = [
                        'type' => 'text',
                        'text' => "ชื่อผู้ใช้งาน : " . $user_displayname . "\r\n" . "😇 ชื่อผู้ใช้นี้เป็นสมาชิกอยู่แล้ว"
                    ];
                } else {
                    $data = array(
                        "user_displayname" => $user_displayname,
                        "fullname" => $user_displayname,
                        "user_lineid" => $userID,
                    );

                    $data_register = json_encode($data);

                    $ch = curl_init('http://e-sport.in.th/ssdev/dt/dashboard/api/user_test/register');

                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_register);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

                    $result = curl_exec($ch);
                    curl_close($ch);

                    $messages = [
                        'type' => 'text',
                        'text' => "ชื่อผู้ใช้งาน : " . $user_displayname . "\r\n" . "✅ ทำการลงทะเบียนสำเร็จ ✅" . "\r\n"
                    ];
                }
            } else {
                if (!$bet_string) {
                    $messages = [
                        'type' => 'text',
                        'text' => "ชื่อผู้ใช้งาน : " . $user_displayname . "\r\n" . "⛔️ รูปแบบการเดิมพันไม่ถูกต้อง",
                        "quickReply" => [
                            "items" => [
                                [
                                    "type" => "action",
                                    "action" => [
                                        "type" => "message",
                                        "label" => "👉 ดูคู่มือการเดิมพัน",
                                        "text" => "step"
                                    ]
                                ]
                            ]
                        ]
                    ];
                } else if (!is_numeric($bet_value)) {

                    $messages = [
                        'type' => 'text',
                        'text' => "ชื่อผู้ใช้งาน : " . $user_displayname . "\r\n" . "⛔️ ยอดเงินเดิมพันไม่ถูกต้อง",
                        "quickReply" => [
                            "items" => [
                                [
                                    "type" => "action",
                                    "action" => [
                                        "type" => "message",
                                        "label" => "👉 ดูคู่มือการเดิมพัน",
                                        "text" => "step"
                                    ]
                                ]
                            ]
                        ]
                    ];
                } else {
                    $ch = curl_init('http://e-sport.in.th/ssdev/dt/dashboard/api/user_test/profile/' . $userID);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',));
                    $result = curl_exec($ch);
                    curl_close($ch);
                    $resultData = json_decode($result, true);
                    $data = $resultData['data'];
                    $user_id = $data['id'];

                    $_POST['user_id'] = "4";
                    $_POST['user_lineid'] = "test1234";
                    $_POST['user_displayname'] = "tarn";
                    $_POST['bet_text'] = "test";
                    $_POST['value'] = "50.00";
                    $_POST['bet_code'] = "null";

                    $data = array(
                        "user_id" => $_POST['user_id'],
                        "user_lineid" => $_POST['user_lineid'],
                        "user_displayname" => $_POST['user_displayname'],
                        "bet_text" => $_POST['bet_text'],
                        "value" => $_POST['value'],
                        "bet_code" => $_POST['bet_code']
                    );
                    // $string = http_build_query($data);

                    $url = "http://e-sport.in.th/ssdev/dt/dashboard/api/bet_test/logbet_create";

                    $postData = "";

                    foreach ($data as $key => $val) {
                        $postData .= $key . "=" . $val . "&";
                    }

                    $postData = rtrim($postData, "&");
                    // $postData = json_decode($data,true);

                    
                    // Get cURL resource
                    $curl = curl_init();

                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_POST, 1);
                    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);

                    // Send the request & save response to $resp
                    $resp = curl_exec($curl);

                    print_r($resp);

                    // Close request to clear up some resources
                    curl_close($curl);

                    $messages = [
                        'type' => 'text',
                        'text' => $resp['code']
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

                // echo $bet_string;
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
                'text' => " ชื่อผู้ใช้งาน : " . $user_displayname . " " . $reponse_bet . "\r\n" . "💰 ยอดเงินคงเหลือ : "
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


    // echo "OK";
}
