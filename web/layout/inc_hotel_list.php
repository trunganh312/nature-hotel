 <?php
// // Tạo mảng với 10 ảnh từ link được cung cấp
// $list_images = [
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

// // Gán mảng ảnh giả lập cho từng khách sạn trong danh sách
// if (isset($hotels) && is_array($hotels)) {
//     foreach ($hotels as $key => $hotel) {
//         if (!isset($hotels[$key]['images']) || empty($hotels[$key]['images'])) {
//             $hotels[$key]['images'] = array_slice($list_images, 0, 5);  // Lấy 5 ảnh đầu tiên cho mỗi khách sạn
//         }
//     }
// }
?>

<div class="col-md-9">
    <div id="hotels_list">
        <!-- Hotel Card 1 -->
        <?php if (!empty($hotels)): ?>
            <?php foreach ($hotels as $hotel): ?>
                <div class="hotel-card mb-4">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="image-container">
                                <div class="owl-carousel owl-theme hotel-list-carousel list-carousel" id="carousel-<?= $hotel['hot_id'] ?>">
                                    <!-- Lặp ảnh, nếu không có thì để ảnh mặc định -->
                                    <?php if (!empty($hotel['images'])): ?>
                                        <?php foreach ($hotel['images'] as $image): ?>
                                            <div class="item">
                                                <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($hotel['hot_name']) ?>" class="hotel-image">
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="item">
                                            <img src="<?= htmlspecialchars($cfg_default_image) ?>" alt="<?= htmlspecialchars($hotel['hot_name']) ?>" class="hotel-image">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="hotel-content">
                                <div class="hotel-header">
                                    <div class="hotel_name_title d-flex align-items-center">
                                        <a href="<?= $hotel['link'] ?>" style='text-decoration: none;'>
                                            <h2 class="hotel-name me-2"><?= $hotel['hot_name'] ?></h2>
                                        </a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="hotel-phone mt-2">
                                            <i class="fas fa-phone-alt me-1"></i>
                                            <span><?= $hotel['hot_phone'] ?></span>
                                        </div>
                                        <div class="hotel-mail mt-2">
                                            <i class="fas fa-envelope me-1"></i>
                                            <span><?= $hotel['hot_email'] ?></span>
                                        </div>
                                        <div class="hotel-address mt-2">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            <span><?= $hotel['hot_address_full'] ?></span>
                                        </div>

                                    </div>
                                </div>
                                <div class="hotels_content ">
                                    <div class="row align-items-start">
                                        <div class="col-md-4">
                                            <div class="utilities">
                                                <div class="amenities">
                                                    <?php if (!empty($hotel['utilities'])): ?>
                                                        <?php foreach ($hotel['utilities'] as $uti): ?>
                                                            <div class="amenity mb-1" style='display: flex;'>
                                                                <div><i class="<?= htmlspecialchars($uti['icon']) ?>"></i></div>
                                                                <div style='margin-left: 5px;'><?= htmlspecialchars($uti['name']) ?></div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <div class="amenity mb-1 text-muted">Đang cập nhật</div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="price-section">
                                            <div class="price-container">
                                                <span class="price-value"><?= number_format(100000, 0, ',', '.') ?> đ</span>
                                            </div>
                                            <div class="button-detail">
                                                <a href="#" class="btn-detail">Xem chi tiết</a>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div>Không có khách sạn nào phù hợp.</div>
        <?php endif; ?>
    </div>
</div>