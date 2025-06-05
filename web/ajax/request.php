<?
include('../Core/Config/require_web.php');
include('../../Vietgoing_Core/Model/DataModel.php');
$DataModel  =   new DataModel;

//Lưu log để theo dõi spam
save_log('send_request.cfn', json_encode($_POST));

$fullname   =   getValue('fullname', GET_STRING, GET_POST, '', 1);
$email      =   getValue('email', GET_STRING, GET_POST, '', 1);
$phone      =   convert_phone_number(getValue('phone', GET_STRING, GET_POST, '', 1));
$note       =   getValue('note', GET_STRING, GET_POST, '', 1);

$response   =   [
    'status'   =>  0,
    'errors'   =>  []
];

/** Chặn spam **/
$arr_deny   =   ['5556660606'];
if (in_array($phone, $arr_deny)) {
    $response['errors']['note'] =   'Có lỗi xảy ra, vui lòng thử lại';
    echo    json_encode($response);
    exit;
}

if (empty($fullname)) {
    $response['errors']['fullname'] =   'Vui lòng nhập họ và tên';
}
$valid_phone    =   false;
if (empty($phone)) {
    $response['errors']['phone']    =   'Vui lòng nhập số điện thoại';
} else if(!validate_phone($phone)) {
    $response['errors']['phone']    =   'Số điện thoại không hợp lệ';
} else {
    $valid_phone    =   true;
}
if (empty($note)) {
    $response['errors']['note']     =   'Vui lòng nhập nội dung yêu cầu';
}

if (!empty($response['errors'])) {
    echo    json_encode($response);

    //Nếu có nhập số ĐT nhưng ko nhập tên với nội dung thì bắn vào Tele
    if ($valid_phone) {
        $str_notify =   "Có khách đang gửi Request bị sai thông tin:\n";
        $str_notify .=  "ĐT: " . $phone . "\n";
        $str_notify .=  "Họ tên: " . $fullname . "\n";
        $str_notify .=  "Note: " . $note . "\n";
        foreach ($response['errors'] as $e) {
            $str_notify .=  "Error: " . $e . "\n";
        }
        $str_notify .=  "URL: " . @$_SERVER['SERVER_NAME'] . urldecode(@$_SERVER["REQUEST_URI"]);
        
        pushNotifyTelegram($str_notify, $icon="%F0%9F%94%94", $chat_id='-782051860');
    }

    exit;
}

/** --- Class query để insert dữ liệu --- **/
$Query  =   new GenerateQuery('customer_request');
$Query->add('cure_name', DATA_STRING, $fullname)
        ->add('cure_phone', DATA_STRING, $phone)
        ->add('cure_email', DATA_STRING, $email)
        ->add('cure_content', DATA_STRING, $note)
        ->add('cure_url', DATA_STRING, getValue('url', GET_STRING, GET_POST, ''))
        ->add('cure_source', DATA_INTEGER, BK_SOURCE_WEB)
        ->add('cure_status', DATA_INTEGER, STT_NEW)
        ->add('cure_time_create', DATA_INTEGER, CURRENT_TIME)
        ->add('cure_admin_create', DATA_INTEGER, 0)
        ->add('cure_admin_process', DATA_INTEGER, 0)
        ->add('cure_last_update', DATA_INTEGER, CURRENT_TIME)
        ->add('cure_ads', DATA_INTEGER, getValue(PARAM_ADS, GET_INT, GET_COOKIE));
/** --- End of Class query để insert dữ liệu --- **/

if ($Query->validate()) {
    
    $request_id =   $DB->executeReturn($Query->generateQueryInsert());
    
    if ($request_id > 0) {
        
        /** Lưu lại lịch sử log ở bảng log xử lý nội bộ **/
        $Query  =   new GenerateQuery('request_note');
        $Query->add('reno_request_id', DATA_INTEGER, $request_id)
                ->add('reno_admin_id', DATA_INTEGER, 0)
                ->add('reno_time_create', DATA_INTEGER, CURRENT_TIME)
                ->add('reno_content', DATA_STRING, $note);
        if ($Query->validate()) {
            $DB->execute($Query->generateQueryInsert());
        }
        
        /** Update Data ID **/
        $DataModel->updateDataNew([
            'phone'     =>  $phone,
            'name'      =>  $fullname,
            'content'   =>  'Gửi Request: ' . $note
        ], 'customer_request', $request_id);
        
        //Bắn thông báo vào Tele
        pushNotifyTelegram($cfg_website['con_message_new_request']);
        
        //Set session để ko hiển thị lại trong phiên này
        $_SESSION['vg_sent_request']    =   1;
		//setcookie("vg_sent_request", 1, (CURRENT_TIME + 604800), "/");
    }
}
$response['status'] =   1;
echo    json_encode($response);
exit;