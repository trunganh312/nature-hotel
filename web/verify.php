<?
include('Core/Config/require_web.php');

$Layout->setTitle('Xác thực tài khoản')
        ->setDescription('Xác thực tài khoản')
        ->setKeywords('Xác thực tài khoản')
        ->setIndex(false);

//Breadcrum bar
$arr_breadcrum  =   [
                    ['text' => 'Xác thực tài khoản']
                    ];
                    
$action =   getValue('action', GET_STRING, GET_POST, '');
$uid    =   getValue('uid');
$token_verify   =   false;  //Đánh dấu là có hợp lệ để cho reset password ko

if ($uid > 0) {
    
    $token_verify   =   $User->verifyToken();
    
    if ($token_verify) {
        $User->setVerified($uid);
    } else {
        $error  =   $User->getError();
    }
    
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?=$Layout->loadHead()?>
</head>
<body class="page-template-default page st-header-2 woocommerce-page header_white">
    <?
    include($path_root . 'layout/inc_header.php');
    ?>
    <div id="st-content-wrapper">
        <div class="st-breadcrumb">
            <div class="container">
                <?=generate_navbar($arr_breadcrum)?>
            </div>
        </div>
        <div class="container">
            <div class="st-checkout-page">
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-lg-push-8 col-md-push-8">
                        
                    </div>
                    <div class="col-lg-8 col-md-8 col-lg-pull-4 col-md-pull-4">
                        <?
                        if ($token_verify) {
                            ?>
                            <h4 class="title">Tài khoản của bạn đã được xác thực thành công!</h4>
                            <?
                            if ($User->logged) {
                                ?>
                                <p>Bạn có thể truy cập <a href="<?=$cfg_path_profile?>">Trang cá nhân</a> để quản lý thông tin và theo dõi các dịch vụ đã sử dụng tại <?=BRAND_NAME?> ngay bây giờ!</p>
                                <?
                            } else {
                                ?>
                                <p>Vui lòng <a href="javascript:;" class="login" data-toggle="modal" data-target="#st-login-form">Đăng nhập</a> để quản lý thông tin và theo dõi các dịch vụ đã sử dụng tại <?=BRAND_NAME?> ngay bây giờ!</p>
                                <?
                            }
                        } else {
                            echo    '<h4>Liên kết không hợp lệ hoặc đã quá thời gian cho phép!</h4>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?
    include($path_root . 'layout/inc_footer.php');
    ?>
    
    <?=$Layout->loadFooter()?>
</body>
</html>