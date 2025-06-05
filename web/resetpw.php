<?
include('Core/Config/require_web.php');

$Layout->setTitle('Đặt lại mật khẩu đăng nhập')
        ->setDescription('Đặt lại mật khẩu đăng nhập tài khoản')
        ->setKeywords('Đặt lại mật khẩu đăng nhập tài khoản')
        ->setJS(['page.basic']);

//Breadcrum bar
$arr_breadcrum  =   [
                    ['text' => 'Đặt lại mật khẩu']
                    ];
                    
$action =   getValue('action', GET_STRING, GET_POST, '');
$uid    =   getValue('uid');
$token_verify   =   false;  //Đánh dấu là có hợp lệ để cho reset password ko

if ($uid > 0) {
    
    $token_verify   =   $User->verifyToken();
    if ($token_verify) {
        
        if ($action == 'change_password') {
            
            $new_password           =   getValue('new_password', 'str', 'POST', '');
            $new_password_confirm   =   getValue('new_password_confirm', 'str', 'POST', '');
            
            if ($new_password != $new_password_confirm) {
                $error[]    =   'Xác nhận mật khẩu mới không trùng khớp';
            }
            
            if (empty($error)) {
                //Update password
                if ($User->createNewPassword($uid, $new_password)) {
                    set_session_toastr('change_password', 'success');
                    
                    redirect_url($cfg_path_profile);
                } else {
                    $error  =   $User->getError();
                }
            }
        }
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
<body class="page_collection page_basic header_white page-template st-header-2">
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
                    <div class="col-md-12">
                        <?
                        if ($token_verify || 2 > 1) {
                            ?>
                            <h3 class="title">Đặt lại mật khẩu mới</h3>
                            <div class="check-out-form">
                                <form id="cc-form" class="" method="POST" name="checkout_tour">
                                    <div class="clearfix">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group form-group-icon-left">                
                                                    <label for="field-st_name">Mật khẩu mới <span class="require">*</span> </label>
                                                    <i class='fa fa-user input-icon'></i><input class="form-control required" id="field-st_name" name="new_password" placeholder="Tối thiểu 6 ký tự" type="password">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group form-group-icon-left">
                                                    <label for="field-st_email">Nhập lại mật khẩu mới <span class="require">*</span> </label>
                                                    <i class='fa fa-user input-icon'></i><input class="form-control required" id="field-st_email" name="new_password_confirm" placeholder="Nhập lại mật khẩu mới" type="password">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix">
                                        <div class="row">
                                            <div class="col-sm-6"></div>
                                        </div>
                                    </div>
                                    <div class="alert alert-danger form_alert <?=(empty($error) ? 'hidden' : '')?>">
                                        <?
                                        if (!empty($error)) {
                                            foreach ($error as $e) {
                                                echo    '&bull; ' . $e . '<br>';
                                            }
                                        }
                                        ?>
                                    </div>
                                    <button type="submit" id ="demo-form" class="btn btn-search btn-st-big">Cập nhật</button>
                                    <input type="hidden" name="action" value="change_password" />
                                </form>
                            </div>
                            <?
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