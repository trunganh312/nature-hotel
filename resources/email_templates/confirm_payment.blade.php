@extends('layout')

@section('content')
<div style="padding:10px 3%;font-size:13px">
    <p style="margin-top:10px">Xin chào <b>{{ $booking['bkho_name'] }}</b>,</p>
    <p style="line-height:1.5">Cảm ơn Quý khách đã lựa chọn sử dụng dịch vụ tại {{ $name }}!</p>
    <p style="line-height:1.5">Chúng tôi xác nhận thông tin thanh toán cho đơn đặt dịch vụ mã <b>#{{ $booking['bkho_code'] }}</b> của Quý khách như sau:</p>
    <div style="width:100%;background-color:#fff8dd">
        <table cellspacing="0" cellpadding="3" width="100%" border="0">
            <tbody>
                <tr>
                    <td style="vertical-align:top">
                        <table cellspacing="0" cellpadding="5" width="300px;" border="0">
                            <tbody>
                                <tr>
                                    <td style="width:130px">Tổng tiền:</td>
                                    <td style="font-weight:bold;text-align:right"><span style="color:#f12f23">{{ number_format($booking['bkho_money_pay']) }}</span>₫</td>
                                </tr>
                                <tr>
                                    <td>Đã thanh toán:</td>
                                    <td style="font-weight:bold;text-align:right"><span style="color:#f12f23">{{ number_format($booking['bkho_money_received']) }}</span>₫</td>
                                </tr>
                                <tr>
                                    <td style="border-top:1px solid">Còn thiếu:</td>
                                    <td style="font-weight:bold;text-align:right;border-top:1px solid"><span style="color:#f12f23">{{ number_format($booking['bkho_money_pay'] - $booking['bkho_money_received']) }}</span>₫</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <p style="line-height:1.5"><b>Nhân viên tư vấn:</b> {{ $sale['name'] }}<br><b>Điện thoại:</b> {{ $sale['phone'] }}</p>
    <p style="line-height:1.5;padding-bottom: 20px;border-bottom:1px solid #ccc;">Trân trọng cảm ơn!</p>
</div>
@endsection