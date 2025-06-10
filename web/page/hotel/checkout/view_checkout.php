    <div class="checkout">
        <div class="checkout-container ">
            <h1 class="section-title text-center mb-4">Đặt Phòng</h1>
            <div class="row checkout-grid">
                <!-- Thông tin khách sạn và Thông tin liên hệ -->
                <div class="col-lg-8">
                    <!-- Thông tin khách sạn -->
                    <div class="payment-form">
                        <div class=" mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <img src="<?php echo htmlspecialchars($image_hotel); ?>" alt="<?php echo htmlspecialchars($hotel_info['hot_name']); ?>" class="hotel-image mb-3">
                                        <div class="ms-3">
                                            <h4 class="hotel-name mb-2"><?php echo htmlspecialchars($hotel_info['hot_name']); ?></h4>
                                            <p class="hotel-address mb-2"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($hotel_info['hot_address_full']); ?></p>
                                        </div>
                                    </div>
                                    <div class="d-flex ">

                                        <div class="box-in d-flex flex-column  mb-1" style='margin-right: 20px;'>
                                            <div><i class="fas fa-calendar-check"></i> Nhận phòng:</div>
                                            <div><i class="fas fa-calendar-times"></i> Trả phòng:</div>
                                            <div><i class="fas fa-users"></i> Số người:</div>
                                        </div>
                                        <div class="d-flex flex-column  mb-1">
                                            <div><?php echo htmlspecialchars($booking_info['checkin_date']); ?></div>
                                            <div><?php echo htmlspecialchars($booking_info['checkout_date']); ?></div>
                                            <div><?php echo htmlspecialchars($booking_info['adult_qty']); ?> người lớn, <?php echo htmlspecialchars($booking_info['child_qty']); ?> trẻ em, <?php echo htmlspecialchars($booking_info['baby_qty']); ?> em bé</div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Thông tin liên hệ -->
                    <div class="payment-form">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h2 class="mb-3 " style='font-weight: bold;'>Thông tin liên hệ</h2>

                                <form action="/process_payment.php" method="POST" class="needs-validation" novalidate>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="full_name" class="form-label">Họ và Tên <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                                    <input type="text" class="form-control" id="full_name" name="full_name" required>
                                                    <div class="invalid-feedback">Vui lòng nhập họ và tên.</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                                    <input type="email" class="form-control" id="email" name="email" required>
                                                    <div class="invalid-feedback">Vui lòng nhập email hợp lệ.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                                    <input type="tel" class="form-control" id="phone" name="phone" required>
                                                    <div class="invalid-feedback">Vui lòng nhập số điện thoại.</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="nationality" class="form-label">Quốc tịch</label>
                                                <select class="form-select" id="nationality" name="nationality">
                                                    <option value="VN">Việt Nam</option>
                                                    <option value="US">Hoa Kỳ</option>
                                                    <option value="CN">Trung Quốc</option>
                                                    <!-- Thêm các quốc gia khác nếu cần -->
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="special_requests" class="form-label">Yêu cầu đặc biệt</label>
                                        <textarea class="form-control" id="special_requests" name="special_requests" rows="4" placeholder="Lưu ý: Các yêu cầu chỉ được đáp ứng tùy tình trạng phòng."></textarea>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thông tin các hạng phòng -->
                <div class="col-lg-4 booking-summary">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="mb-3" style='font-weight: bold;'>Thông tin các hạng phòng</h2>
                            <?php foreach($room_book as $room) { ?>
                            <div class="room-info mb-3">
                                <img src="<?php echo htmlspecialchars($room['image']); ?>" alt="<?php echo htmlspecialchars($room['roo_name']); ?>" class="room-image mb-2">
                                <div class="d-flex flex-column">
                                    <span class="fw-bold"><?php echo htmlspecialchars($room['roo_name']); ?></span>
                                    <span class="text-muted small"><?php echo htmlspecialchars($room['view']); ?></span>
                                    <span class="text-muted small"><?php echo htmlspecialchars($room['bed']); ?></span>
                                    <span class="text-muted small">1 phòng x 1 đêm</span>
                                    <span class="text-primary-color fw-bold">1,500,000 VNĐ</span>
                                </div>
                            </div>
                            <?php } ?>
                            <div class="summary-item total">
                                <span><i class="fas fa-money-bill"></i> Tổng cộng:</span>
                                <span><?php echo number_format($booking['total_price'] ?? 3000000, 0, ',', '.') . ' VNĐ'; ?></span>
                            </div>
                            <!-- Nút xác nhận -->
                            <button type="submit" class="btn btn-primary submit-button btn-block">Xác Nhận Đặt Phòng</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>