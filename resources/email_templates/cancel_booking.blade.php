@extends('layout')

@section('content')
<div style="padding:10px 3%;font-size:13px">
    <p style="margin-top:10px">Xin chào <b>{{ $booking['bkho_name'] }}</b>,</p>
    <p style="line-height:1.5">Đơn đặt dịch vụ mã <b>#{{ $booking['bkho_code'] }}</b> của Quý khách đã được hủy trên hệ thống của {{ $name }}!</p>
    <p style="line-height:1.5">Chúng tôi rất lấy làm tiếc vì chưa có cơ hội được phục vụ Quý khách ở lần này, rất mong sẽ được đồng hành cùng Quý khách trong những chuyến đi lần sau. Nếu Quý khách cần tư vấn hoặc hỗ trợ thêm các thông tin khác, vui lòng liên hệ Hotline của {{ $name }} {{ $hotline }}.</p>
    <p style="line-height:1.5"><b>Nhân viên tư vấn:</b> {{ $sale['name'] }}<br><b>Điện thoại:</b> {{ $sale['phone'] }}</p>
    <p style="line-height:1.5;border-bottom:1px solid #ccc;padding-bottom: 20px;">Trân trọng cảm ơn!</p>
</div>
@endsection