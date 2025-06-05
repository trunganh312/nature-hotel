<?php
include('../Core/Config/require_web.php');
require_once($path_core . 'Classes/Upload.php');

//Temp unique
$temp           =  getValue('tempup', 'str', 'SESSION', '', 1);
$temp_client    =  getValue('tempup', 'str', 'POST', '', 1);
if($temp != $temp_client) exit;

if (!empty($_FILES)) {
    $list_image     =   [];
    $targetFolder   =   '../../Vietgoing_CRM/image/temp_up/'; //Folder upload temp

    //Class Upload
    $Upload   = new Upload('files', $targetFolder);
    $filename = $Upload->new_name;
    if (empty($Upload->error)) {
        //Insert bản ghi vào bảng temp
        if ($filename != '') {
            $record_id = $DB->executeReturn("INSERT INTO image_temp (imte_image, imte_temp) VALUES ('" . replaceMQ($filename) . "', '" . $temp . "')");
            $list_image[] = [
                'path' => '/temp_up/' . $filename,
                'id'   => $record_id,
            ];
        }
    }
    // Lấy ra thông tin ảnh
    $data = [];
    foreach ($list_image as $item) {
        $data['k' . $item['id']] = [
            'id'    => $item['id'],
            'src'  => DOMAIN_IMAGE . $item['path'],
            'name' => $filename,
        ];
    }
    echo json_encode($data);
    exit;
}
