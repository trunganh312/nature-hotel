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
                            <input type="hidden" name="checkIn" value="<?= $checkIn ?>">
                        </div>
                        <div class="col-md-4">
                            <p class="fw-bold mb-0">Trả phòng</p>
                            <span><?= $hotel_info['hot_checkout'] ?>, <?= $checkOut ?></span>
                            <input type="hidden" name="checkOut" value="<?= $checkOut ?>">
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
            <div class="card-body card mb-3">
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
                            <?php foreach ($room['tags'] as $tag) { ?>
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
                        <label for="username">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" id="username" name="username" value="" placeholder="Nhập họ và tên" required>
                    </div>
                    <div class="email-phone">
                        <div class="email">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="" placeholder="Nhập email">
                        </div>
                        <div id="phoneForm" class="phone">
                            <label for="phone">Số điện thoại <span class="text-danger">*</span></label>
                            <input id="phone" type="tel" name="phone" value=""
                                placeholder="Nhập số điện thoại" required>
                        </div>
                    </div>
                    <!-- <div class="check">
                        <input type="checkbox" id="checkbox">
                        <label for="checkbox">Tôi đặt phòng giúp cho người khác.</label>
                    </div> -->
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
                <!-- Thông tin phòng -->
                <div class="room-detail-section">
                    <div class="room-type-header">
                        <h3>Thông tin phòng</h3>
                        <span class="room-count"><?= $total_room ?> phòng</span>
                    </div>
                    <?php foreach ($roomTypeGuests as $room) { ?>
                        <div class="room-item">
                            <div class="room-info">
                                <div class="room-name"><?= $room['roomName'] ?></div>
                                <div class="room-specs">
                                    <span><i class="fa fa-user"></i> <?= $room['adult'] ?> người lớn</span>
                                </div>
                            </div>
                            <div class="room-price">
                                <div class="price-amount"><?= $room['price'] ?>₫</div>
                                <div class="night-count">x <?= $nights ?> đêm</div>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <!-- Chi tiết giá -->
                <div class="price-summary-section">
                    <div class="price-row">
                        <div><span>Tổng giá phòng</span></div>
                        <div class="text-end">
                            <span class="promotion">-15%</span>
                            <span class="old-price ms-2"><?= $total_discount ?>₫</span>
                        </div>
                    </div>
                    <div class="original-price"><?= $total_price ?>₫</div>

                    <!-- Tổng tiền thanh toán -->
                    <div class="total-price-section">
                        <div class="price-row">
                            <span class="total-label">Tổng tiền thanh toán:</span>
                            <span class="price-sale" id="discountAmountDisplay"><?= $total_price ?>₫</span>
                        </div>
                        <div class="text-center mt-4">
                            <button id="btnPayment" class="btn btn-primary btn-lg" style="width: 100%; max-width: 300px;">
                                Thanh toán ngay
                            </button>
                        </div>
                        <!-- Toast Notification -->
                        <div id="toastNotification" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;">
                            <div id="toastAlert" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                                <div class="toast-header">
                                    <strong class="me-auto">Thông báo</strong>
                                    <button type="button" class="btn-close" onclick="closeToast()"></button>
                                </div>
                                <div class="toast-body" id="toastMessage">
                                    Vui lòng điền đầy đủ thông tin trước khi thanh toán
                                </div>
                            </div>
                        </div>
                        <!-- <div class="payment-note">
                            <i class="fa fa-info-circle"></i>
                            <span>Đã bao gồm thuế và phí</span>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Form validation and toast notification
    document.getElementById('btnPayment').addEventListener('click', function(e) {
        e.preventDefault();

        // Kiểm tra tất cả các trường bắt buộc
        const username = document.getElementById('username').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const email = document.getElementById('email').value.trim();

        if (!username || !phone) {
            showToast('Vui lòng điền đầy đủ thông tin trước khi thanh toán');
            return false;
        }
        // Kiểm tra định dạng email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (email) {
            if (!emailRegex.test(email)) {
                showToast('Email không hợp lệ');
                return false;
            }
        }
        // Call api
        const checkIn = document.querySelector('input[name="checkIn"]').value;
        const checkOut = document.querySelector('input[name="checkOut"]').value;
        $.ajax({
            url: 'ajax/get_rooms.php',
            type: 'POST',
            data: {
                checkIn: checkIn,
                checkOut: checkOut,
                hotel_id: <?= $hotel_info['hot_id'] ?>
            },
            success: function(response) {
                // Dữ liệu phòng trống thực tế từ DB
                const data = JSON.parse(response);
                // Dữ liệu phòng đã chọn từ session
                const roomTypeGuests = <?= json_encode($roomTypeGuests) ?>;

                let isValid = true;
                let errorMsg = '';

                roomTypeGuests.forEach(selected => {
                    // Tìm phòng tương ứng trong data trả về từ DB
                    const dbRoom = data.find(r => parseInt(r.room_id) === parseInt(selected.roomId));
                    if (!dbRoom) {
                        isValid = false;
                        errorMsg += `Không tìm thấy dữ liệu phòng ${selected.roomName}.\n`;
                        return;
                    }
                    if (selected.roomCount > dbRoom.min_qty) {
                        isValid = false;
                        errorMsg += `Số phòng "${selected.roomName}" bạn chọn (${selected.roomCount}) vượt quá số phòng còn lại (${dbRoom.min_qty}).\n`;
                    }
                });

                if (!isValid) {
                    showToast(errorMsg || 'Có lỗi về số lượng phòng. Vui lòng kiểm tra lại!');
                    return;
                }

                // Nếu hợp lệ, tiếp tục chuyển hướng hoặc xử lý thanh toán
                window.location.href = '<?= $redirect_url ?>';
            }
        });
    });

    // Hiển thị toast notification
    function showToast(message) {
        const toastContainer = document.getElementById('toastNotification');
        const toast = document.getElementById('toastAlert');
        const toastMessage = document.getElementById('toastMessage');

        // Cập nhật nội dung
        toastMessage.textContent = message;

        // Hiển thị toast
        toast.classList.add('show');
        toastContainer.style.display = 'block';

        // Tự động ẩn toast sau 3 giây
        setTimeout(function() {
            closeToast();
        }, 3000);
    }

    // Đóng toast
    function closeToast() {
        const toastContainer = document.getElementById('toastNotification');
        const toast = document.getElementById('toastAlert');
        toast.classList.remove('show');
        setTimeout(() => {
            toastContainer.style.display = 'none';
        }, 150);
    }
</script>