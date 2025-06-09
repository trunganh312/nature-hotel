<?
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
