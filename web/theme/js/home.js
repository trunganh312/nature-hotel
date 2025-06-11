function initHomeJS() {
    try {
        // Room selector
        $('.room-selector').on('click', function(e) {
            e.stopPropagation();
            if (window.innerWidth <= 991.98) {
                // Mobile/Tablet: Show slide menu
                $('#slideMenu').addClass('show');
                $('#slideMenuContent .location-content').hide();
                $('#slideMenuContent .room-selector-content').show();
                $('.slide-menu-title').text('Số phòng, số khách');
            } else {
                // PC: Show dropdown
                $('#roomSelectorDropdown').toggleClass('show');
            }
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
                $('#roomQty').val(1);
                $('#adultQty').val($(this).find('p').text().includes('2 người') ? 2 : 1);
                $('#childQty').val(0);
                if (window.innerWidth <= 991.98) {
                    $('#slideMenu').removeClass('show');
                } else {
                    $('#roomSelectorDropdown').removeClass('show');
                }
            }
        });

        $('.counter button').on('click', function(e) {
            e.stopPropagation();
            const $span = $(this).siblings('span');
            let value = parseInt($span.text());
            value += $(this).hasClass('plus') ? 1 : -1;
            if (value >= 0) $span.text(value);

            const $counter = $(this).closest('.counter');
            const $detailItem = $counter.closest('.detail-item');
            const label = $detailItem.find('label').text().trim();
            if (label === 'Số phòng') {
                const rooms = parseInt($span.text());
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
                $('#roomQty').val(rooms);
                $('#adultQty').val(adults);
                $('#childQty').val(children);
                $('.selected-option').text(`${rooms} phòng, ${adults} người lớn, ${children} trẻ em`);
            }
        }

        // Dropdown/Slide menu handling for location
        $('#locationInput').on('click', function(e) {
            e.stopPropagation();
            if (window.innerWidth <= 991.98) {
                // Mobile/Tablet: Show full-screen slide menu
                $('#slideMenu').addClass('show');
                $('#slideMenuContent .location-content').show();
                $('#slideMenuContent .room-selector-content').hide();
                $('.slide-menu-title').text('Chọn địa điểm');
            } else {
                // PC: Show dropdown
                $('.dropdown-panel').toggleClass('show');
            }
        });

        // Close slide menu
        $('.slide-menu-close').on('click', function() {
            $('#slideMenu').removeClass('show');
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('.place, .room-selector, .slide-menu, .dropdown-panel, .dropdown-menu').length) {
                $('.dropdown-panel, #roomSelectorDropdown, .slide-menu').removeClass('show');
                $('.option').removeClass('active');
            }
        });

        // Destination selection
        $('.destination').on('click', function(e) {
            e.stopPropagation();
            const cityName = $(this).data('city-name');
            $('#locationInput').val(cityName);
            // Note: cityId is not provided in data attributes, assuming it's not needed or handled elsewhere
            $('#selectedLocationId').val(''); // Update if cityId is available
            if (window.innerWidth <= 991.98) {
                $('#slideMenu').removeClass('show');
            } else {
                $('.dropdown-panel').removeClass('show');
            }
            $('input[name="datetimes"]').focus().click();
        });

        // Search button
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
            if (roomQty == 0) {
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

            const expirationDays = 1;
            setCookie('search_city', cityName, expirationDays);
            setCookie('search_checkin', startDate, expirationDays);
            setCookie('search_checkout', endDate, expirationDays);
            setCookie('search_room_qty', roomQty, expirationDays);
            setCookie('search_adult_qty', adultQty, expirationDays);
            setCookie('search_child_qty', childQty, expirationDays);

            const params = new URLSearchParams({
                checkin: startDate,
                checkout: endDate,
                roomQty: roomQty,
                adultQty: adultQty,
                childQty: childQty
            });
            window.location.href = cityLink + '?' + params.toString();
        });

        function setCookie(name, value, days) {
            let expires = "";
            if (days) {
                const date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + encodeURIComponent(value) + expires + "; path=/";
        }

        function getCookie(name) {
            const nameEQ = name + "=";
            const ca = document.cookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i];
                while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) == 0) return decodeURIComponent(c.substring(nameEQ.length, c.length));
            }
            return null;
        }

        // Date picker
        $('input[name="datetimes"]').daterangepicker({
            timePicker: false,
            parentEl: '.baler-info',
            startDate: moment().startOf("day"),
            endDate: moment().add(1, "day").startOf("day"),
            minSpan: { days: 1 },
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
                end = start.clone().add(1, "day");
                nights = 1;
                $('input[name="datetimes"]').data('daterangepicker').setEndDate(end);
            }
            $("#startDateText").text(start.format("DD/MM/YYYY"));
            $("#endDateText").text(end.format("DD/MM/YYYY"));
            $(".moon").html(`${nights} <i class='fas fa-moon'></i>`);
        });

        // Load saved search parameters
        function loadSavedSearchParams() {
            const savedCity = getCookie('search_city');
            const savedCheckin = getCookie('search_checkin');
            const savedCheckout = getCookie('search_checkout');
            const savedRoomQty = getCookie('search_room_qty');
            const savedAdultQty = getCookie('search_adult_qty');
            const savedChildQty = getCookie('search_child_qty');

            if (savedCity) $('#locationInput').val(savedCity);
            if (savedRoomQty) $('#roomQty').val(savedRoomQty);
            if (savedAdultQty) $('#adultQty').val(savedAdultQty);
            if (savedChildQty) $('#childQty').val(savedChildQty);

            if (savedRoomQty && savedAdultQty && savedChildQty) {
                $('.selected-option').text(`${savedRoomQty} phòng, ${savedAdultQty} người lớn, ${savedChildQty} trẻ em`);
            }

            if (savedCheckin && savedCheckout) {
                const start = moment(savedCheckin, "DD/MM/YYYY");
                const end = moment(savedCheckout, "DD/MM/YYYY");
                if (start.isValid() && end.isValid()) {
                    const picker = $('input[name="datetimes"]').data('daterangepicker');
                    if (picker) {
                        picker.setStartDate(start);
                        picker.setEndDate(end);
                        $("#startDateText").text(start.format("DD/MM/YYYY"));
                        $("#endDateText").text(end.format("DD/MM/YYYY"));
                        const nights = end.diff(start, "days");
                        $(".moon").html(`${nights} <i class='fas fa-moon'></i>`);
                    }
                }
            }
        }

        const start = moment().startOf("day");
        const end = moment().add(1, "day").startOf("day");
        $("#startDateText").text(start.format("DD/MM/YYYY"));
        $("#endDateText").text(end.format("DD/MM/YYYY"));
        $(".moon").html(`1 <i class='fas fa-moon'></i>`);

        setTimeout(loadSavedSearchParams, 100);

        // Indicator handling
        const $indicator = $('.indicator');
        const $columns = $('.search-place .col-md-4');

        $('#locationInput').on('focus click', () => moveIndicator(0));
        $('input[name="datetimes"]').on('focus click', () => moveIndicator(1));
        $('.room-selector').on('focus click', () => moveIndicator(2));

        $(document).on('click', (e) => {
            if (!$(e.target).closest('.search-place, .dropdown-panel, .dropdown-menu').length) {
                $indicator.css({ width: '0', opacity: '0' });
            }
        });

        function moveIndicator(index) {
            const $column = $columns.eq(index);
            if ($column.length && window.innerWidth > 991.98) {
                const offset = $column.offset().left - $columns.first().parent().offset().left;
                const width = $column.width();
                $indicator.css({ left: offset + 'px', width: width + 'px', opacity: '1' });
            }
        }

        // Other existing functionalities
        $('.service-card[data-service]').each(function(index) {
            const $card = $(this);
            setTimeout(() => $card.addClass('visible'), index * 100);
        });

        $('#bannerCarousel').on('slid.bs.carousel', function() {
            const currentSlide = $(this).find('.carousel-item.active').index() + 1;
            $('.carousel-indicators').attr('aria-label', `Slide ${currentSlide} of 2`);
        });

        $('img.lazyload').each(function() {
            const $img = $(this);
            $img.attr('src', $img.data('src') || $img.attr('src'));
        });

        $('.destination-card').css('opacity', 0).each(function(i) {
            $(this).delay(i * 100).animate({ opacity: 1 }, 500);
        });

        $('.attraction-card').css('opacity', 0).each(function(i) {
            $(this).delay(i * 100).animate({ opacity: 1 }, 500);
        });

        $('.news-card[data-news]').each(function(index) {
            const $card = $(this);
            setTimeout(() => {
                $card.addClass('visible');
            }, index * 100);
        });

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

if (window.jQuery) {
    $(document).ready(initHomeJS);
} else {
    const waitForJQuery = setInterval(() => {
        if (window.jQuery) {
            clearInterval(waitForJQuery);
            $(document).ready(initHomeJS);
        }
    }, 100);
}