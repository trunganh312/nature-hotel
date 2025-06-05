<?
include('../../../Core/Config/require_web.php');

/**
 * Trang danh sách các bộ sưu tập theo Tỉnh/TP
 */
$search_type    =   getValue('group', GET_STRING, GET_GET, '');
$search_id      =   getValue('id');
$sql_filter     =   "col_active = 1 AND col_group = " . GROUP_HOTEL;

/** Tùy từng type search để lấy ra câu sql và title tương ứng **/
switch ($search_type) {
    case 'city':
        $search_info    =   $DB->query("SELECT cit_id, cit_name, cit_name AS name, cit_image, cit_content AS description, cit_active AS item_active
                                        FROM city
                                        WHERE cit_id = $search_id")
                                        ->getOne();
        if (empty($search_info)) {
            include('../../../soft_404.php');
            exit;
        }
        $city_id        =   $search_id;
        $sql_filter     .=  " AND col_city = $city_id";
        $place_name     =   $search_info['cit_name'];
        $place_name_full    =   $search_info['cit_name'];
        $place_url      =   $Router->listCollectionCity($search_info);
        if (!empty($search_info['cit_image']))  $page_image =   $Router->srcPlace($search_info['cit_image']);
        $url_canonical  =   $place_url;
        //Breadcrum bar
        $arr_breadcrum  =   [
                            ['link' => $place_url, 'text' => $search_info['cit_name']]
                            ];
    break;
    
    case 'district':
        $search_info    =   $DB->query("SELECT dis_id, dis_name, dis_name_show, dis_hot, dis_image, cit_id, cit_name, cit_image,
                                            dis_content AS description, dis_active AS item_active
                                        FROM district
                                        INNER JOIN city ON dis_city = cit_id
                                        WHERE dis_id = $search_id")
                                        ->getOne();
        if (empty($search_info)) {
            include('../../../soft_404.php');
            exit;
        }
        $district_name  =   $PlaceModel->getDistrictName($search_info, true);
        $search_info['name']    =   $district_name;
        $city_id        =   $search_info['cit_id'];
        
        $sql_filter     .=  " AND col_district = " . $search_info['dis_id'];
        $place_name     =   $district_name;
        $place_name_full    =    ($district_name == $search_info['cit_name'] ? $search_info['dis_name'] : $district_name . ', ' . $search_info['cit_name']);
        $place_url      =   $Router->listCollectionDistrict($search_info);
        $url_canonical  =   $place_url;
        if (!empty($search_info['dis_image'])) {
            $page_image =   $Router->srcPlace($search_info['dis_image']);
        } else {
            if (!empty($search_info['cit_image']))  $page_image =   $Router->srcPlace($search_info['cit_image']);
        }
        
        //Breadcrum bar
        $arr_breadcrum  =   [
                            ['link' => $Router->listCollectionCity($search_info), 'text' => $search_info['cit_name']],
                            ['link' => $place_url, 'text' => ($district_name == $search_info['cit_name'] ? $search_info['dis_name'] : $district_name)]
                            ];
        
    break;
    
    default:
        include('../../../soft_404.php');
        exit;
}

//Redirect về link đúng trong trường hợp tên của đối tượng bị sửa => URL bị sửa
redirect_correct_url($url_canonical);
$url_hotel_city =   $Router->listHotelCity($search_info);
$page_h1    =   'Các bộ sưu tập khách sạn HOT ở ' . $place_name_full . ' ' . date('Y') . ' (top list)';

//Lấy ra tất cả các BST
$list_collection    =   $DB->query("SELECT *
                                    FROM collection
                                    WHERE $sql_filter
                                    ORDER BY col_group, col_hot DESC, col_last_update DESC")
                                    ->toArray();

$Layout->setTitle($page_h1)
        ->setDescription('Danh sách các bộ sưu tập (top list) khách sạn, resort, khu nghỉ dưỡng được du khách ưa thích và lựa chọn đặt nhiều tại ' . $place_name_full . ' ' . date('Y'))
        ->setKeywords('bộ sưu tập, khách sạn, resort, địa danh, du lịch, yêu thích, đánh giá cao, đặt nhiều, ' . $place_name_full)
        ->setCanonical($url_canonical)
        ->setImages(['src' => $page_image, 'alt' => $page_h1])
        ->setJS(['page.basic']);

?>