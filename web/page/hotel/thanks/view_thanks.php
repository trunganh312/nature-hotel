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
                    <img src="https://cdn3.ivivu.com/2014/01/SUPER-DELUXE2.jpg" alt="Nature Retreat Vũng Tàu">
                    <h3><?php echo $booking_info['hot_name']; ?></h3>
                    <p class="location"><?php echo $booking_info['hot_address_full']; ?></p>
                </div>
            </section>

            <section class="room-info">
                <h2>Thông tin phòng</h2>
                <div class="room-details">
                    <?php
                    // Tính tổng số người và tiền
                    $total_adult = 0;
                    $total_child = 0;
                    $total_infant = 0;
                    $total_price = 0;
                    
                    // Hiển thị thông tin từng phòng
                    foreach($rooms as $room) {
                        $total_adult += isset($room['adult']) ? $room['adult'] : 0;
                        $total_child += isset($room['child']) ? $room['child'] : 0;
                        $total_infant += isset($room['infant']) ? $room['infant'] : 0;
                        $total_price += isset($room['totalPrice']) ? $room['totalPrice'] : 0;
                        //TODO: đang hardcode
                        // Định dạng giá nếu không có priceFormatted
                        $priceDisplay = isset($room['priceFormatted']) 
                            ? $room['priceFormatted'] 
                            : number_format($room['totalPrice'] ?? 0, 0, ',', '.') . 'đ';
                    ?>
                    <div class="room-type">
                        <p><strong><?php echo $room['roomName']; ?></strong></p>
                        <p><?php echo $room['roomCount']; ?> phòng x <?php echo $nights; ?> đêm: <?php echo $priceDisplay; ?></p>
                    </div>
                    <?php } ?>
                    
                    <p><strong>Nhận phòng:</strong> <?php echo $checkInFormatted ?? $checkIn; ?></p>
                    <p><strong>Trả phòng:</strong> <?php echo $checkOutFormatted ?? $checkOut; ?></p>
                    <p><strong>Người lớn:</strong> <?php echo $total_adult; ?></p>
                    <?php if ($total_child > 0) { ?>
                    <p><strong>Trẻ em:</strong> <?php echo $total_child; ?></p>
                    <?php } ?>
                    <?php if ($total_infant > 0) { ?>
                    <p><strong>Em bé:</strong> <?php echo $total_infant; ?></p>
                    <?php } ?>
                    
                    <?php
                    // Tính giảm giá 15%
                    $total_discount = $total_price * 0.15;
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
            <a href="#" class="home-link">Quý về trang chủ</a>
        </footer>
    </div>
    </div>