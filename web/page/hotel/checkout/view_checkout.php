    <div class="ok">
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
                                <img src="https://cdn3.ivivu.com/2014/01/SUPER-DELUXE2.jpg" alt="Hotel Image" class="hotel-image mb-3">
                                <div class="ms-3">
                                    <h4 class="hotel-name mb-2"><?php echo htmlspecialchars($booking['hotel_name'] ?? 'Nature Hotel'); ?></h4>
                                    <p class="hotel-address mb-2"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($booking['address'] ?? '123 Đường Láng, Hà Nội, Việt Nam'); ?></p>
                                </div>
                            </div>
                            <div class="d-flex ">

                                <div class="box-in d-flex flex-column  mb-1" style='margin-right: 20px;'>
                                    <div><i class="fas fa-calendar-check"></i> Nhận phòng:</div>
                                     <div><i class="fas fa-calendar-times"></i> Trả phòng:</div>
                                     <div><i class="fas fa-moon"></i> Số đêm:</div>
                                      <div><i class="fas fa-door-open"></i> Số phòng:</div>
                                       <div><i class="fas fa-users"></i> Số người:</div>
                                </div>
                                <div  class="d-flex flex-column  mb-1">
 <div><?php echo htmlspecialchars($booking['check_in_date'] ?? 'Thứ 6, 06 tháng 6'); ?> - <?php echo htmlspecialchars($booking['check_in_time'] ?? '12:00'); ?></div>
         <div><?php echo htmlspecialchars($booking['check_out_date'] ?? 'Thứ 7, 07 tháng 6'); ?> - <?php echo htmlspecialchars($booking['check_out_time'] ?? '14:00'); ?></div>
            <div>1 đêm</div>
            <div>1 phòng Deluxe, 1 phòng Superior</div>
                   <div>2 người lớn, 1 trẻ em, 0 em bé</div>
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
                                <div class="row">
                                    
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="checkin_time" class="form-label">Thời gian nhận phòng dự kiến</label>
                                            <select class="form-select" id="checkin_time" name="checkin_time">
                                                <option value="">Tôi chưa biết</option>
                                                <option value="12:00">12:00</option>
                                                <option value="13:00">13:00</option>
                                                <option value="14:00">14:00</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                               
                                <div class="invoice-details" style="display: none;">
                                    <div class="form-group mb-3">
                                        <label for="company_name" class="form-label">Tên công ty</label>
                                        <input type="text" class="form-control" id="company_name" name="company_name">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="tax_code" class="form-label">Mã số thuế</label>
                                        <input type="text" class="form-control" id="tax_code" name="tax_code">
                                    </div>
                                </div>
                              
                                <!-- Thông tin thẻ tín dụng -->
                                <div class="credit-card-info" style="display: none;">
                                    <div class="form-group mb-3">
                                        <label for="card_number" class="form-label">Số thẻ</label>
                                        <input type="text" class="form-control" id="card_number" name="card_number">
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group mb-3">
                                                <label for="expiry_date" class="form-label">Ngày hết hạn</label>
                                                <input type="text" class="form-control" id="expiry_date" name="expiry_date" placeholder="MM/YY">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-group mb-3">
                                                <label for="cvv" class="form-label">CVV</label>
                                                <input type="text" class="form-control" id="cvv" name="cvv">
                                            </div>
                                        </div>
                                    </div>
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
                        <!-- Hạng phòng 1: Deluxe -->
                        <div class="room-info mb-3">
                            <img src="https://cdn3.ivivu.com/2014/01/SUPER-DELUXE2.jpg" alt="Phòng Deluxe" class="room-image mb-2">
                            <div class="d-flex flex-column">
                                <span class="fw-bold">Phòng Deluxe</span>
                                <span class="text-muted small">View thành phố</span>
                                <span class="text-muted small">Giường đôi</span>
                                <span class="text-muted small">1 phòng x 1 đêm</span>
                                <span class="text-primary-color fw-bold">1,500,000 VNĐ</span>
                            </div>
                        </div>
                        <!-- Hạng phòng 2: Superior -->
                        <div class="room-info mb-3">
                            <img src="https://cdn3.ivivu.com/2014/01/SUPER-DELUXE2.jpg" alt="Phòng Superior" class="room-image mb-2">
                            <div class="d-flex flex-column">
                                <span class="fw-bold">Phòng Superior</span>
                                <span class="text-muted small">View vườn</span>
                                <span class="text-muted small">Giường đơn</span>
                                <span class="text-muted small">1 phòng x 1 đêm</span>
                                <span class="text-primary-color fw-bold">1,200,000 VNĐ</span>
                            </div>
                        </div>
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