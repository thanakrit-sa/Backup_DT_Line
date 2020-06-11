<?php
include('./config.php');
require_once('./custom/dt_function.php');

http_response_code(200);

date_default_timezone_set('Asia/Bangkok');
$current_datetime = date("Y-m-d H:i:s");
$content = file_get_contents('php://input');

$events = json_decode($content, true);

foreach ($events['events'] as $event) {

  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => "http://e-sport.in.th/ssdev/dt/dashboard/api/status/active",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
      "Cookie: dt_sys=3ila6qtfd0sqvbjvolk0b05scp6oldc5"
    ),
  ));
  $response = curl_exec($curl);
  curl_close($curl);
  $resultData = json_decode($response, true);
  $data_active = $resultData['data'];
  $data_current_start = $resultData['start'];


  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => "http://e-sport.in.th/ssdev/dt/dashboard/api/systemconfig/adminid",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
      "Cookie: dt_sys=3ila6qtfd0sqvbjvolk0b05scp6oldc5"
    ),
  ));
  $response = curl_exec($curl);
  curl_close($curl);
  $resultData = json_decode($response, true);
  $data_adminid = $resultData['admin_id'];

  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => "http://e-sport.in.th/ssdev/dt/dashboard/api/systemconfig/minmax",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
      "Cookie: dt_sys=3ila6qtfd0sqvbjvolk0b05scp6oldc5"
    ),
  ));
  $response = curl_exec($curl);
  curl_close($curl);
  $resultData = json_decode($response, true);
  $data_min = $resultData['min'];
  $data_max = $resultData['max'];


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


    if ($text == "info") {

      $messages = [
        'type' => 'text',
        'text' => "UserID : " . $userID . "\r\n" . "GroupID : " . $groupID
      ];
    } else if ($text == "@start" && $data_adminid == $userID) {

      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_URL => "http://e-sport.in.th/ssdev/dt/dashboard/api/status/start",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
          "Cookie: dt_sys=lt00i39rlprbgqj19anrp6820vk787kd"
        ),
      ));

      $response = curl_exec($curl);
      curl_close($curl);
      $resultStart = json_decode($response, true);
      $data_start_status = $resultStart['status'];

      if ($data_start_status) {



        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://e-sport.in.th/ssdev/dt/dashboard/api/status/autoupdate_game",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "Cookie: dt_sys=3ila6qtfd0sqvbjvolk0b05scp6oldc5"
          ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $resultData = json_decode($response, true);
        $data_game = $resultData['game'];

        $messages = [
          'type' => 'text',
          'text' => 'เตรียมเริ่มตาที่ ' . $data_game
        ];
      } else {

        $messages = [
          'type' => 'text',
          'text' => 'ไม่สามารถใช้คำสั่ง @start ซ้ำได้เนื่องจาก คุณยังไม่ได้พิมพ์ @end'
        ];

        // $data_start_code = $resultStart['code'];

      }
    } else if ($text == "@end" && $data_adminid == $userID) {

      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_URL => "http://e-sport.in.th/ssdev/dt/dashboard/api/status/end",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
          "Cookie: dt_sys=lt00i39rlprbgqj19anrp6820vk787kd"
        ),
      ));

      $response = curl_exec($curl);
      curl_close($curl);
      $resultStart = json_decode($response, true);
      $data_start_status = $resultStart['status'];

      if ($data_start_status) {

        $messages = [
          'type' => 'text',
          'text' => 'จบการเล่นในตานี้'
        ];
      } else {


        $data_start_code = $resultStart['code'];
        if ($data_start_code == "700") {
          $messages = [
            'type' => 'text',
            'text' => 'ไม่สามารถใช้คำสั่ง @end ได้เนื่องจากยังไม่ได้ ปิดรอบ (Sticker)'
          ];
        } else {
          $messages = [
            'type' => 'text',
            'text' => 'ไม่สามารถใช้คำสั่ง @end ซ้ำได้ โปรดพิมพ์ @start เพื่อเริ่มตาใหม่'
          ];
        }
      }
    } else {


      $bet_value = checkbetvalue($text);

      if (is_numeric($bet_value) && $bet_value <= 0) {


        $messages = [
          'type' => 'text',
          'text' => $user_displayname . " : " . "\r\n" . "⛔️ รูปแบบการเดิมพันไม่ถูกต้อง",
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

        $split_slash_count = substr_count($text, "/");

        if ($split_slash_count == 0) {

          $bet_type = "single";

          $bet_string = checkbetstring(strtolower($text), $code, $part);
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
                'text' => $user_displayname . " : " . "\r\n" . "UserID : " . $userID . "\r\n" . "💰ยอดเงินคงเหลือ : " . $credit . " บาท"
              ];
            } else {
              $messages = [
                'type' => 'text',
                'text' => $user_displayname . " : " . "\r\n" . "🥺 ท่านยังไม่ได้ทำการสมัครสมาชิก" . "\r\n" . "📝 สมัครสมาชิกพิมพ์ : play",
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


            if ($resultData['code'] == "503") {

              $messages = [
                'type' => 'text',
                'text' => $user_displayname . " : " . "\r\n" . "💾 ไม่พบประวัติการเดิมพัน 💾" . "\r\n" . $ans
              ];
            } else {
              foreach ($resultData['msg'] as $data) {
                $name[] = $data['bet_text'];
                $value[] = $data['value'];
              };
              for ($i; $i <= sizeof($resultData['msg']) - 1; $i++) {
                $ans = $ans . "#" . $num++ . " แทง => " . $name[$i] . " จำนวน " . $value[$i] . " บาท" . "\n";
              };

              $messages = [
                'type' => 'text',
                'text' => $user_displayname . " : " . "\r\n" . "💾 ประวัติการเดิมพัน 💾" . "\r\n" . $ans
              ];
            }
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
                'text' => $user_displayname . " : " . "\r\n" . "😇 ชื่อผู้ใช้นี้เป็นสมาชิกอยู่แล้ว"
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
                'text' => $user_displayname . " : " . "\r\n" . "✅ ทำการลงทะเบียนสำเร็จ ✅" . "\r\n"
              ];
            }
          } else {
            if (!$bet_string) {
              // $messages = [
              //     'type' => 'text',
              //     'text' => "ชื่อผู้ใช้งาน : " . $user_displayname . "\r\n" . "⛔️ รูปแบบการเดิมพันไม่ถูกต้อง",
              //     "quickReply" => [
              //         "items" => [
              //             [
              //                 "type" => "action",
              //                 "action" => [
              //                     "type" => "message",
              //                     "label" => "👉 ดูคู่มือการเดิมพัน",
              //                     "text" => "step"
              //                 ]
              //             ]
              //         ]
              //     ]
              // ];
            } else if (!is_numeric($bet_value)) {

              $messages = [
                'type' => 'text',
                'text' => $user_displayname . " : " . "\r\n" . "⛔️ ยอดเงินเดิมพันไม่ถูกต้อง",
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

              // start of bet


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


                if ($data_active == "off") {

                  $messages = [
                    'type' => 'text',
                    'text' => "ขณะนี้ยังไม่เปิดรอบเดิมพัน กรุณารอเปิดรอบเดิมพัน"
                  ];
                } else {
                  if ($bet_value >= $data_min && $bet_value <= $data_max) {
                    $ch = curl_init('http://e-sport.in.th/ssdev/dt/dashboard/api/user_test/profile/' . $userID);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',));
                    $result = curl_exec($ch);
                    curl_close($ch);
                    $resultData = json_decode($result, true);
                    $data = $resultData['data'];
                    $status = $data['status'];
                    if ($status == "active") {
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
                      $response_data = json_decode($response, true);
                      $response_code = $response_data['code'];

                      if ($response_code == "509") {
                        $messages = [
                          'type' => 'text',
                          'text' => $user_displayname . " : " . "\r\n" . "เดิมพันไม่สำเร็จยอดคงเหลือไม่เพียงพอ "
                        ];
                      } else {
                        $messages = [
                          'type' => 'text',
                          'text' => $user_displayname . " : " . "\r\n" . "เดิมพัน : " . $bet_text . "\r\n" . "จำนวน : " . $bet_value . " บาท"
                        ];
                      }
                    } else {
                      $messages = [
                        'type' => 'text',
                        'text' => $user_displayname . " : " . "\r\n" . " ถูกระงับการใช้งาน โปรดติดต่อทีมงาน "
                      ];
                    }
                  } else {
                    $messages = [
                      'type' => 'text',
                      'text' => $user_displayname . " : " . "\r\n" . " เดิมพันไม่สำเร็จ ยอดเดิมพันไม่ถูกต้อง ขั่นต่ำ " . $data_min . " สูงสุด " . $data_max
                    ];
                  }
                }
              } else {
                $messages = [
                  'type' => 'text',
                  'text' => $user_displayname . " : " . "\r\n" . "🥺 ท่านยังไม่ได้ทำการสมัครสมาชิก" . "\r\n" . "📝 สมัครสมาชิกพิมพ์ : play",
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
            }
          }
        } else if ($split_slash_count > 0) {


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

            if ($data_active == "off") {

              $messages = [
                'type' => 'text',
                'text' => "ขณะนี้ยังไม่เปิดรอบเดิมพัน กรุณารอเปิดรอบเดิมพัน"
              ];
            } else {

              $reponse_bet = '';
              $bet_type = "multiple";
              $arrKeywords = explode("/", $text);
              $i = 0;
              foreach ($arrKeywords as $element) {

                $i++;
                $bet_string = checkbetstring($element, $code, $part);
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


                  if ($bet_value >= $data_min && $bet_value <= $data_max) {

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

                    $response_data = json_decode($response, true);
                    $response_code = $response_data['code'];

                    if ($response_code == "509") {
                      $element_reponse = '# ' . $i . ' ยอดเงินคงเหลือไม่เพียงพอสำหรับการแทง > ' . $bet_text . " จำนวน " . $bet_value;
                    } else {
                      $element_reponse = '# ' . $i . ' แทง > ' . $bet_text . " จำนวน " . $bet_value;
                    }
                  } else {

                    $element_reponse = '# ' . $i . ' เดิมพันไม่สำเร็จ ยอดเดิมพันไม่ถูกต้อง ขั่นต่ำ ' . $data_min . ' สูงสุด ' . $data_max;
                  }
                }


                $reponse_bet = $reponse_bet . "\n" . $element_reponse;
              }


              $messages = [
                'type' => 'text',
                'text' => $user_displayname . " : " . " " . $reponse_bet . "\r\n" . "💰 ยอดเงินคงเหลือ : " . $credit
              ];
            }
          } else {

            $messages = [
              'type' => 'text',
              'text' => $user_displayname . " : " . "\r\n" . "🥺 ท่านยังไม่ได้ทำการสมัครสมาชิก" . "\r\n" . "📝 สมัครสมาชิกพิมพ์ : play",
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
        }
      }
    }
  } else if ($event['type'] == 'message' && $event['message']['type'] == 'sticker' && $data_adminid == $userID) {



    if ($data_current_start == "off") {

      $messages = [
        'type' => 'text',
        'text' => "สถานะปัจจุบันคือ @end (จบการเล่นในตานี้) ไม่สามารถส่ง Sticker เพื่อเริ่มรอบใหม่ได้ กรุณาพิมพ์ @start"
      ];
    } else {

      if ($data_active == "on") {
        $image_path = "https://www.img.in.th/images/d4459db3fcf68337c3dfb62c5b1ebef2.png";
      } else {


        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://e-sport.in.th/ssdev/dt/dashboard/api/status/status_game",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "Cookie: dt_sys=3ila6qtfd0sqvbjvolk0b05scp6oldc5"
          ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $resultData = json_decode($response, true);
        $data_game = $resultData['game'];

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://e-sport.in.th/ssdev/dt/dashboard/api/status/autoupdate_part",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "Cookie: dt_sys=3ila6qtfd0sqvbjvolk0b05scp6oldc5"
          ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $resultData = json_decode($response, true);
        $data_part = $resultData['part'];

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://notify-api.line.me/api/notify",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => array(
            'message' => 'เริ่ม ตาที่ ' . $data_game . ' / รอบที่ ' . $data_part
          ),

          CURLOPT_HTTPHEADER => array(
            "authorization: Bearer VQPBsmzilgF9VU4TXJNIk06883RB0dNAfpACTX07RXK"
          ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);



        $image_path = "https://www.img.in.th/images/3de1e615637f61d3f22079157ea70693.png";
      }

      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => "http://e-sport.in.th/ssdev/dt/dashboard/api/status/autoupdate_active",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
          "Cookie: dt_sys=3ila6qtfd0sqvbjvolk0b05scp6oldc5"
        ),
      ));

      $response = curl_exec($curl);
      curl_close($curl);


      $messages = [
        'type' => 'image',
        'originalContentUrl' => $image_path,
        'previewImageUrl' => $image_path
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
