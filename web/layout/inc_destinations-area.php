<?php
// destinations-area.php
?>
<section class="destinations-area py-5" style="background-color: #F7FAFC;">
    <div class="container">
        <div class="section-title text-center mb-5">
            <h2 class="fw-bold" style="color: var(--text-color);">Chọn Điểm Đến</h2>
            <p class="text-muted" style="color: var(--text-light);">Khám phá các địa điểm tuyệt vời cùng Nature Hotel</p>
        </div>
        <div class="row gy-4">
            <?php foreach ($data_city as $city): 
                    $name = $city['name'];
                    $count = $city['value'];
                    $img = isset($city_images[$name]) ? $city_images[$name]['img'] : $cfg_default_image;
                    $link = $city['link'];
                ?>
                <div class="col-xl-4 col-lg-4 col-md-6">
                    <div class="destination-card bg-white rounded-3 shadow-sm overflow-hidden">
                        <a href="<?= $link ?>" class="destination-card-img position-relative">
                            <img src="<?= $img ?>" alt="<?= $name ?>" class="img-fluid w-100">
                            <div class="destination-overlay"></div>
                        </a>
                        <div class="destination-card-content p-3">
                            <h4 class="destination-title mb-1">
                                <a href="<?= $link ?>" style="color: var(--text-color);"><?= $name ?></a>
                            </h4>
                            <p class="text-muted mb-0" style="color: var(--text-light);"><?= $count ?> Khách sạn</p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>