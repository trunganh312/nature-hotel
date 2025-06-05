<?
include('../Core/Config/require_web.php');

$id     =   getValue('id');
$type   =   getValue('type', GET_STRING, GET_GET, 'tour');
$url    =   '';

switch ($type) {
    case 'tour':
        $row    =   $DB->query("SELECT tou_id, tou_name, tou_group FROM tour WHERE tou_id = $id")->getOne();
        if (!empty($row)) {
            $url    =   $Router->detailTour($row);
        }
        break;
        
    case 'article':
        $row    =   $DB->query("SELECT art_id, art_title FROM article WHERE art_id = $id")->getOne();
        if (!empty($row)) {
            $url    =   $Router->detailArticle($row);
        }
        break;
        
    case 'destination':
        $row    =   $DB->query("SELECT des_id, des_name FROM destination WHERE des_id = $id")->getOne();
        if (!empty($row)) {
            $url    =   $Router->detailDestination($row);
        }
        break;

    case 'hotel':
        $row    =   $DB->query("SELECT hot_id, hot_name FROM hotel WHERE hot_id = $id")->getOne();
        if (!empty($row)) {
            $url    =   $Router->detailHotel($row);
        }
        break;

    case 'collection':
        $row    =   $DB->query("SELECT col_id, col_name FROM collection WHERE col_id = $id")->getOne();
        if (!empty($row)) {
            $url    =   $Router->detailCollection($row);
        }
        break;

    case 'ticket':
        $row    =   $DB->query("SELECT tic_id, tic_name FROM ticket WHERE tic_id = $id")->getOne();
        if (!empty($row)) {
            $url    =   $Router->detailTicket($row);
        }
        break;
}


if (!empty($url)) {
    //Set session để ko bị ghi nhận lượt view
    $_SESSION['source_reffer']  =   'crm';
    //redirect_url($url);
    echo '<meta http-equiv="refresh" content="0;url=' . $url . '">';
    exit;
} else {
    //redirect_url(DOMAIN_WEB);
    echo '<meta http-equiv="refresh" content="0;url=' . DOMAIN_WEB . '">';
    exit;
}
?>