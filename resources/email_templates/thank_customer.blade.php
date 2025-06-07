@extends('layout')

@section('content')
<div style="padding:10px 3%;font-size:13px">
    <p style="margin-top:10px">Xin chào <b>{{ $booking['bkho_name'] }}</b>,</p>
    <p style="line-height:1.5">Cảm ơn Quý khách đã lựa chọn sử dụng dịch vụ của {{ $name }}!</p>
    @if($vote_link)
    <p style="line-height:1.5">Nếu Quý khách hài lòng với dịch vụ của chúng tôi, xin vui lòng dành ít phút gửi cho chúng tôi những đánh giá tích cực để chúng tôi có thêm cơ hội được đem dịch vụ của mình đến với nhiều khách hàng hơn.</p>
    <p style="line-height: 1.5;">Link đánh giá: <a href="{{ $link }}" target="_blank">Đánh giá dịch vụ</a>.</p>
    @endif
    <p style="line-height: 1.5;">Nếu Quý khách chưa thực sự hài lòng về sản phẩm hoặc dịch vụ của chúng tôi, xin vui lòng chia sẻ cho chúng tôi những điều mà chúng tôi còn thiếu sót bằng cách phản hồi tại Email này, chúng tôi luôn sẵn sàng lắng nghe tất cả các ý kiến đóng góp quý báu của Quý khách để cải thiện dịch vụ của mình được tốt hơn.</p>
    <p style="line-height:1.5"><b>Nhân viên tư vấn:</b> {{ $sale['name'] }}<br><b>Điện thoại:</b> {{ $sale['phone'] }}</p>
    <p style="line-height:1.5; border-bottom:1px solid #ccc;padding-bottom: 20px;" >Trân trọng cảm ơn!</p>
</div>
@endsection