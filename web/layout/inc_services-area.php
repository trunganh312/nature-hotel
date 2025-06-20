<section class="services-area py-5">
    <div class="container">
        <div class="section-title text-center mb-5">
            <h2 class="fw-bold" style="color: var(--text-color, #000);">Khách sạn Của Chúng Tôi</h2>
            <p class="text-muted">Khám phá các khách sạn để trải nghiệm kỳ nghỉ trọn vẹn</p>
        </div>

        <div class="box-service">
            <div class="owl-carousel owl-theme home-carousel" style='height: 375px;'>
            <!-- Hotel 1 -->
            <?php foreach ($data_hotels as $hotel): ?>
    <div class="item">
        <a href="<?= htmlspecialchars($hotel['link']) ?>" style="text-decoration: none; color: inherit;">
            <div class="card-area hotel-card">
                <div class="position-relative" style='height: 190px;'>
                    <img src="<?= htmlspecialchars($hotel['img']) ?>" class="card-img-top" alt="<?= htmlspecialchars($hotel['name']) ?>">
                </div>
                <div class="card-body p-3">
                    <h5 class="card-title"><?= htmlspecialchars($hotel['name']) ?></h5>
                    <p class="card-text text-muted"><i class="fas fa-map-marker-alt me-1"></i><?= htmlspecialchars($hotel['address']) ?></p>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="utilities-list">
                            <?php if (!empty($hotel['services'])): ?>
                                <div class="amenities d-flex">
                                    <?php foreach (array_slice($hotel['services'], 0, 3) as $service): ?>
                                        <div class="amenity me-2" style="display: flex; align-items: center;" title="<?= htmlspecialchars($service['name'] ?? 'Tiện nghi') ?>">
                                            <i class="<?= htmlspecialchars($service['icon'] ?? 'fas fa-info-circle') ?> me-1"></i>
                                            <span class="utility-name"><?= htmlspecialchars($service['name'] ?? 'Tiện nghi') ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="amenity text-muted">Đang cập nhật</div>
                            <?php endif; ?>
                        </div>                           
                        <p class="card-text price text-danger fw-bold mb-0"><?= htmlspecialchars($hotel['price']) ?>đ</p>
                    </div>
                </div>
            </div>
        </a>
    </div>
<?php endforeach; ?>
        </div>
        <div class="text-end mb-3 d-flex custom-nav" style='z-index: 100;'>
            <button class="btn btn-outline-primary me-2" id="customPrevBtn"><i class="fas fa-chevron-left"></i></button>
            <button class="btn btn-outline-primary" id="customNextBtn"><i class="fas fa-chevron-right"></i></button>
        </div>
    </div>
</section>


<style>
.services-area {
    background-color: #f9f9f9; 
    padding: 50px 0; 
}

.section-title {
    margin-bottom: 40px;
}

.section-title h2 {
    font-size: 2.3rem; 
    font-weight: 700;
    color: #2c3e50; 
    position: relative;
    display: inline-block;
    margin-bottom: 15px;
}

.section-title p {
    font-size: 1rem;
    color: #7f8c8d;
    max-width: 550px;
    margin: 0 auto;
}

.box-service {
    margin-top: 30px;
}

.card-area.hotel-card {
    max-height: 420px;
    min-height: 420px;
    border: none;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1); 
    background: #fff;
    transition: box-shadow 0.3s ease;
}

.card-area.hotel-card:hover {
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15); 
}

.card-img-top {
    width: 100%;
    height: 180px;
    object-fit: cover; 
}

.card-body {
    padding: 20px !important; 
    background: #ffffff;
    border-top: 1px solid #f1f1f1;
}

.card-title {
    font-size: 1.3rem; 
    font-weight: 600;
    color: #2c3e50; 
    margin-bottom: 10px; 
    overflow: hidden;
    text-overflow: ellipsis; 
    white-space: nowrap; 
    transition: color 0.3s ease; 
}

.card-text.text-muted i {
    font-size: 1rem; 
    color: var(--secondary-color, #4a6c4c); 
    margin-right: 8px; 
}

.card-text.price {
    font-size: 1.25rem; 
    font-weight: 700;
    color: #e74c3c;
    margin-bottom: 5px; 
}

.utilities-list {
    display: flex;
    align-items: center;
    max-width: 70%; 
    flex-wrap: wrap; 
}

.amenities {
    display: flex;
    flex-wrap: wrap;
}

.amenity {
    font-size: 0.9rem;
    color: #2c3e50;
    white-space: nowrap; 
    overflow: hidden; 
    text-overflow: ellipsis;
    max-width: 150px; 
    display: flex;
    align-items: center;
}

.amenity i {
    font-size: 1rem;
    color: var(--secondary-color, #4a6c4c);
    margin-right: 5px; 
}

.utility-name {
    font-size: 0.85rem;
    margin-right: 10px; 
}
</style>