function initHomeJS() {
    try {
            // Room selector
            $('.room-selector').on('click', function(e) {
                e.stopPropagation();
                $(this).find('.dropdown-menu').toggleClass('show');
            });

            $('.option').on('click', function(e) {
                e.stopPropagation();
                $('.option').removeClass('active selected');
                $(this).addClass('selected');
                if ($(this).hasClass('has-detail')) {
                    $(this).toggleClass('active');
                    updateSelectedOption();
                } else {
                    $('.selected-option').text($(this).find('p').text() || $(this).find('h4').text());
                }
            });

            $('.counter button').on('click', function(e) {
                e.stopPropagation();
                const $span = $(this).siblings('span');
                let value = parseInt($span.text());
                value += $(this).hasClass('plus') ? 1 : -1;
                if (value >= 0) $span.text(value);

                // Nếu là tăng/giảm số phòng thì cập nhật số người lớn tối thiểu = số phòng
                const $counter = $(this).closest('.counter');
                const $detailItem = $counter.closest('.detail-item');
                const label = $detailItem.find('label').text().trim();
                if (label === 'Số phòng') {
                    const rooms = parseInt($span.text());
                    // Tìm đến detail-item người lớn cùng panel
                    const $adultsSpan = $detailItem.parent().find('.detail-item:nth-child(2) span');
                    let adults = parseInt($adultsSpan.text());
                    if (adults < rooms) {
                        $adultsSpan.text(rooms);
                    }
                }
                updateSelectedOption();
            });

            function updateSelectedOption() {
                const activeOption = $('.option.active');
                if (activeOption.length) {
                    const rooms = activeOption.find('.detail-item:nth-child(1) span').text();
                    const adults = activeOption.find('.detail-item:nth-child(2) span').text();
                    const children = activeOption.find('.detail-item:nth-child(3) span')?.text() || '0';
                    $('.selected-option').text(`${rooms} phòng, ${adults} người lớn, ${children} trẻ em`);
                }
            }

            // Dropdown handling
            $('#locationInput').on('click', function(e) {
                console.log('Location input clicked');
                e.stopPropagation();
                $('.dropdown-panel').toggleClass('show');
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.place, .room-selector').length) {
                    $('.dropdown-panel, .dropdown-menu').removeClass('show');
                    $('.option').removeClass('active');
                }
            });

            // Destination selection
            $('.destination').on('click', function(e) {
                e.stopPropagation();
                const cityId = $(this).data('city-id');
                const cityName = $(this).data('city-name');
                $('#locationInput').val(cityName);
                $('#selectedLocationId').val(cityId);
                $('.dropdown-panel').removeClass('show');
                // Tự động focus và mở datepicker
                $('input[name="datetimes"]').focus().click();
            });

            // Sự kiện click nút search
            $('.btnSreach').on('click', function() {
                const cityName = $('#locationInput').val();
                let cityLink = '';
                $('.destination').each(function() {
                    if ($(this).data('city-name') === cityName) {
                        cityLink = $(this).data('link');
                    }
                });
                const startDate = $("#startDateText").text();
                const endDate = $("#endDateText").text();
                const roomQty = $('#roomQty').val();
                if(roomQty == 0) {
                    alert('Số lượng phòng không được bằng 0');
                    return;
                }
                const adultQty = $('#adultQty').val();
                const childQty = $('#childQty').val();
                if (parseInt(adultQty) < parseInt(roomQty)) {
                    alert('Số người lớn phải lớn hơn hoặc bằng số phòng!');
                    return;
                }
                if (!cityLink) {
                    $('#locationInput').focus();
                    return;
                }
                // Chuyển trang với tham số
                const params = new URLSearchParams({
                    checkin: startDate,
                    checkout: endDate,
                    roomQty: roomQty,
                    adultQty: adultQty,
                    childQty: childQty
                });
                window.location.href = cityLink + '?' + params.toString();
            });

            // Date picker
            $('input[name="datetimes"]').daterangepicker({
                timePicker: false,
                parentEl: '.baler-info',
                startDate: moment().startOf("day"),
                endDate: moment().add(1, "day").startOf("day"),
                minSpan: { days: 1 }, // Không cho phép chọn ngày giống nhau
                isInvalidDate: function(date) {
                    return date.isBefore(moment(), 'day');
                },
                autoApply: true,
                locale: {
                    format: "DD/MM/YYYY",
                    applyLabel: "Chọn",
                    cancelLabel: "Hủy",
                    daysOfWeek: ["CN", "T2", "T3", "T4", "T5", "T6", "T7"],
                    monthNames: ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"]
                }
            }, function(start, end) {
                let nights = end.diff(start, "days");
                if (nights <= 0) {
                    // Nếu chọn ngày giống nhau, tự động tăng ngày checkout lên 1 ngày (cả giá trị thực và UI)
                    end = start.clone().add(1, "day");
                    nights = 1;
                    // Cập nhật lại giá trị thực trong daterangepicker
                    $('input[name="datetimes"]').data('daterangepicker').setEndDate(end);
                }
                $("#startDateText").text(start.format("DD/MM/YYYY"));
                $("#endDateText").text(end.format("DD/MM/YYYY"));
                $(".moon").html(`${nights} <i class='fas fa-moon'></i>`);
            });
            // Default display for initial date
            const start = moment().startOf("day");
            const end = moment().add(1, "day").startOf("day");
            $("#startDateText").text(start.format("DD/MM/YYYY"));
            $("#endDateText").text(end.format("DD/MM/YYYY"));
            $(".moon").html(`1 <i class='fas fa-moon'></i>`);

            // Indicator handling
            const $indicator = $('.indicator');
            const $columns = $('.search-place .col-md-4');
            
            $('#locationInput').on('focus click', () => moveIndicator(0));
            $('input[name="datetimes"]').on('focus click', () => moveIndicator(1));
            $('.room-selector').on('focus click', () => moveIndicator(2));
            
            $(document).on('click', (e) => {
                if (!$(e.target).closest('.search-place').length) {
                    $indicator.css({ width: '0', opacity: '0' });
                }
            });

            function moveIndicator(index) {
                const $column = $columns.eq(index);
                if ($column.length) {
                    const offset = $column.offset().left - $columns.first().parent().offset().left;
                    const width = $column.width();
                    $indicator.css({ left: offset + 'px', width: width + 'px', opacity: '1' });
                }
            }
        // Initialize Nice Select
        // $('.js-select').niceSelect();

        // Initialize Flatpickr for date range
        // flatpickr('#range_date', {
        //     mode: 'range',
        //     dateFormat: 'd-m-Y',
        //     defaultDate: ['05-06-2025', '06-06-2025'],
        //     minDate: 'today',
        //     onChange: function(selectedDates, dateStr, instance) {
        //         // Handle date change if needed
        //     }
        // });

        // Service cards animation
        $('.service-card[data-service]').each(function(index) {
            const $card = $(this);
            setTimeout(() => $card.addClass('visible'), index * 100);
        });

        // Carousel accessibility
        $('#bannerCarousel').on('slid.bs.carousel', function() {
            const currentSlide = $(this).find('.carousel-item.active').index() + 1;
            $('.carousel-indicators').attr('aria-label', `Slide ${currentSlide} of 2`);
        });

        // Lazy load images
        $('img.lazyload').each(function() {
            const $img = $(this);
            $img.attr('src', $img.data('src') || $img.attr('src'));
        });

        // Destination cards animation
        $('.destination-card').css('opacity', 0).each(function(i) {
            $(this).delay(i * 100).animate({ opacity: 1 }, 500);
        });

        // Fade-in animation for attraction cards
        $('.attraction-card').css('opacity', 0).each(function(i) {
            $(this).delay(i * 100).animate({ opacity: 1 }, 500);
        });

        // Initialize Magnific Popup for gallery
        // $('.gallery-popup-two').magnificPopup({
        //     type: 'image',
        //     gallery: {
        //         enabled: true
        //     },
        //     mainClass: 'mfp-with-zoom',
        //     zoom: {
        //         enabled: true,
        //         duration: 300,
        //         easing: 'ease-in-out'
        //     }
        // });

        // Fade-in animation for news cards
        $('.news-card[data-news]').each(function(index) {
            const $card = $(this);
            setTimeout(() => {
                $card.addClass('visible');
            }, index * 100); // Delay each card by 100ms
        });

        // Lazy load images
        $('.lazyload').each(function() {
            const $img = $(this);
            $img.attr('src', $img.data('src')).removeAttr('data-src');
        });

        var owl = $(".home-carousel");

        owl.owlCarousel({
            loop: true,
            margin: 20,
            nav: false,
            dots: false,
            slideBy: 1,
            responsive: {
            0: { items: 1 },
            768: { items: 2 },
            992: { items: 3 },
            1200: { items: 4 }
            }
        });

        // Custom next/prev buttons
        $("#customNextBtn").click(function() {
            owl.trigger('next.owl.carousel');
        });

        $("#customPrevBtn").click(function() {
            owl.trigger('prev.owl.carousel');
        });

    } catch (error) {
        console.error('Error in home.js:', error);
    }
}

// Kiểm tra jQuery đã load chưa
if (window.jQuery) {
    $(document).ready(initHomeJS);
} else {
    // Tạo event listener chờ jQuery load xong
    const waitForJQuery = setInterval(() => {
        if (window.jQuery) {
            clearInterval(waitForJQuery);
            $(document).ready(initHomeJS);
        }
    }, 100);
}