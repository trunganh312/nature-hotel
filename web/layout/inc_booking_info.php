<?php
// Khởi tạo các biến mặc định
$hotel_name = isset($hotel_name) ? htmlspecialchars($hotel_name) : "Khách sạn Sunrise";
$check_in = isset($check_in) ? htmlspecialchars($check_in) : date('Y-m-d');
$check_out = isset($check_out) ? htmlspecialchars($check_out) : date('Y-m-d', strtotime('+1 day'));
$nights = isset($nights) ? $nights : 1;
$tax_service_fee = isset($tax_service_fee) ? $tax_service_fee : 300000;
$discount_code = isset($discount_code) ? htmlspecialchars($discount_code) : "SUMMER25";
$discount_amount = isset($discount_amount) ? $discount_amount : 500000;
?>

<div class="card_booking-info booking-info_booking-info shadow-sm border-0 p-0">
    <div class="card-body">
        <!-- Toggle button cho mobile -->
        <button class="toggle-details-btn d-block d-md-none" style="display: none;">
            <i class="fas fa-chevron-down"></i> Xem chi tiết
        </button>
        <h5 class="text-center booking_info-title">Thông tin đặt phòng</h5>
        <!-- Tiêu đề khách sạn -->
        <h4 class="hotel-name"><?php echo $hotel_name; ?></h4>

        <!-- Thời gian và số đêm -->
        <div class="mb-1 time-section position-relative">
            <p class="info-label"><i class="fas fa-calendar-alt me-2"></i>Thời gian</p>
            <p class="info-value date-range-clickable" style="cursor: pointer;">
                <?php echo $check_in; ?> - <?php echo $check_out; ?> (<?php echo $nights; ?> đêm)
            </p>
            <!-- Input ẩn để lưu giá trị ngày -->
            <input type="text" class="date-range-input" style="display: none;" value="<?php echo $check_in; ?> - <?php echo $check_out; ?>">
        </div>

        <!-- Các khối hạng phòng sẽ được thêm bằng JS -->
        <div id="room-type-sections-container" class="room-type-sections"></div>

        <!-- Tổng tiền phòng -->
        <div class="mb-1">
            <p class="info-label"><i class="fas fa-money-bill me-2"></i>Tiền phòng</p>
            <p class="info-value" id="total-room-price">0 VND</p>
        </div>

        <hr>

        <!-- Tổng hợp chi phí -->
        <div class="mb-1 additional-info">
            <?php if ($discount_code): ?>
            <div class="d-flex justify-content-between mb-2">
                <span class="font-14">Mã giảm giá (<?php echo $discount_code; ?>)</span>
                <span class="discount-amount" id="discount-amount">-<?php echo number_format($discount_amount, 0, ',', '.'); ?> VND</span>
            </div>
            <?php endif; ?>
            <div class="d-flex justify-content-between mb-2">
                <span class="font-14">Giá sau giảm giá</span>
                <span class="font-14" id="price-after-discount">0 VND</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="font-14">Thuế và phí dịch vụ</span>
                <span class="font-14" id="tax-service-fee"><?php echo number_format($tax_service_fee, 0, ',', '.'); ?> VND</span>
            </div>
        </div>

        <!-- Tổng tiền -->
        <div class="pt-1 border-top">
            <div class="d-flex justify-content-between align-items-center">
                <div class="fw-bold total-price-text">Tổng tiền</div>
                <div class="total-price font-14" id="total-price">0 VND</div>
            </div>
        </div>
    </div>

    <!-- Nút đặt ngay -->
    <button class="btn btn-secondary btn-block booking-button">
        <i class="fas fa-check-circle me-2"></i>Đặt ngay
    </button>
</div>

<style>
    .font-14 {
        font-size: 14px;
    }
    .booking-info_fee-item {
        margin-bottom: 5px;
        font-size: 14px;
    }
    .booking-info_room-title {
        font-weight: 700;
        font-size: 14px;
    }
    .booking_info-title {
        color: var(--primary-color);
        font-weight: 700;
        margin-bottom: 10px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--boder_bottom);
    }
    .booking-info_booking-info {
        width: 100%;
        max-width: 306px;
    }
    .card_booking-info .booking-info_booking-info {
        background-color: var(--white);
        border-radius: 0.5rem;
        font-size: 14px;
        width: 100%;
        max-width: 306px;
        box-sizing: border-box;
        position: sticky;
        top: -29px;
    }
    .booking-info_booking-info .hotel-name {
        color: var(--text-color);
        font-weight: 700;
        margin-bottom: 10px;
        font-size: 14px;
    }
    .booking-info_booking-info .info-label {
        color: var(--text-light);
        margin-bottom: 0.25rem;
        font-size: 14px;
    }
    .booking-info_booking-info .info-value {
        color: var(--text-color);
        font-weight: 500;
        font-size: 14px;
        margin-bottom: 5px;
    }
    .booking-info_booking-info .room-type {
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    .booking-info_booking-info .room-type:hover .info-value {
        color: var(--accent-color);
        transition: color 0.3s ease;
    }
    .collapse-details_info-label {
        color: var(--text-color) !important;
        font-weight: bold !important;
        margin-bottom: 0 !important;
        font-size: 14px !important;
    }
    .booking-info_booking-info .collapse-details {
        background-color: var(--background-color);
        border-radius: 0.375rem;
        padding: 1rem;
    }
    .booking-info_booking-info .collapse-details p {
        color: var(--text-color);
        margin-bottom: 0.5rem;
        font-size: 14px;
    }
    .booking-info_booking-info .discount-amount {
        color: var(--success-color);
        font-size: 14px;
    }
    .booking-info_booking-info .total-price {
        color: var(--primary-color);
        font-weight: 700;
        font-size: 14px;
    }
    .booking-info_booking-info hr {
        margin: 0.5rem 0;
        border-color: var(--text-light);
        opacity: 0.2;
    }
    .total-price-text {
        font-size: 14px;
    }
    .booking-button {
        font-weight: 600;
        font-size: 16px;
        padding: 12px 0;
        transition: all 0.3s ease;
        box-shadow: 0 4px 8px var(--secondary-color);
        width: 100%;
    }
    .booking-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px var(--accent-color);
    }

    /* Toggle button cho mobile */
    .toggle-details-btn {
        background: none;
        border: none;
        color: var(--accent-color);
        font-size: 14px;
        padding: 5px 0;
        width: 100%;
        text-align: left;
        cursor: pointer;
        display: none;
    }
    .toggle-details-btn i {
        transition: transform 0.3s ease;
    }
    .toggle-details-btn.active i {
        transform: rotate(180deg);
    }

    /* Responsive cho mobile */
    @media (max-width: 991.98px) {
        
        .booking-info_booking-info hr {
            display: none;
        }
        .booking-info_booking-info {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            max-width: 100%;
            border-radius: 0;
            z-index: 1000;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            padding: 10px;
            display: flex;
            flex-direction: column;
            height: auto;
            max-height: 80vh;
            overflow-y: auto;
            background-color: var(--white);
        }

        .booking-info_booking-info .hotel-name,
        .booking-info_booking-info .booking_info-title,
        .booking-info_booking-info .time-section,
        #room-type-sections-container,
        .additional-info {
            display: none;
        }

        .toggle-details-btn {
            display: block;
        }

        .booking-info_booking-info .info-label,
        .booking-info_booking-info .info-value {
            font-size: 12px;
            margin-bottom: 2px;
        }

        .booking-info_booking-info .total-price-text,
        .booking-info_booking-info .total-price {
            font-size: 14px;
        }

        .booking-button {
            font-size: 14px;
            padding: 8px 0;
        }

        /* Hiển thị khi toggle */
        .booking-info_booking-info.active .hotel-name,
        .booking-info_booking-info.active .booking_info-title,
        .booking-info_booking-info.active .time-section,
        .booking-info_booking-info.active #room-type-sections-container,
        .booking-info_booking-info.active .additional-info {
            display: block;
        }

        .booking-info_booking-info.active .toggle-details-btn i {
            transform: rotate(180deg);
        }
    }

    .time-section .daterangepicker {
        left: -600px !important;
    }

    /* CSS Responsive cho daterangepicker trên mobile */
    @media (max-width: 991.98px) {
        .time-section .daterangepicker {
            left: 201px !important;
        }
        .daterangepicker {
            position: fixed !important;
            top: 46% !important;
            left: 50% !important;
            right: auto !important;
            bottom: auto !important;
            transform: translate(-50%, -50%) !important;
            z-index: 1100 !important;
            width: 95% !important;
            max-width: 300px !important;
            margin: 0 !important;
        }

        /* Hiển thị cả hai bảng tháng với kích thước nhỏ hơn */
        .daterangepicker .drp-calendar {
            width: 48% !important;
            max-width: 48% !important;
            margin: 0 1% !important;
            font-size: 11px !important;
        }
        
        /* Hiển thị bảng tháng thứ hai */
        .daterangepicker .drp-calendar.right {
            display: block !important;
        }

        /* Điều chỉnh kích thước của các ô ngày */
        .daterangepicker td, 
        .daterangepicker th {
            width: 25px !important;
            height: 25px !important;
            min-width: 25px !important;
            line-height: 25px !important;
            font-size: 10px !important;
            padding: 0 !important;
        }

        /* Bo gọn padding của bảng lịch */
        .daterangepicker .drp-calendar.left,
        .daterangepicker .drp-calendar.right {
            padding: 5px !important;
            border-right: none !important;
        }

        /* Thu nhỏ tiêu đề tháng */
        .daterangepicker th.month {
            font-size: 12px !important;
            padding: 0 !important;
        }
        
        /* Thu nhỏ nút prev/next */
        .daterangepicker .prev,
        .daterangepicker .next {
            width: 20px !important;
            height: 20px !important;
            line-height: 20px !important;
            font-size: 12px !important;
        }
    }
</style>

<script>
function initBookingInfoJS() {
    try {
        // Xử lý sticky booking info
        const handleStickyBooking = () => {
            const bookingInfo = document.querySelector('.booking-info_booking-info');
            const endStickyPoint = document.querySelector('#end-sticky-point');
            if (!bookingInfo || !endStickyPoint) return;

            const header = document.querySelector('header');
            const headerHeight = header ? header.offsetHeight : 0;
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const bookingInfoHeight = bookingInfo.offsetHeight;
            const endStickyPointTop = endStickyPoint.getBoundingClientRect().top + scrollTop;

            let topPosition = headerHeight + 20;
            if (scrollTop + bookingInfoHeight + topPosition > endStickyPointTop) {
                bookingInfo.style.position = 'absolute';
                bookingInfo.style.top = `${endStickyPointTop - bookingInfoHeight}px`;
                bookingInfo.classList.remove('is-sticky');
            } else {
                bookingInfo.style.position = 'sticky';
                bookingInfo.style.top = `${topPosition}px`;
                bookingInfo.classList.add('is-sticky');
            }
        };

        // Chỉ áp dụng sticky top trên PC
        if (window.innerWidth > 991.98) {
            window.addEventListener('scroll', handleStickyBooking);
            window.addEventListener('resize', handleStickyBooking);
            handleStickyBooking();
        }

        // Khởi tạo daterange picker
        $('.date-range-input').daterangepicker({
            parentEl: '.time-section',
            opens: 'left',
            autoApply: true,
            locale: {
                format: 'YYYY-MM-DD',
                separator: ' - ',
                applyLabel: 'Áp dụng',
                cancelLabel: 'Hủy',
                fromLabel: 'Từ',
                toLabel: 'Đến',
                daysOfWeek: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
                monthNames: [
                    'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
                    'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'
                ],
                firstDay: 1
            },
            minDate: moment().startOf('day'),
            startDate: '<?php echo $check_in; ?>',
            endDate: '<?php echo $check_out; ?>'
        });

        // Xử lý nhấp vào phần thời gian
        $('.date-range-clickable').on('click', function() {
            $(this).siblings('.date-range-input').trigger('click');
        });

        // Cập nhật hiển thị khi thay đổi ngày
        $('.date-range-input').on('apply.daterangepicker', function(ev, picker) {
            const checkIn = picker.startDate.format('YYYY-MM-DD');
            const checkOut = picker.endDate.format('YYYY-MM-DD');
            const nights = picker.endDate.diff(picker.startDate, 'days');
            $(this).siblings('.date-range-clickable').text(`${checkIn} - ${checkOut} (${nights} đêm)`);

            // Gửi sự kiện để cập nhật thông tin đặt phòng
            const event = new CustomEvent('roomSelectionChange', {
                detail: {
                    selectedRooms: window.currentSelectedRooms || [],
                    checkIn: checkIn,
                    checkOut: checkOut,
                    nights: nights
                }
            });
            window.dispatchEvent(event);
        });

        // Xử lý click vào phần room-type
        $(document).on('click', '.room-type', function() {
            const $icon = $(this).find('.toggle-icon');
            const $collapse = $(this).next('.collapse');
            $icon.toggleClass('fa-chevron-down fa-chevron-up');
            $collapse.collapse('toggle');
        });

        // Toggle chi tiết trên mobile
        const toggleBtn = document.querySelector('.toggle-details-btn');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                const bookingInfo = document.querySelector('.booking-info_booking-info');
                bookingInfo.classList.toggle('active');
                this.classList.toggle('active');
                this.textContent = bookingInfo.classList.contains('active') ? 'Ẩn chi tiết' : 'Xem chi tiết';
            });
        }

        // Lắng nghe sự kiện từ room_detail để cập nhật thông tin
        window.addEventListener('roomSelectionChange', function(e) {
            const selectedRooms = e.detail.selectedRooms;
            const checkIn = e.detail.checkIn;
            const checkOut = e.detail.checkOut;
            const nights = e.detail.nights;
            const discountAmount = <?php echo $discount_amount; ?>;
            const taxServiceFee = <?php echo $tax_service_fee; ?>;
            const extraFees = {
                "Phí dịch vụ phòng": 150000,
                "Phí tiện ích": 200000
            };

            // Lưu trạng thái mở của các mục hạng phòng
            const collapseStates = {};
            document.querySelectorAll('.room-type-section .collapse').forEach(collapse => {
                const roomId = collapse.id.split('roomDetails-')[1];
                collapseStates[roomId] = collapse.classList.contains('show');
            });

            // Cập nhật thời gian
            $('.date-range-clickable').text(`${checkIn} - ${checkOut} (${nights} đêm)`);

            // Kiểm tra nếu không có phòng nào được chọn
            if (!selectedRooms || selectedRooms.length === 0) {
                $('#room-type-sections-container').empty();
                $('#total-room-price').text('0 VND');
                $('#price-after-discount').text('0 VND');
                $('#total-price').text(`${new Intl.NumberFormat('vi-VN').format(taxServiceFee + Object.values(extraFees).reduce((a, b) => a + b, 0))} VND`);
                return;
            }

            // Tạo các khối hạng phòng riêng biệt
            let roomSectionsHtml = '';
            selectedRooms.forEach(roomType => {
                let roomDetailsHtml = '';
                roomType.rooms.forEach((room, index) => {
                    roomDetailsHtml += `
                        <div class="booking-info_room">
                            <div class="booking-info_room-title">${roomType.roomName} - Phòng ${index + 1}:</div>
                            <div class="booking-info_guest">
                                <p><i class="fas fa-user me-2"></i>Người lớn: <span class="adults">${room.adults}</span></p>
                                <p><i class="fas fa-child me-2"></i>Trẻ em: <span class="children">${room.children}</span></p>
                                <p><i class="fas fa-baby me-2"></i>Em bé: <span class="infants">${room.infants}</span></p>
                            </div>
                        </div>`;
                });

                if (extraFees && Object.keys(extraFees).length > 0) {
                    roomDetailsHtml += `
                        <hr class="my-2">
                        <p class="collapse-details_info-label">Phụ phí:</p>`;
                    for (const [feeName, feeAmount] of Object.entries(extraFees)) {
                        roomDetailsHtml += `
                            <div class="d-flex justify-content-between booking-info_fee-item">
                                <span>${feeName}</span>
                                <span>${new Intl.NumberFormat('vi-VN').format(feeAmount)} VND</span>
                            </div>`;
                    }
                }

                // Thêm lớp 'show' nếu mục này đang mở
                const isExpanded = collapseStates[roomType.roomId] ? 'show' : '';
                roomSectionsHtml += `
                    <div class="mb-1 room-type-section">
                        <div class="room-type d-flex justify-content-between align-items-center" data-bs-toggle="collapse" data-bs-target="#roomDetails-${roomType.roomId}">
                            <div class="booking-info_room-type-left">
                                <p class="info-label"><i class="fas fa-bed me-2"></i>Hạng phòng</p>
                                <p class="info-value">${roomType.roomName} (${roomType.roomCount} phòng)</p>
                            </div>
                            <div class="booking-info_room-type-right">
                                <i class="fas fa-chevron-${isExpanded ? 'up' : 'down'} toggle-icon"></i>
                            </div>
                        </div>
                        <div class="collapse ${isExpanded}" id="roomDetails-${roomType.roomId}">
                            <div class="collapse-details">${roomDetailsHtml}</div>
                        </div>
                    </div>`;
            });
            $('#room-type-sections-container').html(roomSectionsHtml);

            // Khôi phục sự kiện collapse
            $('.room-type').off('click').on('click', function() {
                const $icon = $(this).find('.toggle-icon');
                const $collapse = $(this).next('.collapse');
                $icon.toggleClass('fa-chevron-down fa-chevron-up');
                $collapse.collapse('toggle');
            });

            // Tính tổng giá
            let totalRoomPrice = 0;
            selectedRooms.forEach(roomType => {
                totalRoomPrice += roomType.roomPrice * roomType.roomCount * nights;
            });
            $('#total-room-price').text(`${new Intl.NumberFormat('vi-VN').format(totalRoomPrice)} VND`);
            const priceAfterDiscount = totalRoomPrice - discountAmount;
            $('#price-after-discount').text(`${new Intl.NumberFormat('vi-VN').format(priceAfterDiscount)} VND`);
            const totalPrice = priceAfterDiscount + taxServiceFee + Object.values(extraFees).reduce((a, b) => a + b, 0);
            $('#total-price').text(`${new Intl.NumberFormat('vi-VN').format(totalPrice)} VND`);
        });

    } catch (error) {
        console.error('Error in booking_info.js:', error);
    }
}

if (window.jQuery) {
    $(document).ready(initBookingInfoJS);
} else {
    const waitForJQuery = setInterval(() => {
        if (window.jQuery) {
            clearInterval(waitForJQuery);
            $(document).ready(initBookingInfoJS);
        }
    }, 100);
}
</script>