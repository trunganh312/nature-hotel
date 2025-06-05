<?
include('data_detail.php');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?=$Layout->loadHead()?>
</head>
<body class="page_hotel_detail header_white st_hotel-template-default single single-st_hotel st-header-2">
    <?
    include($path_root . 'layout/inc_header.php');
    ?>
    
    <?
    include('view_detail.php');
    if ($hotel_info['hot_active'] == 1) $table_of_content->render();
    ?>
    
    <?
    include($path_root . 'layout/inc_footer.php');
    ?>
    <?=$schema_html?>
    <?=$Layout->loadFooter()?>
    
    <script>
        
        $(function() {
            var self_modal_show_room    = null;
            var modal_show_room_qty     = 0;
            $(document).on('click', '.show-room-detail', function(e) {
                e.preventDefault();

                // Lưu lại element click hiện tại để khi đóng modal còn giảm tồn
                self_modal_show_room    = $(this).parents('.room-item');
                const room_id = self_modal_show_room.data('id');
                modal_show_room_qty     = self_modal_show_room.find('input[name="room_qty"]').val();
                //console.log(modal_show_room_qty);
                // Tự động cộng thêm một vào sl
                // Nếu chưa có sl thì mới update thêm 1 để khi đặt ngay sẽ có phòng hiện tại
                if (modal_show_room_qty < 1) {
                    self_modal_show_room.find('.bound_choose_qty').find('.next').click();
                }

                let name = $(this).parents(".form-booking-inpage").find(".heading").text();
                $("#st-modal-show-room .modal-title").text(name);
                let url_room = `/ajax/get_room.php?id=${room_id}` +'&qty='+ self_modal_show_room.find('input[name=room_qty]').val() + '&utm_web=' + $(this).data('box');
                $.get(url_room, function( data ) {
                    $("#st-modal-show-room .modal-body").html(data);
                    $("#st-modal-show-room .st-gallery", $("body")).each(function () {
                        var parent = $(this);
                        var $fotoramaDiv = $(".fotorama", parent).fotorama({ width: parent.data("width"), nav: parent.data("nav"), thumbwidth: "135", thumbheight: "135", allowfullscreen: parent.data("allowfullscreen") });
                        parent.data("fotorama", $fotoramaDiv.data("fotorama"));
                    });

                    init_input_number($("#st-modal-show-room .st-number-wrapper"));
                    
                    setTimeout(function() {
                        $("#st-modal-show-room").modal();
                        $('#st-modal-show-room .modal-content').click();
                    }, 100);

                });
                $("body").css('overflow-y', 'hidden');
            });

            $('#st-modal-show-room').on('hidden.bs.modal', function () {
                $("body").css('overflow-y', 'auto');
                // Trừ đi 1 nếu sl được cộng do mở modal
                if (modal_show_room_qty < 1 && self_modal_show_room.find('input[name="room_qty"]').val() <= 1) {
                    self_modal_show_room.find('.bound_choose_qty').find('.prev').click();
                }
            });

            // Xử lý event trên modal room detail
            $("#st-modal-show-room").on('click', '.bound_choose_qty .next', function() {
                let room_id = $(this).parents(".page-room-hotel").data('room_id');
                $(`#room-${room_id} .bound_choose_qty .next`).click();
            });
            $("#st-modal-show-room").on('click', '.bound_choose_qty .prev', function() {
                let room_id = $(this).parents(".page-room-hotel").data('room_id');
                $(`#room-${room_id} .bound_choose_qty .prev`).click();
            });
            
            $("#st-modal-show-room").on("click", ".quick_button span",function() { 
                $("#st-modal-show-room .close").click();
            });
            
            $("#st-modal-show-room").on("click", ".quick_button button", function() { 
                $('#form-booking-inpage').submit();
            });

            $('.st-list-rooms').on('change', '.room-item .st-number-wrapper .st-input-number', function() {
                const qty   = $(this).val();
                const key   = $(this).parents('.room-item').attr('id');
                const id    = $(this).parents('.room-item').data('id');
                const title = $(this).parents('.room-item').find('.heading span').text();
                
                if (qty < 1) {
                    
                    //Nếu ko còn phòng này được chọn thì ẩn box đi
                    if($('.rooms-select tr').length <= 1 && $(`.rooms-select tr.${key}`).length) {
                        $(".wp-rooms-select").hide();
                        $(".wp-room_number input").prop('disabled', false);
                        $('.mb_room_selected').hide();
                    }
                    
                    //Ẩn phòng đi
                    $(`.rooms-select tr.${key}`).remove();
                    $(`.mb_room_selected p.${key}`).remove();
                    
                } else if ($(`.rooms-select tr.${key}`).length) {
                    
                    //Nếu phòng được chọn đang có rồi thì cộng thêm số lượng
                    $(`.rooms-select tr.${key} td:last-child`).html('x&nbsp;' + qty);
                    $(`.rooms-select tr.${key} input[name="room[${id}]"]`).val(qty);
                    $(`.mb_room_selected p.${key} .mb_qty`).text(qty);
                    
                } else {
                    
                    //Nếu chọn lần đầu thì show box ra
                    $(".wp-rooms-select").show();
                    $(".wp-room_number input").prop('disabled', true);
                    $('.mb_room_selected').show();
                    
                    $('.rooms-select table').append(`<tr class="${key}">
                                                    <td>
                                                        <a href="#${key}">${title}</a>
                                                        <input type="hidden" name="room[${id}]" value="${qty}" class="manual_selected" data-id="${id}" />
                                                    </td>
                                                    <td>x&nbsp;${qty}</td>
                                                </tr>`);
                    $('.mb_room_selected').append('<p class="' + key + '"><span class="mb_qty">' + qty + '</span><i class="fal fa-times"></i>' + title);
                }
                
                //Set bottom cho các nút call để ko bị che đi nút Đặt ngay
                var _length_room = parseInt($('.mb_room_selected p').length);
                if (_length_room > 0) {
                    $('#footer_contact, #back_top').css('bottom', (130 + _length_room * 20) + 'px');
                } else {
                    $('#footer_contact, #back_top').css('bottom', '105px');
                }
            });

            // Khi scroll chi tiet phong trên mobile thì gắn nút close lên góc màn hình
            if(window.matchMedia("only screen and (max-width: 760px)").matches) {
                $('#st-modal-show-room').scroll(function (event) {
                    var scroll = $('#st-modal-show-room').scrollTop();
                    if(scroll > 60) {
                        $('.modal-body>button.close').addClass('sticky');
                    } else {
                        $('.modal-body>button.close').removeClass('sticky');
                    }
                });
            }

            // Chuyển ảnh khi nhấn phím trái phải
            $(document).keydown(function(e) {

                // Nếu đang xem mode fullscreen thì bỏ qua
                let fotorama = $('body>.fotorama').data('fotorama');
                if (typeof fotorama !== 'undefined') return;
                
                // Kiểm tra xem phần tử đó có nằm trong #st-modal-show-room
                let target = $(e.target).closest('#st-modal-show-room');
                if (target !== null && target.length) {
                    fotorama = $('#st-modal-show-room .st-gallery .fotorama').data('fotorama');
                } else {
                    fotorama = $('#box_hotel_gallery .fotorama').data('fotorama');
                }

                if (typeof fotorama == 'undefined' || typeof fotorama.activeIndex == 'undefined') return;

                let keyCode = e.keyCode || e.which;
                if (keyCode === 37) { // Left
                    fotorama.show(fotorama.activeIndex - 1);
                } else if (keyCode === 39) { // Right
                    fotorama.show(fotorama.activeIndex + 1);
                }
            });
        });

        function reload_rooms(start, end) {
            goto_box('#box_list_room');

            $(".st-list-rooms .loader-wrapper").show();
            $(".st-list-rooms>.fetch").load(`/ajax/get_rooms.php?id=<?=$hotel_id ?>&checkin=${start}&checkout=${end}`,
            function () {
                // Xóa phòng đã chọn
                // $(".wp-rooms-select").hide();
                // $(".wp-rooms-select .rooms-select table>tr").remove();

                // Update lại số lượng vào phòng đã chọn
                $('.rooms-select .manual_selected').each((i, e)=> {
                    const data_id = $(e).data('id');
                    const qty = $(e).val();
                    const max_qty = $(`#room-${data_id} input[name="room_qty"]`).data('max');
                    const displayVal = $(`#room-${data_id} .bound_choose_qty .name_phong span`).text().indexOf('/') !== -1 
                        ? qty + '/' + max_qty 
                        : qty;

                    $(`#room-${data_id} input[name="room_qty"]`).val(qty);
                    $(`#room-${data_id} .bound_choose_qty .name_phong span`).text(displayVal);
                });

                // Update lại phòng có giá nhỏ nhất
                let room_min = {
                    id: $(".st-list-rooms .room-item:nth-child(1)").data('id'),
                    price: $(".st-list-rooms .room-item:nth-child(1) .price-wrapper .price").text(),
                    price_promotion: $(".st-list-rooms .room-item:nth-child(1) .price-wrapper .price_public").text(),
                };
                
                $(".wp-room_number input:nth-child(1)").attr('name', `room[${room_min.id}]`);
                $(".wp-room_number input:nth-child(2)").val(room_min.id);
                if(room_min.price != '') {
                    $('.form-book-wrapper .price .item').text(`${room_min.price}/đêm`)
                }else {
                    $('.form-book-wrapper .price .item').text('Đặt để nhận báo giá ngay lập tức!')
                }
                $('.form-book-wrapper .price .onsale').text(room_min.price_promotion ? room_min.price_promotion : '');

                // Làm mới style button
                $(".st-list-rooms .st-number-wrapper").each(function () {
                    var timeOut = 0;
                    var t = $(this);
                    var input = t.find(".st-input-number");
                    //input.after('<span class="next"><i class="fal fa-plus"></i></span>');
                    //input.before('<span class="prev"><i class="fal fa-minus"></i></span>');
                    var min = input.data("min");
                    var max = input.data("max");
                    t.find("span").on("click", function () {
                        var $button = $(this);
                        numberButtonFunc($button)
                    });
                    t.find("span").on("touchstart", function (e) {
                        $(this).trigger("click");
                        e.preventDefault();
                        var $button = $(this);
                        timeOut = setInterval(function () {}, 150)
                    }).on("mouseup mouseleave touchend", function () {
                        clearInterval(timeOut)
                    });

                    function numberButtonFunc($button) {
                        var oldValue = $button.parent().find("input").val();
                        var container = $button.closest(".form-guest-search");
                        var total = 0;
                        $('input[type="text"]', container).each(function () {
                            total += parseInt($(this).val())
                        });
                        var newVal = oldValue;
                        if ($button.hasClass("next")) {
                            if (total < max) {
                                if (oldValue < max) {
                                    newVal = parseFloat(oldValue) + 1
                                } else {
                                    newVal = max
                                }
                            }
                        } else {
                            if (oldValue > min) {
                                newVal = parseFloat(oldValue) - 1
                            } else {
                                newVal = min
                            }
                        }
                        $button.parent().find("input").val(newVal).trigger("change");

                        // Hiển thị dạng x/y nếu ban đầu có dấu /
                        var maxVal = $button.parent().find("input").data("max");
                        var oldSpanVal = $button.parent().find('.name_phong span').text();
                        var displayVal = newVal;
                        if (oldSpanVal.indexOf('/') !== -1) {
                            displayVal = newVal + '/' + maxVal;
                        }
                        $button.parent().find('.name_phong span').text(displayVal);
                        $('input[name="' + $button.parent().find("input").attr("name") + '"]', ".search-form").trigger("change");
                        $('input[name="' + $button.parent().find("input").attr("name") + '"]', ".form-check-availability-hotel").trigger("change");
                        $('input[name="' + $button.parent().find("input").attr("name") + '"]', ".single-room-form").trigger("change");
                        if (window.matchMedia("(max-width: 767px)").matches) {
                            $("#dropdown-1 label", $button.closest(".field-guest")).hide();
                            $("#dropdown-1 .render", $button.closest(".field-guest")).show()
                        }
                    }
                });

                $(".st-list-rooms .loader-wrapper").hide();
            });
        }
        
        $("#form-booking-inpage .vietgoing-check-in-out").on("apply.daterangepicker", function (ev, picker) {
            let start   = picker.startDate.format("DD/MM/YYYY");
            let end     = picker.endDate.format("DD/MM/YYYY");
            reload_rooms(start, end);
            const url = new URL(window.location);
            url.searchParams.set('checkin', start);
            url.searchParams.set('checkout', end);
            window.history.pushState({}, '', url);
        });

        $(".vietgoing-mobile-check-in-out").daterangepicker({
            singleDatePicker: false,
            dateFormat: 'DD/MM/YYYY - DD/MM/YYYY',
            autoUpdateInput: true,
            autoApply: true,
            disabledPast: true,
            customClass: '',
            widthSingle: 500,
            onlyShowCurrentMonth: true,
            locale: locale_daterangepicker,
            drops: 'up'
        }).on("apply.daterangepicker", function (ev, picker) {
            let start   = picker.startDate.format("DD/MM/YYYY");
            let end     = picker.endDate.format("DD/MM/YYYY");
            var days    = Math.abs(moment(picker.startDate.format("MM/DD/YYYY")).diff(moment(picker.endDate.format("MM/DD/YYYY")), 'd'));
            $('.form-date-search').find('.check-in-wrapper>span').show();
            $('.form-date-search').find('.check-in-render').text(start).show();
            $('.form-date-search').find('.check-out-render').text(end).show();
            $('.form-date-search').find('input[name=checkin]').val(start);
            $('.form-date-search').find('input[name=checkout]').val(end);
            $(".vietgoing-check-in-out").val(start + ' - ' + end);
            $('.form-date-search .date-wrapper .total_night span').text(days);
            $(this).parents('.check-in-wrapper').eq(0).find('.total_night').text('(' + days + ' đêm)');
            reload_rooms(start, end);
            const url = new URL(window.location);
            url.searchParams.set('checkin', start);
            url.searchParams.set('checkout', end);
            window.history.pushState({}, '', url);
        });

        // Submit booking from mobile
        $('.hotel-target-book-mobile a.btn_book').click(function() {
            $('#form-booking-inpage').submit();
        });

        $('[data-toggle="tooltip"]').tooltip();
        
    </script>
    
    <?
    /** Update lượt view **/
    $Model->updateCountView('hotel', $hotel_id);
    ?>
    
    <?
    /** Update visit để tính CTR **/
    set_visit_page(137);
    ?>
</body>
</html>
