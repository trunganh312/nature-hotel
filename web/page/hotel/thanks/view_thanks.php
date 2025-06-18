    <div class='thanks'>
        <div class="box-thanks">
        <header class="thank">
            <span class="checkmark">✔</span>
            <h1>Cảm ơn Quý khách đã sử dụng dịch vụ tại NatureRetreat.com</h1>
        </header>

  
        <main class="content">
            <section class="booking-info">
                <h2>Thông tin người đặt</h2>
                <div class="info-box">
                    <p><strong>Mã đơn:</strong> <?php echo $booking_info['bkho_code']; ?></p>
                    <p><strong>Họ và tên:</strong> <?php echo $booking_info['bkho_name']; ?></p>
                    <p><strong>Điện thoại:</strong> <?php echo $booking_info['bkho_phone']; ?></p>
                    <p><strong>Email:</strong> <?php echo $booking_info['bkho_email']; ?></p>
                </div>

                <div class="hotel-info">
                    <img src="<?php echo $image_hotel ?? $cfg_default_image; ?>" alt="<?php echo htmlspecialchars($booking_info['hot_name']); ?>" style="max-width: 200px; height: auto; object-fit: cover;">
                    <h3><?php echo htmlspecialchars($booking_info['hot_name']); ?></h3>
                    <p class="location"><?php echo htmlspecialchars($booking_info['hot_address_full']); ?></p>
                </div>
            </section>

            <section class="room-info">
                <h2>Thông tin phòng</h2>
                <div class="room-details">
                    <?php
                    // Hiển thị thông tin từng phòng
                    foreach($rooms as $room) {
                        // Tính giá riêng cho từng phòng
                        $roomPrice = isset($room['totalPrice']) ? $room['totalPrice'] : ((isset($room['roomPrice']) ? $room['roomPrice'] : 0) * (int)$room['roomCount']);
                        $priceDisplay = number_format($roomPrice, 0, ',', '.') . 'đ';

                    ?>
                    <div class="room-type" style='display: flex; justify-content: space-between; '>
                        <div>
                            <p><strong><?php echo htmlspecialchars($room['roomName'] ?? 'Không xác định'); ?></strong></p>
                        <p><?php echo (int)($room['roomCount'] ?? 1); ?> phòng x <?php echo (int)$nights; ?> đêm: <?php echo htmlspecialchars($priceDisplay); ?></p>
                        </div>
                        <div>
                            <img src="<?php echo $image_room ?? $cfg_default_image; ?>" alt="<?php echo htmlspecialchars($room['roomName'] ?? 'Không xác định'); ?>"  style="max-width: 200px; height: auto; object-fit: cover;">
                        </div>
                    </div>
                    <?php } ?>

                    <p><strong>Nhận phòng:</strong> <?php echo htmlspecialchars($checkInFormatted ?? $checkIn); ?></p>
                    <p><strong>Trả phòng:</strong> <?php echo htmlspecialchars($checkOutFormatted ?? $checkOut); ?></p>
                    <p><strong>Người lớn:</strong> <?php echo (int)$booking_info['bkho_adult']; ?></p>
                    <?php if ($booking_info['bkho_children'] > 0) { ?>
                        <p><strong>Trẻ em:</strong> <?php echo (int)$booking_info['bkho_children']; ?></p>
                    <?php } ?>
                    <?php if ($booking_info['bkho_baby'] > 0) { ?>
                        <p><strong>Em bé:</strong> <?php echo (int)$booking_info['bkho_baby']; ?></p>
                    <?php } ?>
                    
                    <?php
                    // Lấy tổng tiền từ booking_info
                    $total_price = ($booking_info['bkho_money_total'] ?? 0) * 1.15;
                    
                    // Tính giảm giá 15%
                    $total_discount = $booking_info['bkho_money_total'] * 0.15;
                    $final_price = $total_price - $total_discount;
                    ?>

                    <p><strong>Tổng tiền:</strong> <?php echo format_number($total_price); ?>đ</p>
                    <p><strong>Giảm trừ:</strong> <?php echo format_number($total_discount); ?>đ</p>
                    <p class="total"><strong>Thanh toán:</strong> <?php echo format_number($final_price); ?>đ</p>
                </div>
            </section>
        </main>

        <footer class="footer-note">
            <p>Thông tin chi tiết đơn đặt phòng đã được gửi tới địa chỉ Email: <strong><?php echo $booking_info['bkho_email']; ?></strong></p>
            <a href="<?php echo DOMAIN_WEB; ?>" class="home-link">Quay về trang chủ</a>
        </footer>
    </div>
    </div>