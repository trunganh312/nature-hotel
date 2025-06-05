<?
include('data_checkout.php');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?=$Layout->loadHead()?>
</head>
<body class="header_white page_checkout page_checkout_hotel page-template-default page st-header-2 woocommerce-page">
    <?
    include($path_root . 'layout/inc_header.php');
    ?>
    
    <?
    if ($notfound) {
        ?>
        <div id="st-content-wrapper">
            <div class="container">
                <div class="box_notfound">
                    <?
                    if (!empty($hotel_info['hot_name'])) {
                        ?>
                        <p><strong><?=$hotel_info['hot_name']?></strong> đang được Vietgoing cập nhật thêm thông tin nhằm đem lại những dịch vụ và trải nghiệm tốt hơn cho Quý khách hàng!</p>
                        <?
                    } else {
                        ?>
                        <p>Quý khách chưa lựa chọn Khách sạn để đặt, hoặc Khách sạn mà Quý khách lựa chọn đặt đang được chúng tôi cập nhật thêm thông tin!</p>
                        <?
                    }
                    include('../../../layout/inc_not_found.php');
                    ?>
                </div>
            </div>
        </div>
        <?
    } else {
        include('view_checkout.php');
    }
    ?>
    
    <?
    include($path_root . 'layout/inc_footer.php');
    ?>
    
    <?=$Layout->loadFooter()?>
    <script type="text/javascript">

        document.addEventListener("touchstart", function() {}, false);

        $("#change-date").click(function() {
            $("#change-date .vietgoing-check-in-out").click();
        });

        $("#change-date .vietgoing-check-in-out").change(function() {
            setTimeout(function() {
                update_price();
            }, 100);
        });

        $(".coupon-section button").click(function() {
            setTimeout(function() {
                update_price();
            }, 100);
        });

        $("#need_invoice").change(function() {
            if(this.checked) {
                $('#invoice_info').show();
                $('#invoice').val(1);
            } else {
                $('#invoice_info').hide();
                $('#invoice').val(0);
            }
        });

        function update_price() {
            //history.pushState({}, null, '/checkout/hotel/');
            const params = new URLSearchParams();
            
            params.append('checkin', $(".parent_calendar input[name=checkin]").val());
            params.append('checkout', $(".parent_calendar input[name=checkout]").val());

            <?
            if (isset($room_param) && !empty($room_param)) {
                foreach ($room_param as $k => $v) {
                    ?>
                    params.append('room[<?=$k?>]', <?=$v?>);
                    <?
                }
            }
            ?>
            params.append('adult', $('#adult').val());
            params.append('child', $('#children').val());
            params.append('baby', $('#baby').val());

            $('#field-voucher_code').removeClass('error'); 
            $('.coupon-section .text-danger').hide();
            
            $.post('/checkout/hotel/?' + params.toString(), {

                action: 'ajax',
                voucher: $('#field-voucher_code').val(),
                email: $('#field-st_email').val()

            }, function(res) {

                if('total_money' in res) {

                    if (res.error) {
                        $('#field-voucher_code').addClass('error');
                        $('.coupon-section .text-danger').text(res.error).show();
                    }

                    $("#total_money").text(res.total_money);
                    $(".payment-amount .value").text(res.money_pay);
                    $(".money_discount .value").text(res.money_discount);

                    if (res.promotion_discount > 0) {
                        $(".promotion_discount .value").text(format_money(res.promotion_discount));
                        $(".promotion_discount").show();
                    } else {
                        $(".promotion_discount").hide();
                    }

                    for(let i in res.rooms) {
                        let row = res.rooms[i];
                        $(`.service-section[data-id=${row.roo_id}] .show-price`).html(`${row.qty} phòng <i class="fal fa-times"></i> ${res.count_night} đêm${row.price_total_vnd}`);
                    }
                    
                    if(res.isOTA) {
                        // Số người lớn
                        $("#adult").val(res.adult_number);
                        // Max người lớn
                        $("#adult").attr("max", res.max_adult_number);
                        // Min người lớn
                        $("#adult").attr("min", res.min_adult_number);

                        // Số trẻ em
                        $("#children").val(res.child_number);
                        // Max trẻ em
                        $("#children").attr("max", res.max_children_number);

                        // Số em bé
                        $("#baby").val(res.baby_number);
                        // Max em bé
                        $("#baby").attr("max", res.max_baby_number);
                    }

                } else {
                    location.reload();
                }
            }, 'json');
        }
        
    </script>
    
    <style type="text/css">
        #change-date{
            position: relative;
            cursor: pointer;
        }
        #change-date>input{
            z-index: -1;
        }
    </style>
</body>
</html>
