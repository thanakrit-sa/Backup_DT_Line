<?php
// $url = "http://e-sport.in.th/ssdev/dt/dashboard/auth";

// $data = array(
//     "username" => "admin",
//     "password" => "admin",
// );

// // $request = "";

// // foreach ($data as $key => $val) {
// //     $request .= $key . "=" . $val . "&";
// // }

// // $request = rtrim($request, "&");

// $request = json_encode($data);

// $ch = curl_init();

// curl_setopt($ch, CURLOPT_URL, $url);
// curl_setopt($ch, CURLOPT_POST, 1);
// curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
// curl_setopt($ch, CURLOPT_HEADER, 0);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

// $response = curl_exec($ch);
// curl_close($ch);

// echo $response;

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

            $bet_string = checkbetstring($text, $code);
            $bet_value = checkbetvalue($text);
            $code = explode("/", $bet_string);
            $bet_text = $code[0];
            $bet_code = $code[1];

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
            } else if ($bet_string == "ประวัติ") {
                $ch = curl_init('http://e-sport.in.th/ssdev/dt/dashboard/api/bet_test/logbet_lineid/' . $userID);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',));
                $result = curl_exec($ch);
                curl_close($ch);
                $resultData = json_decode($result, true);
                $i = 0;
                $num = 1;
                $ans = "";
                foreach ($resultData['msg'] as $data) {
                    $name[] = $data['bet_text'];
                    $value[] = $data['value'];
                };
                for ($i; $i <= sizeof($resultData['msg']) - 1; $i++) {
                    $ans = $ans . "#" . $num++ . " แทง => " . $name[$i] . " จำนวน " . $value[$i] . " บาท" . "\n";
                };
                $messages = [
                    'type' => 'text',
                    'text' => " ชื่อผู้ใช้งาน : " . $user_displayname . "\r\n" . "💾 ประวัติการเดิมพัน 💾" . "\r\n" . $ans
                ];
            } else if ($bet_string == "ยกเลิก") {
                $data = array(
                    "user_lineid" => $userID
                );

                $request = "";

                foreach ($data as $key => $val) {
                    $request .= $key . "=" . $val . "&";
                }

                $request = rtrim($request, "&");

                $url = 'http://e-sport.in.th/ssdev/dt/dashboard/api/bet_test/remove_lineid';

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                $response = curl_exec($ch);
                curl_close($ch);

                echo $response;
                $messages = [
                    'type' => 'text',
                    'text' => "ชื่ผู้ใช้งาน : " . $user_displayname . "\r\n" . "❌ ยกเลิกการเดิมพันทั้งหมด ❌"
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

                    $data = array(
                        "user_id" => $user_id,
                        "user_lineid" => $userID,
                        "user_displayname" => $user_displayname,
                        "bet_text" => $bet_text,
                        "value" => $bet_value,
                        "bet_code" => $bet_code
                    );

                    $request = "";

                    foreach ($data as $key => $val) {
                        $request .= $key . "=" . $val . "&";
                    }

                    $request = rtrim($request, "&");

                    $url = 'http://e-sport.in.th/ssdev/dt/dashboard/api/bet_test/logbet_create';

                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                    $response = curl_exec($ch);
                    curl_close($ch);

                    echo $response;

                    $messages = [
                        'type' => 'text',
                        'text' => "ชื่อผู้ใช้งาน : " . $user_displayname . "\r\n" . "เดิมพัน : " . $bet_text . "\r\n" . "จำนวน : " . $bet_value . " บาท" . "\r\n" . "รหัสเดิมพัน : " . $bet_code
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
                $bet_string = checkbetstring($element, $code);
                $bet_value = checkbetvalue($element);
                $code = explode("/", $bet_string);
                $bet_text = $code[0];
                $bet_code = $code[1];


                // echo $bet_string;
                if (!$bet_string) {

                    $element_reponse = '# ' . $i . ' รูปแบบการเดิมพันของท่านไม่ถูกต้อง';
                } else if (!is_numeric($bet_value)) {


                    $element_reponse = '# ' . $i . ' ยอดเงินเดิมพันไม่ถูกต้อง';
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
                    $credit = $data['credit'];

                    $data = array(
                        "user_id" => $user_id,
                        "user_lineid" => $userID,
                        "user_displayname" => $user_displayname,
                        "bet_text" => $bet_text,
                        "value" => $bet_value,
                        "bet_code" => $bet_code
                    );

                    $request = "";

                    foreach ($data as $key => $val) {
                        $request .= $key . "=" . $val . "&";
                    }

                    $request = rtrim($request, "&");

                    $url = 'http://e-sport.in.th/ssdev/dt/dashboard/api/bet_test/logbet_create';

                    $ch = curl_init();

                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                    $response = curl_exec($ch);
                    curl_close($ch);

                    echo $response;

                    $element_reponse = '# ' . $i . ' แทง > ' . $bet_text . " จำนวน " . $bet_value;
                }


                $reponse_bet = $reponse_bet . "\n" . $element_reponse;
            }


            $messages = [
                'type' => 'text',
                'text' => " ชื่อผู้ใช้งาน : " . $user_displayname . " " . $reponse_bet . "\r\n" . "💰 ยอดเงินคงเหลือ : " . $credit
            ];
        }
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

