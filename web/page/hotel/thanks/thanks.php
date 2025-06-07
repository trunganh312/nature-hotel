<?
include('data_thanks.php');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?=$Layout->loadHead()?>
</head>
<body class="page_thanks page_thanks_hotel page-template-default header_white page st-header-2 woocommerce-page">
    <?
    include($path_root . 'layout/inc_header.php');
    ?>
    
    <?
    include('view_thanks.php');
    ?>
    
    <?
    include($path_root . 'layout/inc_footer.php');
    ?>
    
    <?=$Layout->loadFooter()?>
</body>
</html>
