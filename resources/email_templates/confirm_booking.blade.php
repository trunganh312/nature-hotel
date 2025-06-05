@extends('layout')

@section('content')
<div style="padding:10px 3%; font-size: 13px;">
<p style="margin-top:10px;">Xin chào <b>{{ $booking['bkho_name'] }}</b>,</p>
<p style="line-height: 1.5;">
    Đơn đặt phòng mã {{ $booking['bkho_code'] }} của Quý khách tại {{ $name }} đã được xác nhận thành công với các thông tin như sau:
</p>
<div style="width: 100%;background-color: #fff8dd;padding:8px;box-sizing: border-box;margin-bottom: 10px;">
    <div style="width:100%;display:inline-block;">
        <table cellspacing="0" cellpadding="3" width="100%" border="0">
            <tr>
                <td style="text-align:right; width: 100px;">Mã đơn đặt:</td>
                <td colspan="2" style="padding-left: 5px;"><b>{{ $booking['bkho_code'] }}</b></td>
            </tr>
            @if($isTA) 
            <tr>
                <td style="text-align:right;">Khách sạn:</td>
                <td colspan="2" style="padding-left: 5px;">{{ $booking['hot_name'] }}</td>
            </tr>
            @endif
            <tr>
                <td style="text-align:right;">Số người:</td>
                <td colspan="2" style="padding-left: 5px;">
                    {{ $booking['person'] }}
                </td>
            </tr>
            <tr>
                <td style="text-align:right;">Nhận phòng:</td>
                <td colspan="2" style="padding-left: 5px;">{{ $booking['checkin'] }}</td>
            </tr>
            <tr>
                <td style="text-align:right;">Trả phòng:</td>
                <td colspan="2" style="padding-left: 5px;">{{ $booking['checkout'] }}</td>
            </tr>
            @if(!empty($items)) 
            <tr>
                <td colspan="3">
                    <table style="border-collapse: collapse; border: 1px solid #e5e6e7; width: 100%;" cellpadding="3" cellspacing="0">
                        <tr>
                            <td colspan="3" style="text-align:center; border: 1px solid #e5e6e7;font-weight: bold">Dịch vụ</td>
                        </tr>
                        @foreach($items as $item)
                        <tr>
                            <td style="padding-left: 7px; text-align: right; width: 50%;">{{ $item['name'] }}</td>
                            <td style="padding: 0 3px; text-align: center;">&#10005;&nbsp;{{ $item['qty'] }}</td>
                            <td style="text-align: left; padding-right: 7px;">&#10005;&nbsp;{{ number_format($item['price']) }}₫</td>
                        </tr>
                        @endforeach
                    </table>
                </td>
            </tr>
            @endif
            <tr>
            <td style="text-align:right;">Tổng tiền:</td>
            <td style="text-align:right;width:75px;">{{ number_format($booking['bkho_money_total']) }}₫</td>
            <td></td>
            </tr>
            <tr>
                <td style="text-align:right;">Giảm trừ:</td><td style="text-align:right;">{{ number_format($booking['bkho_money_discount']) }}₫</td>
            </tr>
            <tr>
            <td style="border-top:1px solid #ccc;padding-top:10px;text-align:right;">Thanh toán:</td>
            <td style="font-weight:bold;text-align:right;border-top:1px solid #ccc;padding-top:10px;"><span>{{ number_format($booking['bkho_money_pay']) }}</span>₫</td>
            <td></td>
            </tr>
            <tr>
                <td style="border-top:1px solid #ccc;padding-top:10px;text-align:right;">Đã thanh toán:</td>
                <td style="font-weight:bold;text-align:right;border-top:1px solid #ccc;padding-top:10px;"><span>{{ number_format($booking['bkho_money_received']) }}</span>₫</td>
                <td></td>
            </tr>
            <tr>
                <td style="border-top:1px solid #ccc;padding-top:15px;text-align:right;">Còn thiếu:</td>
                <td style="font-weight:bold;text-align:right;border-top:1px solid #ccc;padding-top:15px;"><span style="color:#f12f23;">{{ number_format($booking['bkho_money_pay'] - $booking['bkho_money_received']) }}</span>₫</td>
                <td></td>
            </tr>
        </table>
    </div>
    <div style="width:100%; display:inline-block;">
        <table cellspacing="0" cellpadding="3" width="100%" border="0" style="border-top:1px solid #ccc;padding-top: 10px;padding-bottom:0px;margin-top:7px;">
            <tr>
            <td colspan="2"><b>Thông tin người đặt:<b></td>
            </tr>
            <tr>
                <td style="width:70px;">Họ tên:</td>
                <td>{{ $booking['bkho_name'] }}</td>
            </tr>
            <tr>
                <td>Email:</td>
                <td>{{ $booking['bkho_email'] }}</td>
            </tr>
            <tr>
                <td>Điện thoại:</td>
                <td>{{ $booking['bkho_phone'] }}</td>
            </tr>
        </table>
    </div>
</div>
<b style="margin-top: 5px">Ghi chú:</b> {{ $booking['note'] }}
<p style="margin-bottom:2px; font-weight:bold;">Chính sách hủy: </p>
{!! $policy_cancel !!}
<b>Nhân viên tư vấn:</b> {{ $sale['name'] }}
<br>
<b>Điện thoại:</b> {{ $sale['phone'] }}
<p style="line-height: 1.5;">Rất hân hạnh được đón tiếp và chúc Quý khách có một chuyến đi vui vẻ!</p>
<p style="line-height: 1.5;border-bottom:1px solid #ccc;padding-bottom: 20px;">Trân trọng cảm ơn!</p>
@endsection