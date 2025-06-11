<?php
// // Tạo mảng với 10 ảnh từ link được cung cấp
// $overview_images = [
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

// // Gán 5 ảnh đầu tiên cho các biến riêng lẻ
// $image_1 = $overview_images[0];
// $image_2 = $overview_images[1];
// $image_3 = $overview_images[2];
// $image_4 = $overview_images[3];
// $image_5 = $overview_images[4];
// $images = array_slice($overview_images, 5);
?>

<section id="hotel-overview" class="container py-5">
    <!-- Tiêu đề khách sạn -->
    <div class="row ">
        <div class="col-12">
            <h1 class="display-5 fw-bold text-dark"><?= $hotel['hot_name'] ?></h1>
        </div>
    </div>

    <!-- Thông tin đánh giá và địa chỉ -->
    <div class="row mb-4">
        <div class="col-12 col-md-6 d-flex align-items-center">
            <i class="far fa-map-marker-alt me-2 text-primary"></i>
            <span><?= $hotel['hot_address_full'] ?></span>
            <a href="#map-section" class="btn btn-outline ms-3">Xem bản đồ</a>
        </div>
    </div>

    <!-- Slideshow hình ảnh và video -->
    <div class="row position-relative">
        <div class="col-12 col-lg-6 pe-0">
            <div class="main-image-container">
                <img src="<?= $image_1 ?>" class="img-fluid rounded w-100 h-100" alt="Hotel Image 1">
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="row hotel-gallery">
                <div class="col-6 mb-2">
                    <img src="<?= $image_2 ?>" class="img-fluid rounded" alt="Hotel Image 2">
                </div>
                <div class="col-6 mb-2">
                    <img src="<?= $image_3 ?>" class="img-fluid rounded" alt="Hotel Image 3">
                </div>
                <div class="col-6 mb-2">
                    <img src="<?= $image_4 ?>" class="img-fluid rounded" alt="Hotel Image 4">
                </div>
                <div class="col-6 mb-2 position-relative">
                    <img src="<?= $image_5 ?>" class="img-fluid rounded" alt="Hotel Image 5">
                    <?php
                    if (count($images) > 0): ?>
                        <div class="gallery-overlay">
                            <div class="overlay-content">
                                <span><?= count($images) ?></span>
                                <i class="far fa-images ms-2"></i>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Modal HTML -->
<div class="modal fade" id="hotelGalleryModal" tabindex="-1" aria-labelledby="hotelGalleryModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="hotelGalleryModalLabel"><?= $hotel['hot_name'] ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Thêm carousel chính -->
                <div id="galleryCarouselMain" class="owl-carousel owl-theme overview-carousel gallery-main">
                    <!-- Render tử images -->
                    <?php
                    foreach (array_merge([$image_1, $image_2, $image_3, $image_4, $image_5], $images) as $image): ?>
                        <div class="item">
                            <img src="<?= $image ?>" class="img-fluid rounded" alt="Hotel Image">
                        </div>
                    <?php endforeach; ?>
                </div>
                <!-- Thêm thumbnail carousel -->
                <div id="galleryCarouselThumbs"
                    class="gallery-thumbnails-container owl-carousel owl-theme gallery-thumbs">
                    <!-- Render từ images -->
                    <?php
                    foreach (array_merge([$image_1, $image_2, $image_3, $image_4, $image_5], $images) as $image): ?>
                        <div class="item">
                            <img src="<?= $image ?>" class="img-fluid rounded" alt="Hotel Image">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .main-image-container {
        height: 353px;
        width: 100%;
        overflow: hidden;
    }
</style>