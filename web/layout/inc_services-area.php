
<section class="services-area py-5">
    <div class="container">
        <div class="section-title text-center mb-5">
            <h2 class="fw-bold" style="color: var(--text-color, #000);">Khách sạn Của Chúng Tôi</h2>
            <p class="text-muted">Khám phá các khách sạn để trải nghiệm kỳ nghỉ trọn vẹn</p>
        </div>

        <div class="box-service">
            <div class="owl-carousel owl-theme home-carousel">
            <!-- Hotel 1 -->
            <?php foreach ($data_hotels as $hotel): ?>
                <div class="item">
                <div class="card-area hotel-card">
                    <div class="position-relative">
                        <img src="<?= htmlspecialchars($hotel['img']) ?>" class="card-img-top" alt="<?= htmlspecialchars($hotel['name']) ?>">
                        
                    </div>
                    <div class="card-body p-3">
                        <h5 class="card-title"><?= htmlspecialchars($hotel['name']) ?></h5>
                        <p class="card-text text-muted"><i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars($hotel['address']) ?></p>
                        <div class="rating mb-2">
                        </div>
                        <p class="card-text price text-danger fw-bold"><?= htmlspecialchars($hotel['price']) ?>đ</p>
                        <p class="card-text text-muted small text-decoration-line-through"><?= htmlspecialchars($hotel['price']) ?>đ</p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>   
        </div>
        <div class="text-end mb-3 d-flex custom-nav">
            <button class="btn btn-outline-primary me-2" id="customPrevBtn"><i class="fas fa-chevron-left"></i></button>
            <button class="btn btn-outline-primary" id="customNextBtn"><i class="fas fa-chevron-right"></i></button>
        </div>
        </div>
    </div>
</section>

