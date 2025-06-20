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
    <div class="row">
        <div class="col-12">
            <h1 class="display-5 fw-bold text-dark"><?= $hotel_detail['hot_name'] ?></h1>
        </div>
    </div>
    <div class="overview-icon-start"><svg width="14" height="14" fill="none" class="svgFillAll jss93"
            style="stroke:#FFBC39;fill:#FFBC39">
            <path
                d="M12.464 5.684a1.055 1.055 0 00-.849-.719L9.078 4.6 7.94 2.31a1.05 1.05 0 00-1.88 0L4.925 4.598l-2.536.367a1.057 1.057 0 00-.87 1.292c.047.191.148.365.29.502L3.64 8.534l-.433 2.51a1.05 1.05 0 001.521 1.107l2.272-1.188 2.273 1.19a1.05 1.05 0 001.522-1.108l-.435-2.51 1.832-1.776a1.05 1.05 0 00.271-1.075z"
                fill="#FFBC39"></path>
        </svg><svg width="14" height="14" fill="none" class="svgFillAll jss93" style="stroke:#FFBC39;fill:#FFBC39">
            <path
                d="M12.464 5.684a1.055 1.055 0 00-.849-.719L9.078 4.6 7.94 2.31a1.05 1.05 0 00-1.88 0L4.925 4.598l-2.536.367a1.057 1.057 0 00-.87 1.292c.047.191.148.365.29.502L3.64 8.534l-.433 2.51a1.05 1.05 0 001.521 1.107l2.272-1.188 2.273 1.19a1.05 1.05 0 001.522-1.108l-.435-2.51 1.832-1.776a1.05 1.05 0 00.271-1.075z"
                fill="#FFBC39"></path>
        </svg><svg width="14" height="14" fill="none" class="svgFillAll jss93" style="stroke:#FFBC39;fill:#FFBC39">
            <path
                d="M12.464 5.684a1.055 1.055 0 00-.849-.719L9.078 4.6 7.94 2.31a1.05 1.05 0 00-1.88 0L4.925 4.598l-2.536.367a1.057 1.057 0 00-.87 1.292c.047.191.148.365.29.502L3.64 8.534l-.433 2.51a1.05 1.05 0 001.521 1.107l2.272-1.188 2.273 1.19a1.05 1.05 0 001.522-1.108l-.435-2.51 1.832-1.776a1.05 1.05 0 00.271-1.075z"
                fill="#FFBC39"></path>
        </svg><svg width="14" height="14" fill="none" class="svgFillAll jss93" style="stroke:#FFBC39;fill:#FFBC39">
            <path
                d="M12.464 5.684a1.055 1.055 0 00-.849-.719L9.078 4.6 7.94 2.31a1.05 1.05 0 00-1.88 0L4.925 4.598l-2.536.367a1.057 1.057 0 00-.87 1.292c.047.191.148.365.29.502L3.64 8.534l-.433 2.51a1.05 1.05 0 001.521 1.107l2.272-1.188 2.273 1.19a1.05 1.05 0 001.522-1.108l-.435-2.51 1.832-1.776a1.05 1.05 0 00.271-1.075z"
                fill="#FFBC39"></path>
        </svg><svg width="14" height="14" fill="none" class="svgFillAll jss93" style="stroke:#FFBC39;fill:#FFBC39">
            <path
                d="M12.464 5.684a1.055 1.055 0 00-.849-.719L9.078 4.6 7.94 2.31a1.05 1.05 0 00-1.88 0L4.925 4.598l-2.536.367a1.057 1.057 0 00-.87 1.292c.047.191.148.365.29.502L3.64 8.534l-.433 2.51a1.05 1.05 0 001.521 1.107l2.272-1.188 2.273 1.19a1.05 1.05 0 001.522-1.108l-.435-2.51 1.832-1.776a1.05 1.05 0 00.271-1.075z"
                fill="#FFBC39"></path>
        </svg></div>
    <!-- Thông tin đánh giá và địa chỉ -->
    <div class="row mb-4">
        <div class="col-12 d-flex align-items-center flex-wrap">
            <i class="far fa-map-marker-alt me-2 text-primary"></i>
            <span class="address"><?= $hotel_detail['hot_address_full'] ?></span>
            <a href="#map-section" class="ms-3 map-icon" aria-label="Xem bản đồ">
                <svg width="24" height="24" fill="none" class="svgFillAll jss61">
                    <path d="M3 7l6-3 6 3 6-3v13l-6 3-6-3-6 3V7zM9 4v13M15 7v13" stroke="#00B6F3" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </a>
        </div>
    </div>

    <!-- Slideshow hình ảnh -->
    <div class="row position-relative hotel-gallery">
        <!-- Bố cục cho PC -->
        <div class="col-12 col-lg-6 pe-lg-0 d-none d-lg-block">
            <div class="main-image-container">
                <img src="<?= $image_1 ?>" class="img-fluid rounded w-100 h-100" alt="Hotel Image 1"
                    data-bs-toggle="modal" data-bs-target="#hotelGalleryModal">
            </div>
        </div>
        <div class="col-12 col-lg-6 d-none d-lg-block">
            <div class="row pc-gallery">
                <div class="col-6 mb-2">
                    <img src="<?= $image_2 ?>" class="img-fluid rounded" alt="Hotel Image 2" data-bs-toggle="modal"
                        data-bs-target="#hotelGalleryModal">
                </div>
                <div class="col-6 mb-2">
                    <img src="<?= $image_3 ?>" class="img-fluid rounded" alt="Hotel Image 3" data-bs-toggle="modal"
                        data-bs-target="#hotelGalleryModal">
                </div>
                <div class="col-6 mb-2">
                    <img src="<?= $image_4 ?>" class="img-fluid rounded" alt="Hotel Image 4" data-bs-toggle="modal"
                        data-bs-target="#hotelGalleryModal">
                </div>
                <div class="col-6 mb-2 position-relative">
                    <img src="<?= $image_5 ?>" class="img-fluid rounded" alt="Hotel Image 5" data-bs-toggle="modal"
                        data-bs-target="#hotelGalleryModal">
                    <?php if (count($images) > 0): ?>
                        <div class="gallery-overlay" data-bs-toggle="modal" data-bs-target="#hotelGalleryModal">
                            <div class="overlay-content">
                                <span>+<?= count($images) ?></span>
                                <i class="far fa-images ms-2"></i>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Bố cục cho mobile -->
        <div class="col-12 d-lg-none">
            <div class="main-image-container mb-1">
                <img src="<?= $image_1 ?>" class="img-fluid rounded w-100" alt="Hotel Image 1" data-bs-toggle="modal"
                    data-bs-target="#hotelGalleryModal">
            </div>
            <div class="thumbnail-gallery d-flex justify-content-between">
                <div class="thumbnail-item position-relative">
                    <img src="<?= $image_2 ?>" class="img-fluid rounded" alt="Hotel Image 2" data-bs-toggle="modal"
                        data-bs-target="#hotelGalleryModal">
                </div>
                <div class="thumbnail-item position-relative">
                    <img src="<?= $image_3 ?>" class="img-fluid rounded" alt="Hotel Image 3" data-bs-toggle="modal"
                        data-bs-target="#hotelGalleryModal">
                </div>
                <div class="thumbnail-item position-relative">
                    <img src="<?= $image_4 ?>" class="img-fluid rounded" alt="Hotel Image 4" data-bs-toggle="modal"
                        data-bs-target="#hotelGalleryModal">
                </div>
                <div class="thumbnail-item position-relative">
                    <img src="<?= $image_5 ?>" class="img-fluid rounded" alt="Hotel Image 5" data-bs-toggle="modal"
                        data-bs-target="#hotelGalleryModal">
                    <?php if (count($images) > 0): ?>
                        <div class="gallery-overlay" data-bs-toggle="modal" data-bs-target="#hotelGalleryModal">
                            <div class="overlay-content">
                                <span>+<?= count($images) ?></span>
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
                <h5 class="modal-title" id="hotelGalleryModalLabel"><?= $hotel_detail['hot_name'] ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Thêm carousel chính -->
                <div id="galleryCarouselMain" class="owl-carousel owl-theme overview-carousel gallery-main">
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