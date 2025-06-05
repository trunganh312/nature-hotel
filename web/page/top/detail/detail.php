<?
include('data_detail.php');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?=$Layout->loadHead()?>
</head>
<body class="<?=($page_module == 'destination' ? 'page_destination_list' : 'page_tour_list page_hotel_list')?> page_collection header_white page-template st-header-2">
    <?
    include($path_root . 'layout/inc_header.php');
    ?>
    
    <?
    include('view_detail_' . $page_module . '.php');
    ?>
    
    <?
    include($path_root . 'layout/inc_footer.php');
    ?>
    
    <?=$Layout->loadFooter()?>
    
    <?
    /** Update lượt view **/
    $Model->updateCountView('collection', $collection_id);
    ?>
</body>
</html>
