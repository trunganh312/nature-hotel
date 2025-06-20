<?
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
include('data_detail.php');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?=$Layout->loadHead()?>
</head>
<body class="page_hotel_detail header_white st_hotel-template-default single single-st_hotel st-header-2">
    <?
    include($path_root . 'layout/inc_header.php');
    ?>
    
    <?
    include('view_detail.php');
    ?>
    
    <?
    include($path_root . 'layout/inc_footer.php');
    ?>
    <?=$Layout->loadFooter()?>
</body>
</html>
