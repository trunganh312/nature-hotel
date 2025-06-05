<div class="col-md-12">
    <div class="infor-st-setting">
        <form method="post" action="" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-12">
                    <?
                    /** Nếu là Reset mật khẩu thành công **/
                    $change_password    =   getValue('change_password', GET_STRING, GET_SESSION, '');
                    if ($change_password == 'success') {
                        unset($_SESSION['change_password']);
                        ?>
                        <div class="alert alert-success">
                            <button data-dismiss="alert" type="button" class="close"><span aria-hidden="true">×</span></button>
                            <p class="text-small">Đổi mật khẩu mới thành công!</p>
                        </div>
                        <?
                    }
                    
                    /** Nếu là đk tài khoản mới **/
                    $new_register   =   getValue('new_register', GET_STRING, GET_SESSION, '');
                    if ($new_register == 'success') {
                        unset($_SESSION['new_register']);
                        ?>
                        <div class="alert alert-success">
                            <button data-dismiss="alert" type="button" class="close"><span aria-hidden="true">×</span></button>
                            <p class="text-small">Đăng ký tài khoản thành công! Vui lòng truy cập vào email <?=$User->email?> để xác thực tài khoản của bạn.</p>
                        </div>
                        <?
                    }
                    
                    /** Nếu cập nhật thông tin thành công **/
                    $result_update  =   getValue('result', GET_STRING, GET_SESSION, '');
                    if ($result_update == 'success') {
                        unset($_SESSION['result']);
                        ?>
                        <div class="alert alert-success">
                            <button data-dismiss="alert" type="button" class="close"><span aria-hidden="true">×</span></button>
                            <p class="text-small">Cập nhật thành công</p>
                        </div>
                        <?
                    }
                    /** Nếu cập nhật thông tin lỗi **/
                    if (!empty($error)) {
                        ?>
                        <div class="alert alert-danger">
                            <button data-dismiss="alert" type="button" class="close"><span aria-hidden="true">×</span></button>
                            <?
                            foreach ($error as $e) {
                                echo    '<p class="text-small">' . $e . '</p>';
                            }
                            ?>
                        </div>
                        <?
                    }
                    ?>
                    <h4>Thông tin cá nhân</h4>
                </div>
            </div>
            <div class="row">
                    <div class="form-group form-group-filled col-md-3 col-xs-12">
                        <label for="st_email">Email</label>
                        <input id="email" readonly disabled class="form-control" value="<?=$User->email?>" type="text"/>
                    </div>
                    <div class="form-group form-group-filled col-md-3 col-xs-12">
                        <label for="st_name">Họ và tên</label>
                        <input id="name" name="name" class="form-control" value="<?=$name?>" type="text" />
                    </div>
                    <div class="form-group form-group-filled col-md-3 col-xs-12">
                        <label for="st_phone">Điện thoại</label>
                        <input id="phone" name="phone" class="form-control" value="<?=$phone?>" type="text" />
                    </div>
                    <div class="form-group form-group-filled col-md-3 col-xs-12">
                        <label for="city">Tỉnh/TP</label>
                        <select id="city" class="form-control" name="city">
                            <option value="0">--Chọn Tỉnh/TP--</option>
                            <?
                            foreach ($cfg_city as $k => $v) {
                                echo    '<option value="' . $k . '"' . ($k == $city ? ' selected' : '') . '>' . $v . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group form-group-filled col-md-3 col-xs-12">
                        <label for="district">Quận/Huyện</label>
                        <select id="district" class="form-control" name="district">
                            <option value="0">--Chọn Quận/Huyện--</option>
                            <?
                            foreach ($list_district as $k => $v) {
                                echo    '<option value="' . $k . '"' . ($k == $district ? ' selected' : '') . '>' . $v . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group form-group-filled col-md-3 col-xs-12">
                        <label for="ward">Xã/Phường</label>
                        <select id="ward" class="form-control" name="ward">
                            <option value="0">--Chọn Xã/Phường--</option>
                            <?
                            foreach ($list_ward as $k => $v) {
                                echo    '<option value="' . $k . '"' . ($k == $ward ? ' selected' : '') . '>' . $v . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group form-group-filled col-md-3 col-xs-12">
                        <label>Người giới thiệu</label>
                        <input readonly disabled class="form-control" value="<?=$User->info['use_referral_from']?>" type="text"/>
                    </div>
                    <div class="form-group form-group-filled col-md-3 col-xs-12">
                        <label for="st_address">Địa chỉ</label>
                        <input id="address" name="address" class="form-control" value="<?=$address?>" type="text" />
                    </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group form-group-icon-left">
                        <div class="st-change-avatar">
                            <div class="user-profile-avatar user_seting">
                                <img width="75" height="75" class="avatar avatar-300 photo img-thumbnail" src="<?=$Router->srcUserAvatar($User->info['use_avatar'], SIZE_MEDIUM)?>" alt="<?=$User->info['use_name']?>">
                            </div>
                            <div class="st-title">
                                <p class="title" style="margin-top: 10px;">Đổi Avatar</p>
                                <p>JPG, JPEG, PNG</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="form-group form-group-icon-left" style="margin-top: 50px;">
                        <div class="custom-file">
                          <input type="file" class="custom-file-input" name="avatar" id="customFile">
                          <label class="custom-file-label" for="customFile">Chọn ảnh</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-icon-left">
                        <div class="st-line-bg"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-icon-left">
                        <input name="st_btn_update" type="submit" class="btn btn-primary" value="Cập nhật">
                        <input type="hidden" name="action" value="info" />
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="infor-st-setting" id="infor-st-setting" style="margin-bottom: 15px;">
        <form method="POST" action="#infor-st-setting">        
            <div class="row">
                <div class="col-md-12">
                    <h4>Đổi mật khẩu</h4>
                    <div class="msg">
                        <?
                        /** Nếu cập nhật thông tin thành công **/
                        $result_update  =   getValue('update_password', GET_STRING, GET_SESSION, '');
                        if ($result_update == 'success') {
                            unset($_SESSION['update_password']);
                            ?>
                            <div class="alert alert-success">
                                <button data-dismiss="alert" type="button" class="close"><span aria-hidden="true">×</span>
                                </button>
                                <p class="text-small">Đổi mật khẩu thành công</p>
                            </div>
                            <?
                        }
                        /** Nếu cập nhật thông tin lỗi **/
                        if (!empty($error_password)) {
                            ?>
                            <div class="alert alert-danger">
                                <button data-dismiss="alert" type="button" class="close"><span aria-hidden="true">×</span>
                                </button>
                                <?
                                foreach ($error_password as $e) {
                                    echo    '<p class="text-small">' . $e . '</p>';
                                }
                                ?>
                            </div>
                            <?
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group form-group-filled">
                        <label for="current_password">Mật khẩu hiện tại</label>
                        <input name="current_password" class="form-control" type="password" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group form-group-filled">
                        <label for="new_pass">Mật khẩu mới</label>
                        <input name="new_password" class="form-control" type="password" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group form-group-filled">
                        <label for="new_pass_again">Nhập lại mật khẩu mới</label>
                        <input name="new_password_confirm" class="form-control" type="password" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-filled">
                        <div class="st-line-bg"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-group-filled">
                        <input name="btn_update_pass" class="btn btn-primary" type="submit" value="Cập nhật" />
                        <input type="hidden" name="action" value="password" />
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>