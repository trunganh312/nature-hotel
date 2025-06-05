<?
include('../../Core/Config/require_crm.php');

$keyword    =   getValue('term', GET_STRING, GET_GET, '');
$type       =   getValue('type', GET_STRING, GET_GET, '');
$data       =   [];

switch ($type) {
    case 'hotel':
        //Lấy các KS active hoặc KS 161 là KS dùng để tạo các đơn KS nhỏ lẻ
        $data   =   $DB->query("SELECT hot_id AS id, hot_name AS name,
                                    IF(hot_name LIKE '" . $keyword . "%', 20, 0) + IF(hot_name LIKE '%" . $keyword . "%', 8, 0) AS diem
                                FROM hotel
                                WHERE hot_name LIKE '%" . $keyword . "%' AND (hot_active = 1 OR hot_id = 161)
                                ORDER BY diem DESC, hot_name
                                LIMIT 10")
                                ->toArray();
        break;
        
    case 'tour':
        $data   =   $DB->query("SELECT tou_id AS id, CONCAT(tou_code, ' - ', tou_name) AS value,
                                    IF(tou_name LIKE '" . $keyword . "%', 20, 0) + IF(tou_name LIKE '%" . $keyword . "%', 8, 0) AS diem,
                                    tou_price AS price_adult, tou_price_children AS price_children, tou_price_baby AS price_baby
                                FROM tour
                                WHERE tou_active = 1 AND (tou_name LIKE '%" . $keyword . "%' OR tou_code LIKE '%" . $keyword . "%')
                                ORDER BY diem DESC, tou_name
                                LIMIT 10")
                                ->toArray();
        break;
    
    case 'partner':
        $data   =   $DB->query("SELECT par_id AS id, CONCAT(par_name, ' - ', par_email) AS value,
                                    IF(par_name LIKE '" . $keyword . "%', 20, 0) + IF(par_name LIKE '%" . $keyword . "%', 8, 0) AS diem
                                FROM partner
                                WHERE par_name LIKE '%" . $keyword . "%'
                                ORDER BY diem DESC, par_name
                                LIMIT 10")
                                ->toArray();
        break;
    
    case 'ticket':
        $data   =   $DB->query("SELECT tic_id AS id, tic_name AS value,
                                    IF(tic_name LIKE '" . $keyword . "%', 20, 0) + IF(tic_name LIKE '%" . $keyword . "%', 8, 0) AS diem
                                FROM ticket
                                WHERE tic_name LIKE '%" . $keyword . "%'
                                ORDER BY diem DESC, tic_name
                                LIMIT 10")
                                ->toArray();
        break;
        
    case 'user':
        $data   =   $DB->query("SELECT use_id AS id, CONCAT(use_name, ' - ', use_email) AS value,
                                    IF(use_name LIKE '" . $keyword . "%', 20, 0) + IF(use_name LIKE '%" . $keyword . "%', 8, 0)
                                    + IF(use_email LIKE '" . $keyword . "%', 20, 0) + IF(use_email LIKE '%" . $keyword . "%', 5, 0) AS diem
                                FROM users
                                WHERE use_active = 1 AND (use_name LIKE '%" . $keyword . "%' OR use_email LIKE '%" . $keyword . "%')
                                ORDER BY diem DESC, use_name
                                LIMIT 10")
                                ->toArray();
        break;
        
    case 'admin':
        $data   =   $DB->query("SELECT adm_id AS id, CONCAT(adm_name, ' - ', adm_email) AS value,
                                    IF(adm_name LIKE '" . $keyword . "%', 20, 0) + IF(adm_name LIKE '%" . $keyword . "%', 8, 0)
                                    + IF(adm_email LIKE '" . $keyword . "%', 20, 0) + IF(adm_email LIKE '%" . $keyword . "%', 15, 0) AS diem
                                FROM admins
                                WHERE adm_active = 1 AND adm_cto = 0 AND (adm_name LIKE '%" . $keyword . "%' OR adm_email LIKE '%" . $keyword . "%')
                                ORDER BY diem DESC, adm_name
                                LIMIT 10")
                                ->toArray();
        break;
        
    case 'company':
        $sql_more   =   "";
        //Lọc thêm là lấy nhóm company nào (KS, lữ hành, TA...)
        $more   =   getValue('more', GET_STRING, GET_GET, '');
        if ($more == 'hotel') {
            $sql_more   .=  "AND com_group = " . MODULE_HOTEL;
        } else if ($more == 'tour') {
            $sql_more   .=  "AND com_group = " . MODULE_TOUR;
        }
        $data   =   $DB->query("SELECT com_id AS id, CONCAT(com_name, ' - ', com_license_business) AS value,
                                    IF(com_name LIKE '" . $keyword . "%', 20, 0) + IF(com_name LIKE '%" . $keyword . "%', 8, 0) + IF(com_license_business LIKE '" . $keyword . "%', 20, 0) + IF(com_license_business LIKE '%" . $keyword . "%', 8, 0) AS diem
                                FROM company
                                WHERE com_active = 1 $sql_more AND (com_name LIKE '%" . $keyword . "%' OR com_license_business LIKE '%" . $keyword . "%')
                                ORDER BY diem DESC, com_name
                                LIMIT 10")
                                ->toArray();
        break;
}

echo    json_encode($data);
exit;
?>