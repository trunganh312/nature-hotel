<?php 
include('../Core/Config/require_web.php');

$record_id  =   getValue('id');
$type       =   getValue('type', GET_STRING, GET_GET, 'destination');   //Mặc định là địa danh
$data       =   [];

switch ($type) {
    case 'article':
        $data   =   $DB->query("SELECT art_id AS id, art_image, art_title AS name, art_content AS content
                                FROM article
                                WHERE art_id = $record_id LIMIT 1")
                                ->getOne();
        if(!empty($data)) {
            $data['image']  =   $Router->srcArticle($data['art_image']);
            /** Update lượt view **/
            $Model->updateCountView('article', $record_id);
        }
        break;
        
    default:
        $data   =   $DB->query("SELECT des_id AS id, des_address_full AS address, des_image, des_name AS name, des_description AS content
                                FROM destination
                                WHERE des_id = $record_id LIMIT 1")
                                ->getOne();
        if(!empty($data)) {
            $data['image']  =   $Router->srcDestination($data['des_image']);
            /** Update lượt view **/
            $Model->updateCountView('destination', $record_id);
        }
        break;
}
if(empty($data)) return;

echo json_encode($data);
exit;