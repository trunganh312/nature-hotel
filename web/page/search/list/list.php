<?
include('../../../Core/Config/require_web.php');

/** Keyword search **/
$keyword    =   getValue('q', GET_STRING, GET_GET, '', 1);

//Nếu spam thì cho 404 luôn
$keyword_lower  =   mb_strtolower($keyword, 'UTF-8');
if (strpos($keyword, '~') !== false
|| preg_match('/xoso|loto/i', $keyword_lower)
) {
    save_log('spam_search.cfn', 'POST: ' . json_encode($_POST) . '. GET: ' . json_encode($_GET) . '. SERVER: ' . json_encode($_SERVER) . '. Keyword: ' . $keyword);
    redirect_url($cfg_path_404);
}

$Layout->setTitle('Tìm kiếm theo từ khóa: ' . $keyword)
        ->setDescription('Kết quả tìm kiếm liên quan đến du lịch của từ khóa: ' . $keyword . '. Vietgoing hỗ trợ tư vấn thêm các thông tin đặt phòng, tour, vé đảm bảo giá tốt nhất.')
        ->setKeywords('kết quả, tìm kiếm, du lịch, ' . $keyword . ', đặt phòng, đặt tour, đặt vé')
        ->setCanonical($cfg_path_search . '?q=' . $keyword)
        ->setIndex(false)
        ->setJS(['page.basic']);

//Generate ra các câu SQL tính điểm
$search_data    =   $Search->generateSQLSearch($keyword, [
    'cit_search_data',
    'dis_search_data',
    'hot_data_search',
    'tou_search_data',
    'tic_search_data',
    'des_search_data',
    'art_search_data'
]);
//dd($search_data);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?=$Layout->loadHead()?>
</head>
<body class="page_home page_search header_white home page-template page st-header-2">
    <?
    include($path_root . 'layout/inc_header.php');
    ?>
    
    <?
    include('view_search.php');
    ?>
    
    <?
    include($path_root . 'layout/inc_footer.php');
    ?>
    
    <?=$Layout->loadFooter()?>
</body>
</html>
