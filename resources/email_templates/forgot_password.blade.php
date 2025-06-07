<div style="background-color: #fafafa; padding: 10px;">
    <div style="background-color: #fff;">
        <div style="padding: 0; margin: 0; width: 100%;">
            <div style="text-align: center; width: 100%; margin: 0 auto;">
                <img src="{{ $logo }}" width="100%">
            </div>
            <div style="padding: 10px 3%; font-size: 13px;">
                <p style="margin-top: 10px;">Xin chào <b>{{ $name }}</b>,</p>
                <p>Chúng tôi vừa nhận được một yêu cầu lấy lại mật khẩu cho tài khoản <b>{{ $email }}</b>.</p>
                <p style="line-height: 1.5">Nếu Quý khách là người gửi yêu cầu cho chúng tôi, vui lòng truy cập vào đường dẫn dưới đây để lấy lại mật khẩu. Nếu Quý khách không gửi yêu cầu cho chúng tôi, vui lòng bỏ qua Email này.</p>
                <p>Link lấy lại mật khẩu: <a href="{{ $link }}" target="_blank">{{ $link }}</a></p>

                <p style="border-bottom: 1px solid #ccc; margin: 20px 0 15px;"></p>
                <p style="margin: 5px 0; text-align: center; color: #777; font-weight: bold;">Copyright © {{ $brand_domain }}</p>
                <p style="margin: 5px 0; text-align: center; color: #777;">{{ $domain_web }} - {{ $email_contact }}</p>

                <p><i>Đây là email tự động, vui lòng không trả lời email này. Nếu Quý khách cần biết thêm thông tin, vui lòng liên hệ {{ $hotline ?? '' }}.</i></p>

                <p>Trân trọng cảm ơn!</p>
            </div>
        </div>
    </div>
</div>