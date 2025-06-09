<? if(empty($rooms)) return; ?>
<? foreach($rooms as $idx => $room): ?>
<div class="room-card mb-4">
    <? if($idx == 0): ?>
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
    <? endif; ?>
    <div class="room-card-content d-flex col-md-12">
        <div class="row">
            <div class="room-images-section col-md-3">
                <div class="image-gallery">
                    <div class="main-image">
                        <img src="<?= $room['images'][0]?>"
                            alt="<?= $room['roo_name'] ?>">
                    </div>
                    <div class="thumbnail-images">
                        <img src="<?= $room['images'][1]?>"
                            alt="<?= $room['roo_name'] ?>">
                        <img src="<?= $room['images'][2]?>"
                            alt="<?= $room['roo_name'] ?>">
                        <img src="<?= $room['images'][3]?>"
                            alt="<?= $room['roo_name'] ?>">
                    </div>
                </div>
                <button class="view-details-button">
                    Xem chi tiết phòng
                    <svg width="16" height="16" fill="none">
                        <path d="M6 12l4-4-4-4" stroke="#6b9c6e" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round"></path>
                    </svg>
                </button>
                <div class="amenities-list">
                    <!-- Hiển thị 5 cái tiện ích đầu tiên -->
                    <? foreach($room['utilities'] as $idx => $utility): ?>
                    <? if($idx < 6): ?>
                    <span class="amenity-item"><?= $utility['name'] ?></span>
                    <? endif; ?>
                    <? endforeach; ?>
                    <span class="amenity-count"><?= count($room['utilities']) ?> tiện ích</span>
                </div>
            </div>
            <div class="room-details-section col-md-8">
                <div class="row">
                    <h3 class="room-title"><?= $room['roo_name'] ?></h3>
                    <div class=" room_details_room-info pb-3">
                        <div class="info-item">
                            <svg width="16" height="16" fill="none">
                                <path
                                    d="M2 14v-1.333A2.667 2.667 0 014.667 10h2.666A2.667 2.667 0 0110 12.667V14m.667-11.913a2.667 2.667 0 010 5.166M14 14v-1.333a2.667 2.667 0 00-2-2.567M8.667 4.667a2.667 2.667 0 11-5.334 0 2.667 2.667 0 015.334 0z"
                                    stroke="#4A5568" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                            <span><?= $room['roo_max_adult'] ?> người</span>
                            <span class="detail-link">(Xem chi tiết)</span>
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
                            <span><?= $room['roo_square_meters'] ?> m²</span>
                        </div>
                        <div class="info-item">
                            <svg width="16" height="16" fill="none">
                                <path d="M8 9.333a1.333 1.333 0 100-2.666 1.333 1.333 0 000 2.666z" stroke="#4A5568"
                                    stroke-linecap="round" stroke-linejoin="round"></path>
                                <path
                                    d="M14.667 8c-1.778 3.111-4 4.667-6.667 4.667S3.11 11.11 1.333 8c1.778-3.111 4-4.667 6.667-4.667S12.889 4.89 14.667 8z"
                                    stroke="#4A5568" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                            <span><?= $room['attr_view'] ?></span>
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
                                <? if($room['roo_extra_bed'] == 1): ?>
                                    <span class="tag">
                                    <svg width="16" height="16" fill="none">
                                        <path d="M12.739 6.478L6.652 15l1.217-5.478H3L9.087 1 7.87 6.478h4.87z"
                                            stroke="#ED8936" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                    Có thể kê thêm giường phụ
                                </span>
                                <? endif; ?>
                            </div>
                            <!-- <div class="offer-info">
                                <span class="offer-title">Ưu đãi bao gồm</span>
                                <div class="offer-item">
                                    <svg width="16" height="16" fill="none">
                                        <path d="M3.333 8l3.333 3.333 6.667-6.666" stroke="#48BB78"
                                            stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                    <span class="info-text">Ăn sáng</span>
                                </div>
                                <div class="additional-info">
                                    <span class="info-title">Thông tin bổ sung</span>
                                    <span class="info-text">Đặt phòng không đổi tên khách</span>
                                </div>
                            </div> -->
                        </div>
                    </div>
                    <div class="col-md-3 room_detail_boder_bed">
                        <div class="bed-info">
                            <svg width="24" height="24" fill="none">
                                <path
                                    d="M22.5 11.75h-21a1.5 1.5 0 00-1.5 1.5v4a1.5 1.5 0 001.125 1.45.5.5 0 01.375.483v1.067a1 1 0 102 0v-1a.5.5 0 01.5-.5h16a.5.5 0 01.5.5v1a1 1 0 002 0v-1.064a.5.5 0 01.375-.483A1.5 1.5 0 0024 17.25v-4a1.5 1.5 0 00-1.5-1.5zM2.5 10.25a.5.5 0 00.5.5h18a.5.5 0 00.5-.5v-5a2.5 2.5 0 00-2.5-2.5H5a2.5 2.5 0 00-2.5 2.5v5zm4-3h2a2.5 2.5 0 012.166 1.25.5.5 0 01-.433.75H4.767a.5.5 0 01-.433-.75A2.5 2.5 0 016.5 7.25zm9 0h2a2.5 2.5 0 012.166 1.25.5.5 0 01-.433.75h-5.466a.5.5 0 01-.433-.75A2.5 2.5 0 0115.5 7.25z"
                                    fill="#718096"></path>
                            </svg>
                            <div class="bed-text"><?= $room['attr_bed'] ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="pricing">
                            <div class="final-price">1.200.000 VND</div>

                            <div class="room-quantity">
                                <div class="quantity-label">Số phòng:</div>
                                <div class="quantity-control">
                                    <button class="quantity-decrease"><i class="fa-solid fa-minus"></i></button>
                                    <input type="number" id="room-quantity" name="room_quantity" class="quantity-input"
                                        value="0" min="0" max="100">
                                    <button class="quantity-increase"><i class="fa-solid fa-plus"></i></button>
                                </div>
                            </div>

                            <div class="guest-selection-container">
                                <!-- Sẽ được thêm bằng JS -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<? endforeach; ?>
<!-- Điểm dừng của sticky booking info -->
<div id="end-sticky-point" style="height: 0; overflow: hidden;" class="pt-4"></div>

<style>
    .room-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        border: 1px solid var(--accent-color);
    }

    .room-card-header {
        padding: 10px 20px;
        background: var(--accent-color-hover);
    }

    .recommended-badge {
        display: inline-flex;
        align-items: center;
        background: #fff;
        border: 1px solid var(--accent-color);
        border-radius: 4px;
        padding: 4px 8px;
        font-size: 12px;
        color: var(--accent-color);
        font-weight: 500;
    }

    .recommended-badge .star-icon {
        margin-right: 4px;
    }

    .room-card-content {
        display: flex;
        padding: 20px;
        gap: 20px;
    }

    .room-images-section {
        position: sticky;
        top: 170px;
    }

    .image-gallery {
        display: grid;
        grid-template-areas:
            "main main main"
            "thumb1 thumb2 thumb3";
        gap: 2px;
    }

    .main-image {
        grid-area: main;
        width: 100%;
        height: 140px;
    }

    .main-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 8px;
    }

    .thumbnail-images {
        display: flex;
        gap: 2px;
        width: 70px;
        height: 53px;
    }

    .thumbnail-images img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 8px;
    }

    .view-details-button {
        display: flex;
        align-items: center;
        background: none;
        border: none;
        color: var(--accent-color);
        font-size: 14px;
        font-weight: 500;
        padding: 6px 0;
        cursor: pointer;
    }

    .view-details-button svg {
        margin-left: 4px;
    }

    .amenities-list {
        display: flex;
        flex-wrap: wrap;
        gap: 2px;
    }

    .amenity-item {
        background: #f1f5f9;
        border-radius: 20px;
        margin: 0 2px;
        padding: 4px 8px;
        font-size: 12px;
        color: var(--text-light);
    }

    .amenity-count {
        font-size: 12px;
        color: var(--accent-color);
    }

    .room-details-section {
        flex: 1;
    }

    .room-title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .room_details_room-info {
        border-bottom: 1px solid var(--boder_bottom);
        display: flex !important;
        flex-direction: row;
        gap: 8px;
    }

    .info-item {
        display: flex;
        align-items: center;
        font-size: 14px;
        color: var(--text-light);
    }

    .info-item svg {
        margin-right: 6px;
    }

    .detail-link {
        color: var(--accent-color);
        margin-left: 4px;
        cursor: pointer;
    }

    .room-options {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .room-option {
        border-bottom: 1px solid #edf2f7;
        padding-bottom: 20px;
    }

    .option-header {
        margin-bottom: 10px;
    }

    .option-title {
        font-size: 16px;
        font-weight: 600;
        display: block;
        margin-bottom: 8px;
    }

    .option-tags {
        gap: 12px;
    }

    .tag {
        display: flex;
        align-items: center;
        font-size: 14px;
        color: var(--text-light);
    }

    .tag svg {
        margin-right: 4px;
    }

    .option-details {
        display: flex;
        justify-content: space-between;
        gap: 20px;
    }

    .bed-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .bed-info span {
        display: flex;
        align-items: center;
        font-size: 14px;
        color: var(--text-light);
    }

    .bed-info svg {
        margin-right: 8px;
    }

    .pricing {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .final-price {
        font-size: 16px;
        font-weight: 600;
        color: var(--primary-color);
        text-align: right;
    }

    .room-quantity {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .quantity-control {
        display: flex;
        align-items: center;
    }

    .quantity-control button {
        width: 30px;
        height: 30px;
        background: var(--accent-color);
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .quantity-control button i {
        font-size: 12px;
    }

    .quantity-input {
        width: 50px;
        text-align: center;
        margin: 0 5px;
        padding: 5px;
        border: 1px solid var(--boder_bottom);
        border-radius: 4px;
    }

    .guest-selection-container {
        border-top: 1px solid var(--boder_bottom);
    }

    .discount {
        background: #ffedd5;
        color: #ed8936;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 600;
    }

    .price-details {
        text-align: right;
    }

    .original-price {
        text-decoration: line-through;
        color: #718096;
        font-size: 14px;
    }

    .discounted-price {
        color: #2d3748;
        font-size: 16px;
        font-weight: 600;
    }

    .coupon {
        background: #e6f7ff;
        padding: 8px 12px;
        border-radius: 4px;
        font-size: 14px;
    }

    .coupon strong {
        color: #00b6f3;
    }

    .book-now-button {
        background: #00b6f3;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
    }

    .price-breakdown {
        margin-top: 10px;
        text-align: right;
    }

    .price-breakdown>span {
        font-size: 16px;
        font-weight: 600;
        color: #2d3748;
    }

    .breakdown-details {
        margin-top: 8px;
        font-size: 12px;
        color: #4a5568;
    }

    .room_detail_boder_bed {
        border-left: 1px solid var(--boder_bottom);
        border-right: 1px solid var(--boder_bottom);
    }

    .breakdown-details div {
        display: flex;
        justify-content: space-between;
        margin-bottom: 4px;
    }

    .quantity-label {
        font-size: 14px;
        color: var(--text-color);
    }

    .view-more-button {
        display: flex;
        align-items: center;
        background: none;
        border: none;
        color: #00b6f3;
        font-size: 14px;
        font-weight: 500;
        padding: 10px 0;
        cursor: pointer;
        margin-top: 20px;
    }

    .view-more-button svg {
        margin-left: 4px;
    }

    .hidden {
        display: none;
    }

    .offer-info {
        margin-top: 10px;
        background-color: #F7FAFC;
        padding: 10px;
        border-left: 2px solid var(--accent-color);
        border-radius: 0 10px 10px 0;
    }

    .offer-title {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .offer-item {
        display: flex;
        align-items: center;
        font-size: 14px;
        color: #4a5568;
    }

    .offer-item svg {
        margin-right: 8px;
    }

    .info-title {
        font-size: 14px;
        font-weight: 600;
        display: block;
        width: 100%;
    }

    .info-text {
        font-size: 14px;
        color: #4a5568;
    }

    .guest-label {
        font-size: 12px;
        font-weight: 600;
    }

    .guest-title {
        font-size: 14px;
        font-weight: 600;
        margin: 6px 0;
    }

    .guest-input {
        display: flex;
        align-items: center;
        gap: 2px;
        cursor: pointer;
    }

    .guest-input .guest-label label {
        cursor: pointer;
    }

    .guest-input input {
        width: 20px;
        border: none;
        background: transparent;
        font-size: 12px;
    }

    .guest-indicator {
        position: absolute;
        bottom: -2px;
        left: 0;
        height: 1px;
        width: 0;
        background-color: var(--accent-color);
        transition: all 0.3s ease;
        opacity: 0;
    }

    /* Thêm position relative cho container của indicator */
    .guest-selection {
        position: relative;
    }

    .guest-input {
        position: relative;
    }

    input:focus {
        outline: none;
    }

    /* Ẩn mũi tên tăng giảm mặc định */
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type="number"] {
        -moz-appearance: textfield;
    }

    @media (max-width: 768px) {
        .room-card-content {
            flex-direction: column;
        }

        .room-images-section {
            position: static;
        }
    }
</style>


<script>
    function initRoomDetailJS() {
        try {
            // Xử lý tăng/giảm số lượng phòng
            const quantityControls = document.querySelectorAll('.quantity-control');

            quantityControls.forEach(control => {
                const decreaseBtn = control.querySelector('.quantity-decrease');
                const increaseBtn = control.querySelector('.quantity-increase');
                const input = control.querySelector('.quantity-input');
                const container = control.closest('.pricing').querySelector('.guest-selection-container');

                decreaseBtn.addEventListener('click', () => {
                    if (parseInt(input.value) > 0) {
                        input.value = parseInt(input.value) - 1;
                        updateGuestSelection(input, container);
                    }
                });

                increaseBtn.addEventListener('click', () => {
                    if (parseInt(input.value) < 10) {
                        input.value = parseInt(input.value) + 1;
                        updateGuestSelection(input, container);
                    }
                });

                input.addEventListener('change', () => updateGuestSelection(input, container));

                // Khởi tạo ban đầu cho mỗi phòng
                updateGuestSelection(input, container);
            });

            // Hàm cập nhật form chọn khách
            function updateGuestSelection(input, container) {
                const roomCount = parseInt(input.value);

                let html = '';

                for (let i = 1; i <= roomCount; i++) {
                    // Tạo ID duy nhất dựa trên container để tránh trùng lặp
                    const uniqueId = container.closest('.room-card').id || Math.random().toString(36).substring(2, 9);
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

                // Xử lý indicator cho input
                setupGuestIndicator();
            }

            // Xử lý indicator cho các input
            function setupGuestIndicator() {
                const inputFields = document.querySelectorAll('.guest-input-field');

                inputFields.forEach((input) => {
                    // Lấy indicator trong cùng guest-input với input
                    const guestInput = input.closest('.guest-input');
                    const indicator = guestInput.querySelector('.guest-indicator');

                    // Xử lý sự kiện focus
                    input.addEventListener('focus', () => {
                        // Reset tất cả indicator về trạng thái ban đầu
                        document.querySelectorAll('.guest-indicator').forEach(ind => {
                            ind.style.width = '0';
                            ind.style.opacity = '0';
                        });

                        // Hiển thị indicator cho input hiện tại
                        indicator.style.width = '100%';
                        indicator.style.opacity = '1';
                    });
                });

                // Ẩn indicator khi click ra bên ngoài
                document.addEventListener('click', (e) => {
                    if (!e.target.classList.contains('guest-input-field') &&
                        !e.target.closest('.guest-label')) {
                        document.querySelectorAll('.guest-indicator').forEach(ind => {
                            ind.style.width = '0';
                            ind.style.opacity = '0';
                        });
                    }
                });
            }

            // Khởi tạo các ID cho room-cards
            document.querySelectorAll('.room-card').forEach((card, index) => {
                // Thêm ID nếu chưa có
                if (!card.id) {
                    card.id = 'room-card-' + (index + 1);
                }
            });
        } catch (error) {
            console.error('Error in room_detail.js:', error);
        }
    }

    // Kiểm tra jQuery đã load chưa
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