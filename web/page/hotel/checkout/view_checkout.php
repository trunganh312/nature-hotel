<div class="container my-4">
    <div class="row container_main" style="position: relative;">
        <div class="col-lg-7">
            <!-- Thông tin khách sạn -->
            <div class="card mb-3">
                <div class="card-body p-4">
                    <div class="d-flex mb-4 card_body_booking1">
                        <img src="<?= $image_hotel ?>" alt="<?= $hotel_info['hot_name'] ?>"
                            class="rounded me-3 booking_img_firt">
                        <div>
                            <h1 class="card_booking_title"><?= $hotel_info['hot_name'] ?></h1>
                            <div class="mb-2">
                                <!-- Dựa vào số sao để render -->
                                <?php for ($i = 0; $i < $hotel_info['hot_star']; $i++) { ?>
                                    <i class="fas fa-star text-warning_star"></i>
                                <?php } ?>
                            </div>
                            <p class="card_booking_title_text"><?= $hotel_info['hot_address_full'] ?></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <p class="fw-bold mb-0">Nhận phòng</p>
                            <span><?= $hotel_info['hot_checkin'] ?>, <?= $checkIn ?></span>
                        </div>
                        <div class="col-md-4">
                            <p class="fw-bold mb-0">Trả phòng</p>
                            <span><?= $hotel_info['hot_checkout'] ?>, <?= $checkOut ?></span>
                        </div>
                        <div class="col-md-4">
                            <p class="fw-bold mb-0">Số đêm</p>
                            <p><?= $nights ?></p>
                        </div>
                    </div>
                    <div>
                        <p class="fw-bold mb-0">Số phòng</p>
                        <?php foreach ($rooms as $room) { ?>
                            <p><span style="color: var(--primary-color);"><?= $room['roomCount'] ?>x</span> <?= $room['roomName'] ?></p>
                        <?php } ?>
                    </div>
                    <div>
                        <p class="fw-bold mb-0">Đủ chỗ ngủ cho</p>
                        <div><?= $total_adult ?> người lớn</div>
                        <?php if ($total_child > 0) { ?>
                            <div><?= $total_child ?> trẻ em</div>
                        <?php } ?>
                        <?php if ($total_infant > 0) { ?>
                            <div><?= $total_infant ?> em bé</div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="card-body mb-3">
                <h2 class="h5 mb-3">Thông tin phòng</h2>
                <?php foreach ($roomTypeGuests as $room) { ?>
                    <div class="position-relative mb-2 d-flex card_booking_left1">
                        <img src="<?= $room['image'] ?>" alt="<?= $room['roomName'] ?>" class="img_booking_right1">
                    </div>
                    <h3 class="h5 mb-1"><?= $room['roomName'] ?></h3>
                    <div class="list-unstyled mb-3">
                        <li class="mb-1 items_booking_r1">
                            <i class="fas fa-users icon_booking_right1"></i><?= $room['total_adult'] ?> người lớn
                        </li>
                        <li class="mb-1 items_booking_r1">
                            <i class="fas fa-eye icon_booking_right1"></i><?= $room['view'] ?>
                        </li>
                        <li class="mb-1 items_booking_r1">
                            <i class="fas fa-bed icon_booking_right1"></i><?= $room['bed'] ?>
                        </li>
                    </div>
                    <!-- Tags -->
                    <div class="hotel-tags">
                        <h4 class="h6 mb-2">Tiện nghi phòng</h4>
                        <div class="d-flex flex-wrap justify-content-start">
                            <?php foreach($room['tags'] as $tag) { ?>
                                <span class="tag me-4"><i class="<?= $tag['icon'] ?> me-2"></i> <?= $tag['name'] ?></span>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- Thông tin phòng -->
        <div class="col-lg-5 column-right">
            <!-- Thông tin liên hệ -->
            <div class="card mb-3">
                <div class="card-body">
                    <h2>Thông tin liên hệ</h2>
                    <div class="contact">
                        <label for="username">Họ và tên</label>
                        <input type="text" id="username" name="username" value="Nguyễn Văn A">
                    </div>
                    <div class="email-phone">
                        <div class="email">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="nguyenvana@example.com">
                        </div>
                        <div id="phoneForm" class="phone">
                            <label for="phone">Số điện thoại</label>
                            <input id="phone" type="tel" name="phone" value="+84 123 456 789"
                                placeholder="Nhập số điện thoại">
                        </div>
                    </div>
                    <div class="check">
                        <input type="checkbox" id="checkbox">
                        <label for="checkbox">Tôi đặt phòng giúp cho người khác.</label>
                    </div>
                    <div id="nameInput" style="display: none;">
                        <label for="guestName" style="font-weight: 500; font-size: 17px; margin: 13px 0 5px;">Thông tin
                            khách nhận phòng</label>
                        <label for="guestName" style="font-weight: 400; font-size: 16px;">Họ tên</label>
                        <input type="text" id="guestName" name="guestName"
                            style="border: 1px solid #c1c1c1; border-radius: 8px; padding: 8px; width: 100%;">
                    </div>
                </div>
            </div>
            <!-- Chi tiết giá -->
            <div class="price-card">
                <div class="price-card-header">
                    <h2 class="title_pay">Chi tiết giá</h2>
                    <div class="booking-dates">
                        <i class="fa fa-calendar"></i>
                        <span>10/06/2025 - 12/06/2025</span>
                    </div>
                </div>
                
                <!-- Thông tin phòng -->
                <div class="room-detail-section">
                    <div class="room-type-header">
                        <h3>Thông tin phòng</h3>
                        <span class="room-count">2 phòng</span>
                    </div>
                    
                    <div class="room-item">
                        <div class="room-info">
                            <div class="room-name">Phòng Deluxe</div>
                            <div class="room-specs">
                                <span><i class="fa fa-user"></i> 2 người lớn</span>
                                <span><i class="fa fa-bed"></i> 1 giường đôi</span>
                            </div>
                        </div>
                        <div class="room-price">
                            <div class="price-amount">2,000,000₫</div>
                            <div class="night-count">x 2 đêm</div>
                        </div>
                    </div>
                    
                    <div class="room-item">
                        <div class="room-info">
                            <div class="room-name">Phòng Superior</div>
                            <div class="room-specs">
                                <span><i class="fa fa-user"></i> 2 người lớn</span>
                                <span><i class="fa fa-bed"></i> 2 giường đơn</span>
                            </div>
                        </div>
                        <div class="room-price">
                            <div class="price-amount">2,000,000₫</div>
                            <div class="night-count">x 2 đêm</div>
                        </div>
                    </div>
                </div>
                
                <!-- Chi tiết giá -->
                <div class="price-summary-section">
                    <div class="price-row">
                        <div><span>Tổng giá phòng</span></div>
                        <div class="text-end">
                            <span class="promotion">-15%</span>
                            <span class="old-price ms-2">4,000,000₫</span>
                        </div>
                    </div>
                    <div class="original-price">3,400,000₫</div>
                    
                    <!-- Dịch vụ thêm -->
                    <div class="extra-services-section">
                        <h3>Dịch vụ thêm</h3>
                        <div class="service-item">
                            <div class="service-name">Đón sân bay</div>
                            <div class="service-price">Miễn phí</div>
                        </div>
                        <div class="service-item">
                            <div class="service-name">Bữa sáng</div>
                            <div class="service-price">Đã bao gồm</div>
                        </div>
                    </div>
                    
                    <!-- Mã giảm giá -->
                    <div class="discount-section">
                        <div class="discount-sale flex">
                            <div class="name-sale-info">
                                <span class="info-sale">Mã giảm giá</span>
                                <span class="name-sale" id="discountCodeDisplay">SAVE10</span>
                            </div>
                            <span class="reduce-price">-340,000₫</span>
                        </div>
                    </div>
                    
                    <!-- Tổng tiền thanh toán -->
                    <div class="total-price-section">
                        <div class="price-row">
                            <span class="total-label">Tổng tiền thanh toán:</span>
                            <span class="price-sale" id="discountAmountDisplay">3,060,000₫</span>
                        </div>
                        <div class="payment-note">
                            <i class="fa fa-info-circle"></i>
                            <span>Đã bao gồm thuế và phí</span>
                        </div>
                        <div class="text-center mt-4">
                            <button class="btn btn-primary btn-lg" style="width: 100%; max-width: 300px;">
                                Thanh toán ngay
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Handle phone input
    const phoneInput = document.querySelector("#phone");
    const iti = window.intlTelInput(phoneInput, {
        initialCountry: "vn",
        preferredCountries: ["vn", "us"],
        separateDialCode: true,
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"
    });

    // Show guest name input
    const checkbox = document.getElementById("checkbox");
    const nameInput = document.getElementById("nameInput");
    checkbox.addEventListener("change", function() {
        nameInput.style.display = this.checked ? "block" : "none";
    });
</script>