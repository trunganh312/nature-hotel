<?

use src\Models\MailTemplate;
use src\Models\NotifyTemplate;

include('../../Core/Config/require_crm.php');
require_once(PATH_CORE . '/Model/DataLevelModel.php');

//Đường dẫn lưu ảnh
$path_image_company     =   PATH_ROOT . '/static/image/company/';
$path_image_department  =   PATH_ROOT . '/static/image/department/';
$path_image_admin       =   PATH_ROOT . '/static/image/admin/';
$path_image_user        =   PATH_ROOT . '/static/image/user/';
$path_image_cccd        =   PATH_ROOT . '/static/image/cccd/';

//Resize avatar của member và department
$array_resize   =   [
    SIZE_MEDIUM =>  ['maxwidth' => 450, 'maxheight' => 450],
    SIZE_SMALL  =>  ['maxwidth' => 150, 'maxheight' => 150]
];

$cfg_email_default = [
    MailTemplate::HEADER => [
        'title' => 'Header Email',
        'content' => '<figure class="image"><img style="aspect-ratio:1200/90;" src="https://demo-static.sennet.vn/image/ckeditor/image_glx2503188744.webp" width="1200" height="90"></figure>'
    ],
    MailTemplate::FOOTER => [
        'title' => 'Footer Email',
        'content' => '<p style="text-align:center;">Copyright © SENNET</p><p style="text-align:center;">https://sennet.vn - info@sennet.vn</p>'
    ],
    MailTemplate::TEST   => [
        'title' => 'Gửi Email thử nghiệm',
        'content' => '<p>Gửi email từ hệ thống SENNET.</p>'
    ],
    MailTemplate::BOOKING_CANCEL => [
        'title' => 'Email thông báo hủy đơn đặt phòng tại SENNET Hotel',
        'content' => '<p>Xin chào&nbsp;{{customer.fullname}},</p><p>&nbsp;</p><p>Đơn đặt dịch vụ mã #{{booking.code}}&nbsp;của Quý khách đã được hủy trên hệ thống khách sạn SENNET Hotel của chúng tôi!</p><p>&nbsp;</p><p>Chúng tôi rất lấy làm tiếc vì chưa có cơ hội được phục vụ Quý khách ở lần này, rất mong sẽ được đồng hành cùng Quý khách trong những chuyến đi lần sau. Nếu Quý khách cần tư vấn hoặc hỗ trợ thêm các thông tin khác, vui lòng liên hệ Hotline của SENNET 0912 606 135.</p><p>&nbsp;</p><p>Nhân viên tư vấn: SENNET<br>Điện thoại: 0912 606 135</p><p>&nbsp;</p><p>Trân trọng cảm ơn!</p>'
    ],
    MailTemplate::THANK_CUSTOMER   => [
        'title' => 'Cảm ơn Quý khách đã sử dụng dịch vụ tại SENNET Hotel',
        'content' => '<p>Xin chào {{customer.fullname}},</p><p><br>Cảm ơn Quý khách đã lựa chọn sử dụng dịch vụ của SENNET Hotel!</p><p><br>Nếu Quý khách hài lòng với dịch vụ của chúng tôi, xin vui lòng dành ít phút gửi cho chúng tôi những đánh giá tích cực để chúng tôi có thêm cơ hội được đem dịch vụ của mình đến với nhiều khách hàng hơn.</p><p><br>Nếu Quý khách chưa thực sự hài lòng về sản phẩm hoặc dịch vụ của chúng tôi, xin vui lòng chia sẻ cho chúng tôi những điều mà chúng tôi còn thiếu sót bằng cách phản hồi tại Email này, chúng tôi luôn sẵn sàng lắng nghe tất cả các ý kiến đóng góp quý báu của Quý khách để cải thiện dịch vụ của mình được tốt hơn.</p><p><br>Các dịch vụ đã sử dụng của đơn #{{booking.code}}:</p><p>Khách sạn: {{info.hotel_name}}</p><p>Nhân viên tư vấn: {{sale.name}}<br>Điện thoại: {{sale.phone}}</p><p>&nbsp;</p><p>Trân trọng cảm ơn!</p>'
    ],
    MailTemplate::CONFIRM_PAYMENT => [
        'title' => 'Xác nhận thanh toán đơn đặt phòng tại SENNET Hotel',
        'content' => '<p>Xin chào {{customer.fullname}},</p><p>&nbsp;</p><p>Cảm ơn Quý khách đã lựa chọn sử dụng dịch vụ tại SENNET Hotel!</p><p>Chúng tôi xác nhận thông tin thanh toán cho đơn đặt dịch vụ mã #{{booking.code}} của Quý khách như sau:</p><p>Tổng tiền: {{booking.money_pay}}₫</p><p>Đã thanh toán: {{booking.money_received}}₫</p><p>Còn thiếu: {{booking.money_debt}}₫</p><p>Nhân viên tư vấn: {{sale.name}}<br>Điện thoại: {{sale.phone}}</p><p>&nbsp;</p><p>Trân trọng cảm ơn!</p>'
    ],
    MailTemplate::BOOKING_CONFIRM     => [
        'title' => 'Thông tin đơn đặt phòng tại SENNET',
        'content' => '<p>Xin chào <strong>{{customer.fullname}},</strong></p><p>&nbsp;</p><p>Chúng tôi xin gửi thông tin đặt phòng mã <strong>#{{booking.code}}</strong> của Quý khách tại SENNET Hotel như sau:</p><figure class="table"><table><tbody><tr><td style="text-align:right;width:95px;">Mã đơn đặt:</td><td style="padding-left:5px;" colspan="2"><strong>{{booking.code}}</strong></td></tr><tr><td style="text-align:right;">Khách sạn:</td><td style="padding-left:5px;" colspan="2"><strong>{{hotel.name}}</strong></td></tr><tr><td style="text-align:right;">Số người:</td><td style="padding-left:5px;" colspan="2"><strong>{{booking.person}}</strong></td></tr><tr><td style="text-align:right;">Nhận phòng:</td><td style="padding-left:5px;" colspan="2"><strong>{{booking.checkin}}</strong></td></tr><tr><td style="text-align:right;">Trả phòng:</td><td style="padding-left:5px;" colspan="2"><strong>{{booking.checkout}}</strong></td></tr><tr><td colspan="3">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; {{booking.rooms}}</td></tr><tr><td style="text-align:right;">Tổng tiền:</td><td style="text-align:right;width:75px;">{{booking.money_total}}₫</td><td>&nbsp;</td></tr><tr><td style="text-align:right;">Giảm trừ:</td><td style="text-align:right;">{{booking.money_discount}}₫</td><td>&nbsp;</td></tr><tr><td style="border-top:1px solid #ccc;padding-top:10px;text-align:right;">Thanh toán:</td><td style="border-top:1px solid #ccc;padding-top:10px;text-align:right;"><strong>{{booking.money_pay}}₫</strong></td><td>&nbsp;</td></tr><tr><td style="padding-bottom:10px;text-align:right;">Đã thanh toán:</td><td style="padding-bottom:10px;text-align:right;">{{booking.money_received}}₫</td><td>&nbsp;</td></tr><tr><td style="border-top:1px solid #ccc;padding-top:15px;text-align:right;">Còn thiếu:</td><td style="border-top:1px solid #ccc;padding-top:15px;text-align:right;"><strong>{{booking.money_debt}}₫</strong></td><td>&nbsp;</td></tr></tbody></table></figure><figure class="table"><table style="border-top:1px solid #ccc;"><tbody><tr><td colspan="2"><strong>Thông tin người đặt:</strong></td></tr><tr><td style="width:70px;">Họ tên:</td><td>{{customer.fullname}}</td></tr><tr><td>Email:</td><td>{{customer.email}}</td></tr><tr><td>Điện thoại:</td><td>{{customer.phone}}</td></tr><tr><td>Địa chỉ:</td><td>{{customer.address}}</td></tr></tbody></table></figure><p>&nbsp;</p><p><strong>Ghi chú:</strong> {{booking.vat}}.</p><p>&nbsp;</p><p><strong>Chính sách hủy:</strong></p><p style="margin-left:0;">{{hotel.policy_cancel}}</p><p style="margin-left:0;">&nbsp;</p><p><strong>Nhân viên tư vấn:</strong> {{sale.name}}<br><strong>Điện thoại:</strong> {{sale.phone}}</p><p>&nbsp;</p><p>Đây là thông tin đơn đặt phòng, chưa phải Email xác nhận đặt phòng thành công. Quý khách vui lòng thanh toán theo hướng dẫn của nhân viên tư vấn để chúng tôi hoàn thành việc xác nhận đơn đặt phòng cho Quý khách.</p><p>&nbsp;</p><p>Trân trọng cảm ơn!</p>'
    ],
    MailTemplate::BOOKING_SUCCESS => [
        'title' => 'Xác nhận đơn đặt phòng thành công tại SENNET Hotel',
        'content' => '<p>Xin chào <strong>{{customer.fullname}},</strong></p><p>&nbsp;</p><p>Đơn đặt phòng mã <strong>#{{booking.code}}</strong> của Quý khách tại SENNET Hotel đã được xác nhận thành công với các thông tin như sau:</p><figure class="table"><table><tbody><tr><td style="text-align:right;width:95px;">Mã đơn đặt:</td><td style="padding-left:5px;" colspan="2"><strong>{{booking.code}}</strong></td></tr><tr><td style="text-align:right;">Khách sạn:</td><td style="padding-left:5px;" colspan="2"><strong>{{hotel.name}}</strong></td></tr><tr><td style="text-align:right;">Số người:</td><td style="padding-left:5px;" colspan="2"><strong>{{booking.person}}</strong></td></tr><tr><td style="text-align:right;">Nhận phòng:</td><td style="padding-left:5px;" colspan="2"><strong>{{booking.checkin}}</strong></td></tr><tr><td style="text-align:right;">Trả phòng:</td><td style="padding-left:5px;" colspan="2"><strong>{{booking.checkout}}</strong></td></tr><tr><td colspan="3">&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; {{booking.rooms}}</td></tr><tr><td style="text-align:right;">Tổng tiền:</td><td style="text-align:right;width:75px;">{{booking.money_total}}₫</td><td>&nbsp;</td></tr><tr><td style="text-align:right;">Giảm trừ:</td><td style="text-align:right;">{{booking.money_discount}}₫</td><td>&nbsp;</td></tr><tr><td style="border-top:1px solid #ccc;padding-top:10px;text-align:right;">Thanh toán:</td><td style="border-top:1px solid #ccc;padding-top:10px;text-align:right;"><strong>{{booking.money_pay}}₫</strong></td><td>&nbsp;</td></tr><tr><td style="padding-bottom:10px;text-align:right;">Đã thanh toán:</td><td style="padding-bottom:10px;text-align:right;">{{booking.money_received}}₫</td><td>&nbsp;</td></tr><tr><td style="border-top:1px solid #ccc;padding-top:15px;text-align:right;">Còn thiếu:</td><td style="border-top:1px solid #ccc;padding-top:15px;text-align:right;"><strong>{{booking.money_debt}}₫</strong></td><td>&nbsp;</td></tr></tbody></table></figure><figure class="table"><table style="border-top:1px solid #ccc;"><tbody><tr><td colspan="2"><strong>Thông tin người đặt:</strong></td></tr><tr><td style="width:70px;">Họ tên:</td><td>{{customer.fullname}}</td></tr><tr><td>Email:</td><td>{{customer.email}}</td></tr><tr><td>Điện thoại:</td><td>{{customer.phone}}</td></tr><tr><td>Địa chỉ:</td><td>{{customer.address}}</td></tr></tbody></table></figure><p>&nbsp;</p><p><strong>Ghi chú:</strong> {{booking.vat}}.</p><p>&nbsp;</p><p><strong>Chính sách hủy:</strong></p><p style="margin-left:0;">{{hotel.policy_cancel}}</p><p style="margin-left:0;">&nbsp;</p><p><strong>Nhân viên tư vấn:</strong> {{sale.name}}<br><strong>Điện thoại:</strong> {{sale.phone}}</p><p>&nbsp;</p><p>Rất hân hạnh được đón tiếp và chúc Quý khách có một chuyến đi vui vẻ!</p><p>&nbsp;</p><p>Trân trọng cảm ơn!</p>'
    ]
];

$cfg_telegram_default = [
    NotifyTemplate::ROOM_ITEM_STATUS => [
        'id' => '-112233',
        'content' => 'Phòng <b>{{room}}</b> (Booking #{{booking.code}}) vừa được chuyển sang trạng thái <b>{{status_now}}</b>. Người thực hiện: {{user.fullname}}, ĐT: {{user.phone}}.'
    ],
    NotifyTemplate::TEST => [
        'id' => '-112233',
        'content' => 'Thông báo thử nghiệm gửi thông báo qua Telegram từ SENNET Hotel.'
    ],
    NotifyTemplate::PAYEMNT_INCOME => [
        'id' => '-112233',
        'content' => 'Có khoản thu tiền mới từ kế toán: Số tiền: {{amount}}đ.Nội dung: {{content}}.'
    ],
    NotifyTemplate::SEND_BOOKING => [
        'id' => '-112233',
        'content' => '<b>{{name}}</b> đã {{status}} đơn đặt phòng <b>{{code}}</b>.'
    ],
    NotifyTemplate::REQUEST_NEW => [
        'id' => '-112233',
        'content' => 'Có Request mới cần tư vấn đặt phòng! Hoặc: Có Request mới cần tư vấn: Khách hàng: {{request.name}}Điện thoại: {{request.phone}} Nội dung: {{request.content}} Kênh liên hệ: {{request.source}}'
    ],
    NotifyTemplate::UPDATE_BOOKING_PARTNER => [
        'id' => '-112233',
        'content' => 'Nội dung cố định theo hệ thống SENNET.'
    ],
];

?>