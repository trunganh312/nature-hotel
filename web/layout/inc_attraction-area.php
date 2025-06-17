<?php
// inc_attraction-area.php
?>
<section class="attraction-area py-5" style="background-color: #F7FAFC;">
    <div class="container">
        <div class="section-title text-center mb-5">
            <h2 class="fw-bold" style="color: var(--text-color);">Thành Phố Có Khách Sạn</h2>
            <div class="section-title-line"></div>
            <p class="text-muted mt-2" style="color: var(--text-light);">Khám phá các thành phố tuyệt đẹp cùng Nature Hotel</p>
        </div>
        <div class="row g-4">
            <?php foreach ($cities as $city): ?>
                <?php
                $slug = to_slug($city['cit_name']); // Tạo slug từ tên thành phố
                $city_link = "http://hotel-sennet.local/city-{$city['cit_id']}-{$slug}.html";
                $city_image = $city['cit_image']; // Ảnh thành phố
                ?>
                <div class="col-xl-3 col-lg-4 col-sm-6">
                    <div class="attraction-card bg-white rounded-3 shadow-sm overflow-hidden">
                        <a href="<?php echo $city_image; ?>" class="gallery-popup-two attraction-card-img position-relative">
                            <img src="<?php echo $city_image; ?>" alt="<?php echo $city['cit_name']; ?>" class="img-fluid w-100">
                            <div class="attraction-overlay"></div>
                            <i class="fas fa-search-plus attraction-icon position-absolute"></i>
                        </a>
                        <div class="attraction-card-content p-3 text-center">
                            <h4 class="attraction-title mb-0">
                                <a href="<?php echo $city_link; ?>" style="color: var(--text-color);"><?php echo $city['cit_name']; ?></a>
                            </h4>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>