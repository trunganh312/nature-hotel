<?php
// // Tạo mảng với nhiều ảnh khác nhau
// $images = [
//     "https://gcs.tripi.vn/hms_prod/photo/img/456705ZyJMIQ/z3097370517030_de87172ad8397d5d257dfa80310804b9.jpg",
//     "https://cdn3.ivivu.com/2014/01/ztq1372403761.jpg",
//     "https://cdn3.ivivu.com/2014/01/20762698_images1477905_6.jpg",
//     "https://cdn3.ivivu.com/2014/01/Khach-san-Midtown-Hue-4-Sao-Lobby.jpg",
//     "https://cdn3.ivivu.com/2014/01/be-boi-horison-hoi-nghi-khach-hang.jpg",
//     "https://cdn3.ivivu.com/2014/01/ngoai-canh-khach-san-ha-an.jpg", 
//     "https://cdn3.ivivu.com/2014/01/diemhencafe1.jpg",
//     "https://cdn3.ivivu.com/2014/01/Deluxe-18687.jpg",
//     "https://cdn3.ivivu.com/2014/01/khach-san-the-mira-hue-be-boi.jpg",
//     "https://cdn3.ivivu.com/2014/01/phong-executive-khach-san-golden-river-hue.jpg"
// ];

// // Gán mảng ảnh cho từng phòng
// foreach ($rooms as $key => $room) {
//     $rooms[$key]['images'] = $images;
// }
?>

<?php if (empty($rooms)) return; ?>
<?php foreach ($rooms as $idx => $room): ?>
<div class="room-card mb-4" id="room-card-<?php echo $room['roo_id']; ?>">
    <?php if ($idx == 0): ?>
    <div class="room-card-header">
        <span class="recommended-badge">
            <svg width="14" height="14" fill="none" class="star-icon">
                <path
                    d="M12.464 5.684a1.055 1.055 0 00-.849-.719L9.078 4.6 7.94 2.31a1.05 1.05 0 00-1.88 0L4.925 4.598l-2.536.367a1.057 1.057 0 00-.87 1.292c.047.191.148.365.29.502L3.64 8.534l-.433 2.51a1.05 1.05 0 001.521 1.107l2.272-1.188 2.273 1.19a1.05 1.05 0 001.522-1.108l-.435-2.51 1.832-1.776a1.05 1.05 0 00.271-1.075z"
                    fill="#FFBC39"></path>
            </svg>
            Được đề xuất
        </span>
    </div>
    <?php endif; ?>
    <div class="room-card-content">
        <div class="row">
            <div class="room-images-section col-md-3">
                <div class="image-gallery">
                    <div class="main-image">
                        <img src="<?php echo htmlspecialchars($room['images'][0]); ?>" alt="<?php echo htmlspecialchars($room['roo_name']); ?>">
                    </div>
                    <div class="thumbnail-images">
                        <img src="<?php echo htmlspecialchars($room['images'][1]); ?>" alt="<?php echo htmlspecialchars($room['roo_name']); ?>">
                        <img src="<?php echo htmlspecialchars($room['images'][2]); ?>" alt="<?php echo htmlspecialchars($room['roo_name']); ?>">
                        <img src="<?php echo htmlspecialchars($room['images'][3]); ?>" alt="<?php echo htmlspecialchars($room['roo_name']); ?>">
                    </div>
                </div>
                <button class="view-details-button" data-bs-toggle="modal" data-bs-target="#roomModal<?php echo $room['roo_id']; ?>">
                    Xem chi tiết phòng
                    <svg width="16" height="16" fill="none">
                        <path d="M6 12l4-4-4-4" stroke="#6b9c6e" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </button>
                <div class="amenities-list">
                    <?php foreach ($room['utilities'] as $idx => $utility): ?>
                    <?php if ($idx < 6): ?>
                    <span class="amenity-item"><?php echo htmlspecialchars($utility['name']); ?></span>
                    <?php endif; ?>
                    <?php endforeach; ?>
                    <span class="amenity-count"><?php echo count($room['utilities']); ?> tiện ích</span>
                </div>
            </div>
            <div class="room-details-section col-md-9">
                <div class="row">
                    <h3 class="room-title"><?php echo htmlspecialchars($room['roo_name']); ?></h3>
                    <div class="room_details_room-info pb-3">
                        <div class="info-item">
                            <svg width="16" height="16" fill="none">
                                <path
                                    d="M2 14v-1.333A2.667 2.667 0 014.667 10h2.666A2.667 2.667 0 0110 12.667V14m.667-11.913a2.667 2.667 0 010 5.166M14 14v-1.333a2.667 2.667 0 00-2-2.567M8.667 4.667a2.667 2.667 0 11-5.334 0 2.667 2.667 0 015.334 0z"
                                    stroke="#4A5568" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                            <span><?php echo $room['roo_max_adult']; ?> người</span>
                            <!-- <span class="detail-link">(Xem chi tiết)</span> -->
                        </div>
                        <div class="info-item">
                            <svg width="16" height="16" fill="none">
                                <path d="M12 2H4a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2V4a2 2 0 00-2-2z"
                                    stroke="#4A5568" stroke-miterlimit="10" stroke-linecap="square"></path>
                                <path
                                    d="M11.333 11.333L5 5M11.334 8.333v3h-3M4.483 2.667L7.586 5.77 5.103 8.253 2 5.149M3.241 13.838l10.552-10.55a5.036 5.036 0 01-1.242 4.965c-2.194 2.194-3.724 2.482-3.724 2.482"
                                    stroke="#4A5568" stroke-miterlimit="10" stroke-linecap="round"
                                    stroke-linejoin="round"></path>
                            </svg>
                            <span><?php echo $room['roo_square_meters']; ?> m²</span>
                        </div>
                        <div class="info-item">
                            <svg width="16" height="16" fill="none">
                                <path d="M8 9.333a1.333 1.333 0 100-2.666 1.333 1.333 0 000 2.666z" stroke="#4A5568"
                                    stroke-linecap="round" stroke-linejoin="round"></path>
                                <path
                                    d="M14.667 8c-1.778 3.111-4 4.667-6.667 4.667S3.11 11.11 1.333 8c1.778-3.111 4-4.667 6.667-4.667S12.889 4.89 14.667 8z"
                                    stroke="#4A5568" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                            <span><?php echo htmlspecialchars($room['attr_view']); ?></span>
                        </div>
                    </div>
                </div>
                <div class="row pt-3">
                    <div class="col-md-5">
                        <div class="option-header">
                            <div class="option-tags">
                                <span class="tag">
                                    <svg width="16" height="16" fill="none">
                                        <path
                                            d="M13.444 6.111H5.667c-.86 0-1.556.696-1.556 1.556v4.666c0 .86.697 1.556 1.556 1.556h7.777c.86 0 1.556-.697 1.556-1.556V7.667c0-.86-.697-1.556-1.556-1.556z"
                                            stroke="#4A5568" stroke-linecap="round" stroke-linejoin="round"></path>
                                        <path
                                            d="M9.556 11.555a1.556 1.556 0 100-3.11 1.556 1.556 0 000 3.11zM11.889 6.111V4.556A1.556 1.556 0 0010.333 3H2.556A1.556 1.556 0 001 4.556v4.666a1.555 1.555 0 001.556 1.556H4.11"
                                            stroke="#4A5568" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                    Hoàn huỷ một phần
                                </span>
                                <span class="tag">
                                    <svg width="16" height="16" fill="none">
                                        <path
                                            d="M11.31 11.976l1.862 1.862M3.241 3.908l4.966 4.965M4.483 2.667L7.586 5.77 5.103 8.253 2 5.149M3.241 13.838l10.552-10.55a5.036 5.036 0 01-1.242 4.965c-2.194 2.194-3.724 2.482-3.724 2.482"
                                            stroke="#48BB78" stroke-miterlimit="10" stroke-linecap="round"
                                            stroke-linejoin="round"></path>
                                    </svg>
                                    Giá đã bao gồm bữa sáng
                                </span>
                                <?php if ($room['roo_extra_bed'] == 1): ?>
                                    <span class="tag">
                                    <svg width="16" height="16" fill="none">
                                        <path d="M12.739 6.478L6.652 15l1.217-5.478H3L9.087 1 7.87 6.478h4.87z"
                                            stroke="#ED8936" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                    Có thể kê thêm giường phụ
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 room_detail_boder_bed">
                        <div class="bed-info">
                            <svg width="24" height="24" fill="none">
                                <path
                                    d="M22.5 11.75h-21a1.5 1.5 0 00-1.5 1.5v4a1.5 1.5 0 001.125 1.45.5.5 0 01.375.483v1.067a1 1 0 102 0v-1a.5.5 0 01.5-.5h16a.5.5 0 01.5.5v1a1 1 0 002 0v-1.064a.5.5 0 01.375-.483A1.5 1.5 0 0024 17.25v-4a1.5 1.5 0 00-1.5-1.5zM2.5 10.25a.5.5 0 00.5.5h18a.5.5 0 00.5-.5v-5a2.5 2.5 0 00-2.5-2.5H5a2.5 2.5 0 00-2.5 2.5v5zm4-3h2a2.5 2.5 0 012.166 1.25.5.5 0 01-.433.75H4.767a.5.5 0 01-.433-.75A2.5 2.5 0 016.5 7.25zm9 0h2a2.5 2.5 0 012.166 1.25.5.5 0 01-.433.75h-5.466a.5.5 0 01-.433-.75A2.5 2.5 0 0115.5 7.25z"
                                    fill="#718096"></path>
                            </svg>
                            <div class="bed-text"><?php echo htmlspecialchars($room['attr_bed']); ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="pricing">
                            <div class="final-price"><?php echo number_format($room['price'] ?? 1200000, 0, ',', '.'); ?> VND</div>

                            <div class="room-quantity">
                                <div class="quantity-label">Số phòng:</div>
                                <div class="quantity-control">
                                    <button class="quantity-decrease"><i class="fa-solid fa-minus"></i></button>
                                    <span class="quantity-display" id="quantity-display-<?php echo $room['roo_id']; ?>">0 / <?php echo isset($room['roo_max_rooms']) ? $room['roo_max_rooms'] : 10; ?></span>
                                    <input type="number" id="room-quantity-<?php echo $room['roo_id']; ?>" name="room_quantity" class="quantity-input-hidden" value="0" min="0" max="<?php echo isset($room['roo_max_rooms']) ? $room['roo_max_rooms'] : 10; ?>" style="display: none;">
                                    <button class="quantity-increase"><i class="fa-solid fa-plus"></i></button>
                                </div>
                            </div>

                            <div class="guest-selection-container" id="guest-selection-<?php echo $room['roo_id']; ?>">
                                <!-- Sẽ được thêm bằng JS -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="roomModal<?php echo $room['roo_id']; ?>" tabindex="-1" aria-labelledby="roomModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="roomModalLabel"><?php echo htmlspecialchars($room['roo_name']); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body row">
                <div class="room-carousel-wrapper col-md-7">
                    <div class="owl-carousel owl-theme room-carousel">
                        <?php foreach ($room['images'] as $image): ?>
                            <div class="item">
                                <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($room['roo_name']); ?>" class="img-fluid rounded carousel-image">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="thumbnail-container mt-3">
                        <ul class="thumbnail-list d-flex justify-content-center">
                            <?php foreach ($room['images'] as $index => $image): ?>
                                <li class="thumbnail-item <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>">
                                    <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($room['roo_name']); ?>" class="thumb-image">
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="col-md-5 mt-4 room-details-scroll">
                    <h6><i class="fas fa-users me-2"></i> Sức chứa</h6>
                    <ul class="room-details-list">
                        <li>Sức chứa tối đa: <?php echo $room['roo_max_adult']; ?> người</li>
                        <li>Số khách tiêu chuẩn: <?php echo $room['roo_adult']; ?> người</li>
                        <li>Cho phép ở thêm: <?php echo $room['roo_max_children']; ?> trẻ em</li>
                    </ul>
                    <div class="">
                        <div class="col-md-6 d-flex">
                            <i class="fas fa-ruler-combined me-2"></i>
                            <p class="room-details-text ms-2"><?php echo htmlspecialchars($room['roo_square_meters']); ?> m²</p>
                        </div>
                        <div class="col-md-6 d-flex">
                            <i class="fas fa-eye me-2"></i>
                            <p class="room-details-text ms-2"><?php echo htmlspecialchars($room['attr_view']); ?></p>
                        </div>
                    </div>
                    <h6 class="mb-3"><i class="fas fa-concierge-bell me-2"></i> Tiện nghi phòng</h6>
                    <div class="row">
                        <?php foreach ($room['utilities'] as $idx => $utility): ?>
                            <div class="col-6 mb-3 d-flex align-items-center">
                                <?php if ($utility['icon']): ?>
                                    <i class="<?php echo htmlspecialchars($utility['icon']); ?>"></i>
                                <?php else: ?>
                                    <i class="fas fa-ban me-2"></i>
                                <?php endif; ?>
                                <span class="room-details-text ms-2"><?php echo htmlspecialchars($utility['name']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
<div id="end-sticky-point" style="height: 0; overflow: hidden;" class="pt-4"></div>
<script>
// Mảng toàn cục để lưu trữ các hạng phòng đã chọn
let selectedRooms = [];

function initRoomDetailJS() {
    try {
        if (!window.jQuery || !window.bootstrap || !$.fn.owlCarousel) {
            console.error('Required dependencies (jQuery, Bootstrap, or Owl Carousel) are missing.');
            return;
        }

        const debounce = (func, wait) => {
            let timeout;
            return (...args) => {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        };
        const initializeModal = (button) => {
            const target = button.getAttribute('data-bs-target');
            if (!target) {
                console.warn('No data-bs-target found for button:', button);
                return;
            }

            const modalElement = document.querySelector(target);
            if (!modalElement) {
                console.warn('Modal element not found for target:', target);
                return;
            }

            const modal = new bootstrap.Modal(modalElement, {
                backdrop: true,
                keyboard: true
            });

            const carousel = modalElement.querySelector('.room-carousel');
            let carouselInitialized = false;

            if (carousel && !carouselInitialized) {
                $(carousel).owlCarousel({
                    loop: false,
                    margin: 10,
                    nav: true,
                    dots: false,
                    items: 1,
                    navText: [
                        '<i class="fas fa-chevron-left"></i>',
                        '<i class="fas fa-chevron-right"></i>'
                    ]
                });
                carouselInitialized = true;
            }

            const thumbnailContainer = modalElement.querySelector('.thumbnail-container');
            if (thumbnailContainer) {
                thumbnailContainer.addEventListener('click', (e) => {
                    const thumbnail = e.target.closest('.thumbnail-item');
                    if (thumbnail) {
                        const index = parseInt(thumbnail.getAttribute('data-index'));
                        $(carousel).trigger('to.owl.carousel', [index, 300]);
                        thumbnailContainer.querySelectorAll('.thumbnail-item').forEach(t => t.classList.remove('active'));
                        thumbnail.classList.add('active');
                    }
                });
            }

            if (carousel) {
                $(carousel).on('changed.owl.carousel', (event) => {
                    const currentIndex = event.item.index % event.item.count;
                    const thumbnails = thumbnailContainer.querySelectorAll('.thumbnail-item');
                    thumbnails.forEach(thumb => {
                        thumb.classList.toggle('active', parseInt(thumb.getAttribute('data-index')) === currentIndex);
                    });
                });
            }

            modalElement.addEventListener('hidden.bs.modal', () => {
                if (carousel && carouselInitialized) {
                    $(carousel).owlCarousel('destroy');
                    carouselInitialized = false;
                }
                document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
            });

            modal.show();
        };

        const viewDetailsButtons = document.querySelectorAll('.view-details-button');
        viewDetailsButtons.forEach(button => {
            button.addEventListener('click', debounce(() => initializeModal(button), 300));
        });

        // Hàm cập nhật thông tin đặt phòng và kích hoạt sự kiện
        function updateBookingInfo(roomCard) {
            const roomId = roomCard.id.split('room-card-')[1];
            const roomName = roomCard.querySelector('.room-title').textContent;
            const quantityInput = roomCard.querySelector(`#room-quantity-${roomId}`);
            const roomCount = parseInt(quantityInput.value) || 0;
            const maxRooms = parseInt(quantityInput.getAttribute('max')) || 10;
            const guestContainer = roomCard.querySelector(`#guest-selection-${roomId}`);
            const guestInputs = guestContainer.querySelectorAll('.guest-input-field');

            // Cập nhật hiển thị số phòng hiện tại / tối đa trong quantity-display
            const quantityDisplay = roomCard.querySelector(`#quantity-display-${roomId}`);
            if (quantityDisplay) {
                quantityDisplay.textContent = `${roomCount} / ${maxRooms}`;
            }

            // Tạo danh sách các phòng với thông tin khách
            const rooms = [];
            for (let i = 1; i <= roomCount; i++) {
                const adults = parseInt(guestContainer.querySelector(`#adults${roomId}_${i}`)?.value) || 2;
                const children = parseInt(guestContainer.querySelector(`#children${roomId}_${i}`)?.value) || 0;
                const infants = parseInt(guestContainer.querySelector(`#babies${roomId}_${i}`)?.value) || 0;
                rooms.push({ adults, children, infants });
            }

            const finalPriceText = roomCard.querySelector('.final-price').textContent;
            const roomPrice = parseInt(finalPriceText.replace(/[^0-9]/g, '')) || 1200000;

            // Cập nhật mảng selectedRooms
            const existingRoomIndex = selectedRooms.findIndex(room => room.roomId === roomId);
            if (existingRoomIndex >= 0) {
                if (roomCount === 0) {
                    selectedRooms.splice(existingRoomIndex, 1); // Xóa nếu số lượng bằng 0
                } else {
                    selectedRooms[existingRoomIndex] = {
                        roomId,
                        roomName,
                        roomCount,
                        rooms,
                        roomPrice
                    };
                }
            } else if (roomCount > 0) {
                selectedRooms.push({
                    roomId,
                    roomName,
                    roomCount,
                    rooms,
                    roomPrice
                });
            }

            // Lấy ngày check-in và check-out
            const checkIn = document.querySelector('input[name="check_in"]')?.value || '2025-06-10';
            const checkOut = document.querySelector('input[name="check_out"]')?.value || '2025-06-13';
            const nights = Math.ceil((new Date(checkOut) - new Date(checkIn)) / (1000 * 60 * 60 * 24)) || 1;

            // Kích hoạt sự kiện để cập nhật booking_info
            const event = new CustomEvent('roomSelectionChange', {
                detail: {
                    selectedRooms,
                    checkIn,
                    checkOut,
                    nights
                }
            });
            window.dispatchEvent(event);
        }

        // Khởi tạo quantity controls và guest selection
        const quantityControls = document.querySelectorAll('.quantity-control');
        quantityControls.forEach(control => {
            const decreaseBtn = control.querySelector('.quantity-decrease');
            const increaseBtn = control.querySelector('.quantity-increase');
            const input = control.querySelector('.quantity-input-hidden');
            const display = control.querySelector('.quantity-display');
            const container = control.closest('.pricing').querySelector('.guest-selection-container');
            const roomCard = control.closest('.room-card');

            decreaseBtn.addEventListener('click', () => {
                if (parseInt(input.value) > 0) {
                    input.value = parseInt(input.value) - 1;
                    updateGuestSelection(input, container); // Cập nhật guest selection
                    updateBookingInfo(roomCard);
                }
            });

            increaseBtn.addEventListener('click', () => {
                const maxRooms = parseInt(input.getAttribute('max')) || 10;
                if (parseInt(input.value) < maxRooms) {
                    input.value = parseInt(input.value) + 1;
                    updateGuestSelection(input, container); // Cập nhật guest selection
                    updateBookingInfo(roomCard);
                }
            });

            input.addEventListener('change', () => {
                const maxRooms = parseInt(input.getAttribute('max')) || 10;
                if (parseInt(input.value) > maxRooms) {
                    input.value = maxRooms;
                } else if (parseInt(input.value) < 0) {
                    input.value = 0;
                }
                updateGuestSelection(input, container); // Cập nhật guest selection
                updateBookingInfo(roomCard);
            });

            // Lắng nghe thay đổi trong guest selection
            container.addEventListener('change', (e) => {
                if (e.target.classList.contains('guest-input-field')) {
                    updateBookingInfo(roomCard);
                }
            });

            // Khởi tạo lần đầu
            updateGuestSelection(input, container);
            updateBookingInfo(roomCard);
        });

        function updateGuestSelection(input, container) {
            const roomCount = parseInt(input.value) || 0;
            const uniqueId = container.id.split('guest-selection-')[1];
            let html = '';
            for (let i = 1; i <= roomCount; i++) {
                html += `
                    <div class="guest-selection">
                        <div class="guest-title">Phòng ${i}</div>
                        <div class="row">
                            <div class="col-md-5 guest-input pe-0">
                                <div class="guest-label"><label for="adults${uniqueId}_${i}">Người lớn:</label></div>
                                <input id="adults${uniqueId}_${i}" type="number" min="1" max="4" value="2" class="guest-input-field">
                                <div class="guest-indicator"></div>
                            </div>
                            <div class="col-md-4 guest-input px-0">
                                <div class="guest-label"><label for="children${uniqueId}_${i}">Trẻ em:</label></div>
                                <input id="children${uniqueId}_${i}" type="number" min="0" max="3" value="0" class="guest-input-field">
                                <div class="guest-indicator"></div>
                            </div>
                            <div class="col-md-3 guest-input px-0">
                                <div class="guest-label"><label for="babies${uniqueId}_${i}">Em bé:</label></div>
                                <input id="babies${uniqueId}_${i}" type="number" min="0" max="2" value="0" class="guest-input-field">
                                <div class="guest-indicator"></div>
                            </div>
                        </div>
                    </div>
                `;
            }
            container.innerHTML = html;
            setupGuestIndicator();
        }

        function setupGuestIndicator() {
            const inputFields = document.querySelectorAll('.guest-input-field');
            inputFields.forEach((input) => {
                const guestInput = input.closest('.guest-input');
                const indicator = guestInput.querySelector('.guest-indicator');
                input.addEventListener('focus', () => {
                    document.querySelectorAll('.guest-indicator').forEach(ind => {
                        ind.style.width = '0';
                        ind.style.opacity = '0';
                    });
                    indicator.style.width = '100%';
                    indicator.style.opacity = '1';
                });
            });
            document.addEventListener('click', (e) => {
                if (!e.target.classList.contains('guest-input-field') && !e.target.closest('.guest-label')) {
                    document.querySelectorAll('.guest-indicator').forEach(ind => {
                        ind.style.width = '0';
                        ind.style.opacity = '0';
                    });
                }
            });
        }

        document.querySelectorAll('.room-card').forEach((card, index) => {
            if (!card.id) {
                card.id = 'room-card-' + (index + 1);
            }
        });

        $('[data-bs-toggle="tooltip"]').tooltip();
    } catch (error) {
        console.error('Error initializing room details:', error);
    }
}

if (window.jQuery) {
    $(document).ready(initRoomDetailJS);
} else {
    const waitForJQuery = setInterval(() => {
        if (window.jQuery) {
            clearInterval(waitForJQuery);
            $(document).ready(initRoomDetailJS);
        }
    }, 100);
}
</script>
