<?
$Layout->setTitle('Nội dung không tồn tại!')
        ->setDescription('Nội dung không tồn tại')
        ->setKeywords('Nội dung không tồn tại')
        ->setJS(['page.basic']);

$page_name  =   'này';
if (!empty($_SERVER['REQUEST_URI'])) {
    $exp    =   explode('?', $_SERVER['REQUEST_URI']);
    $page_name  =   '<strong>' . DOMAIN_WEB . $exp[0] . '</strong>';
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?=$Layout->loadHead()?>
</head>
<body class="page-template-default page st-header-2 woocommerce-page header_white">
    <?
    include('layout/inc_header.php');
    ?>
    <div id="st-content-wrapper">
        <div class="container">
            <div class="box_notfound">
                <p>Nội dung của trang <?=$page_name?> đang được Vietgoing cập nhật thêm thông tin nhằm đem lại những thông tin hữu ích và trải nghiệm tốt hơn cho Quý khách hàng!</p>
                <?
                include('layout/inc_not_found.php');
                ?>
            </div>
        </div>
    </div>
    <?
    include('layout/inc_footer.php');
    ?>
    <?=$Layout->loadFooter()?>
</body>
</html>