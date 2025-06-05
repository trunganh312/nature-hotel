<?
include('Core/Config/require_web.php');

/** Có một số URL đã index trên Google bị báo lỗi thì phải redirect 301 về page đúng **/
$arr_301    =   [
    '/destination/city-37',
    '/destination/city-10',
    '/tour/district-62-'
];

if (in_array($_SERVER['REQUEST_URI'], $arr_301)) {
    redirect301($_SERVER['REQUEST_URI'] . '-url-vg.html');
}

$Layout->setTitle('Nội dung không tồn tại!')
        ->setDescription('Nội dung không tồn tại')
        ->setKeywords('Nội dung không tồn tại')
        ->setIndex(false)
        ->setJS(['page.basic']);

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
            <div class="st-checkout-page">
                <div class="row" style="text-align: center;">
                    <h1>Rất tiếc nội dung bạn vừa truy cập không tồn tại!</h1>
                    <p><img alt="404" src="<?=$cfg_path_image?>404.png" style="height: 200px; margin: 20px 0;" /></p>
                    <h4>Quay lại <a href="<?=DOMAIN_WEB?>" title="Trang chủ">Trang chủ</a></h4>
                </div>
            </div>
        </div>
    </div>
    
    <?
    include('layout/inc_footer.php');
    ?>
    
    <?=$Layout->loadFooter()?>
</body>
</html>
