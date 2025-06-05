<?
if (empty($_SESSION['vg_sent_request']) && strpos($_SERVER['REQUEST_URI'], '/checkout/') === false) {
    ?>
    <div class="modal fade" id="st-support-form" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document" style="max-width: 450px;">
            <div class="modal-content relative">
                <div class="loader-wrapper">
                    <div class="st-loader"></div>
                </div>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                        <i class="input-icon field-icon far fa-times"></i>
                    </button>
                    <h4 class="modal-title">Quý khách chưa tìm thấy dịch vụ mình cần? Vui lòng gửi yêu cầu đặt dịch vụ riêng tại đây, chúng tôi sẽ liên hệ lại ngay để tư vấn.</h4>
                </div>
                <div class="modal-body relative">
                    <form action="" class="form" method="post">
                        <div class="form-group">
                            <input type="text" class="form-control" value="<?=$User->logged ? $User->info['use_name'] : '' ?>" name="fullname" autocomplete="off" placeholder="Họ và tên *">
                            <i class="input-icon field-icon fa fa-info"></i>
                        </div>
                        <p class="text-danger hide" data-name="fullname"></p>
                        <div class="form-group">
                            <input type="text" class="form-control" value="<?=$User->logged ? convert_phone_number($User->info['use_phone']) : '' ?>" name="phone" autocomplete="off" placeholder="Số điện thoại *">
                            <i class="input-icon field-icon fa fa-phone"></i>
                        </div>
                        <p class="text-danger hide" data-name="phone"></p>
                        <div class="form-group">
                            <textarea rows="4" style="height: auto;" class="form-control" name="note" autocomplete="off" placeholder="Nội dung yêu cầu *"></textarea>
                        </div>
                        <p class="text-danger hide" data-name="note"></p>
                        <div class="form-group">
                            <input type="submit" name="submit" class="form-submit btn btn-primary" value="Gửi yêu cầu">
                        </div>
                        <input type="hidden" name="url" value="<?=base64_encode(htmlspecialchars(DOMAIN_WEB . $_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8'));?>" />
                    </form>
                    <div class="message-success hide">
                        <p>Cảm ơn Quý khách đã gửi yêu cầu dịch vụ cho Vietgoing, bộ phận chăm sóc khách hàng của chúng tôi sẽ liên hệ lại ngay với Quý khách để tư vấn!</p>
                        <div class="form-group">
                            <button name="submit" class="form-submit" data-dismiss="modal">Đóng cửa sổ</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?
}
?>