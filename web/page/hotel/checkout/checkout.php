<?
include('data_checkout.php');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?=$Layout->loadHead()?>
</head>
<body class="header_white page_checkout page_checkout_hotel page-template-default page st-header-2 woocommerce-page">
    <?
    include($path_root . 'layout/inc_header.php');
    ?>
    <?
    include('view_checkout.php');
    ?>
    
    <?
    include($path_root . 'layout/inc_footer.php');
    ?>
    
    <?=$Layout->loadFooter()?>
</body>
</html>
