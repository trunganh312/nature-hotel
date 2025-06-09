<?php
// Giả lập dữ liệu đặt phòng
$hotel_name = "Khách sạn Sunrise";
$check_in = "2025-06-10";
$check_out = "2025-06-13";
$nights = 3;
$room_type = "Deluxe Ocean View";
$room_count = 2;
$adults = 4;
$children = 2;
$infants = 1;
$room_price = 2500000; // Giá 1 phòng/đêm
$extra_fees = [
    "Phí dịch vụ phòng" => 150000,
    "Phí tiện ích" => 200000
];
$discount_code = "SUMMER25";
$discount_amount = 500000;
$tax_service_fee = 300000;
?>


<div class="card booking-info shadow-sm border-0 p-0">
    <div class="card-body">
        <h5 class="text-center booking_info-title">Thông tin đặt phòng</h5>
        <!-- Tiêu đề khách sạn -->
        <h4 class="hotel-name"><?php echo $hotel_name; ?></h4>

        <!-- Thời gian và số đêm -->
        <div class="mb-1">
            <p class="info-label"><i class="fas fa-calendar-alt me-2"></i>Thời gian</p>
            <p class="info-value"><?php echo $check_in; ?> - <?php echo $check_out; ?> (<?php echo $nights; ?> đêm)
            </p>
        </div>

        <!-- Hạng phòng, số phòng, số đêm -->
        <div class="mb-1">
            <div class="d-flex justify-content-between align-items-center room-type" data-bs-toggle="collapse"
                data-bs-target="#roomDetails" aria-expanded="false" aria-controls="roomDetails">
                <div>
                    <p class="info-label"><i class="fas fa-bed me-2"></i>Hạng phòng</p>
                    <p class="info-value"><?php echo $room_type; ?> (<?php echo $room_count; ?> phòng)</p>
                </div>
                <i class="fas fa-chevron-down toggle-icon"></i>
            </div>

            <!-- Chi tiết mở rộng -->
            <div class="collapse" id="roomDetails">
                <div class="collapse-details">
                    <p><i class="fas fa-user me-2"></i>Người lớn: <?php echo $adults; ?></p>
                    <p><i class="fas fa-child me-2"></i>Trẻ em: <?php echo $children; ?></p>
                    <p><i class="fas fa-baby me-2"></i>Em bé: <?php echo $infants; ?></p>
                    <?php if (!empty($extra_fees)): ?>
                        <hr class="my-2">
                        <p class="collapse-details_info-label">Phụ phí:</p>
                        <?php foreach ($extra_fees as $fee_name => $fee_amount): ?>
                            <div class="d-flex justify-content-between">
                                <span><?php echo $fee_name; ?></span>
                                <span><?php echo number_format($fee_amount, 0, ',', '.'); ?> VND</span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Tổng tiền phòng -->
        <div class="mb-1">
            <p class="info-label"><i class="fas fa-money-bill me-2"></i>Tiền phòng</p>
            <?php
            $total_room_price = $room_price * $nights * $room_count;
            ?>
            <p class="info-value"><?php echo number_format($total_room_price, 0, ',', '.'); ?> VND</p>
        </div>

        <hr>

        <!-- Tổng hợp chi phí -->
        <div class="mb-1">
            <div class="d-flex justify-content-between mb-2">
                <span>Mã giảm giá (<?php echo $discount_code; ?>)</span>
                <span class="discount-amount">-<?php echo number_format($discount_amount, 0, ',', '.'); ?>
                    VND</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span>Giá sau giảm giá</span>
                <?php
                $price_after_discount = $total_room_price - $discount_amount;
                ?>
                <span><?php echo number_format($price_after_discount, 0, ',', '.'); ?> VND</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span>Thuế và phí dịch vụ</span>
                <span><?php echo number_format($tax_service_fee, 0, ',', '.'); ?> VND</span>
            </div>
        </div>

        <!-- Tổng tiền -->
        <div class="pt-1 border-top">
            <div class="d-flex justify-content-between align-items-center">
                <div class="fw-bold total-price-text">Tổng tiền</div>
                <?php
                $total_price = $price_after_discount + $tax_service_fee + array_sum($extra_fees);
                ?>
                <div class="total-price"><?php echo number_format($total_price, 0, ',', '.'); ?> VND</div>
            </div>
        </div>
    </div>

    <!-- Nút đặt ngay -->
    <button class="btn btn-secondary btn-block booking-button">
        <i class="fas fa-check-circle me-2"></i>Đặt ngay
    </button>
</div>


<style>
    .booking_info-title {
        color: var(--primary-color);
        font-weight: 700;
        margin-bottom: 10px;
        padding-bottom: 10px;
        border-bottom: 1px solid var(--boder_bottom);
    }   
    .card.booking-info {
        background-color: var(--white);
        border-radius: 0.5rem;
        font-size: 14px;
        width: 100%; /* Đảm bảo chiều rộng cố định */
        max-width: 306px; /* Điều chỉnh theo thiết kế */
        box-sizing: border-box; /* Giữ kích thước ổn định */
        position: sticky;
        top: -29px
    }

    .booking-info .hotel-name {
        color: var(--text-color);
        font-weight: 700;
        margin-bottom: 10px;
        font-size: 14px;    
    }

    .booking-info .info-label {
        color: var(--text-light);
        margin-bottom: 0.25rem;
        font-size: 14px;
    }

    .booking-info .info-value {
        color: var(--text-color);
        font-weight: 500;
        font-size: 14px;
        margin-bottom: 5px;
    }

    .booking-info .room-type {
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .booking-info .room-type:hover .info-value {
        color: var(--accent-color);
        transition: color 0.3s ease;
    }
    .collapse-details_info-label {
        color: var(--text-color) !important;
        font-weight: bold !important;
        margin-bottom: 0 !important;
        font-size: 14px !important;
    }
    .booking-info .collapse-details {
        background-color: var(--background-color);
        border-radius: 0.375rem;
        padding: 1rem;
    }

    .booking-info .collapse-details p {
        color: var(--text-color);
        margin-bottom: 0.5rem;
        font-size: 14px;
    }

    .booking-info .discount-amount {
        color: var(--success-color);
        font-size: 14px;
    }

    .booking-info .total-price {
        color: var(--primary-color);
        font-weight: 700;
        font-size: 14px;
    }

    .booking-info hr {
        margin: 0.5rem 0;
        border-color: var(--text-light);
        opacity: 0.2;
    }

    .total-price-text {
        font-size: 14px;
    }

    /* Style cho nút đặt ngay */
    .booking-button {
        font-weight: 600;
        font-size: 16px;
        padding: 12px 0;
        transition: all 0.3s ease;
        box-shadow: 0 4px 8px var(--secondary-color);
    }

    .booking-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px var(--accent-color);
    }

    @media (max-width: 576px) {
        .booking-info .hotel-name {
            font-size: 1.25rem;
        }

        .booking-info .info-value {
            font-size: 0.9375rem;
        }
    }
</style>

<script>
    function initBookingInfoJS() {
        try {
            // Xử lý sticky booking info
            const handleStickyBooking = () => {
                const bookingInfo = document.querySelector('.booking-info');
                const endStickyPoint = document.querySelector('#end-sticky-point');
                if (!bookingInfo || !endStickyPoint) return;

                const header = document.querySelector('header');
                const headerHeight = header ? header.offsetHeight : 0;
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                const bookingInfoHeight = bookingInfo.offsetHeight;
                const endStickyPointTop = endStickyPoint.getBoundingClientRect().top + scrollTop;

                // Tính toán vị trí sticky
                let topPosition = headerHeight + 20;

                // Nếu cuộn xuống quá điểm end-sticky-point
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

            // Đăng ký sự kiện scroll và resize
            window.addEventListener('scroll', handleStickyBooking);
            window.addEventListener('resize', handleStickyBooking);

            // Khởi tạo ngay khi trang tải
            handleStickyBooking();

            // Xử lý click vào phần booking-info
            $('.room-type').on('click', function() {
                const $icon = $(this).find('.toggle-icon');
                $icon.toggleClass('fa-chevron-down fa-chevron-up');
            });

            // Xử lý sự kiện hiển thị/ẩn của collapse
            $('#roomDetails').on('show.bs.collapse', function() {
                $('.room-type .toggle-icon').removeClass('fa-chevron-down').addClass('fa-chevron-up');
            });

            $('#roomDetails').on('hide.bs.collapse', function() {
                $('.room-type .toggle-icon').removeClass('fa-chevron-up').addClass('fa-chevron-down');
            });

        } catch (error) {
            console.error('Error in booking_info.js:', error);
        }
    }

    // Kiểm tra jQuery đã load chưa
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