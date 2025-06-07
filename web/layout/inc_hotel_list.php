<div class="col-md-9">
  <div id="hotels_list">
    <!-- Hotel Card 1 -->
    <?php if (!empty($hotels)): ?>
        <?php foreach ($hotels as $hotel): ?>
            <div class="hotel-card mb-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="image-container">
                            <div class="owl-carousel owl-theme" id="carousel-<?= $hotel['hot_id'] ?>">
                                <div class="item">
                                    <img src="<?= $cfg_default_image ?>" alt="<?= $hotel['hot_name'] ?>" class="hotel-image">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="hotel-content">
                            <div class="hotel-header">
                                <div class="hotel_name_title d-flex align-items-center">
                                    <h2 class="hotel-name me-2"><?= $hotel['hot_name'] ?></h2>
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
                                </div>
                                <div class="col-md-4">
                                    <div class="utilities">
                                        <h3>Tiện ích</h3>
                                        <div class="amenities">
                                            <?php if (!empty($hotel['utilities'])): ?>
                                                <?php foreach ($hotel['utilities'] as $uti): ?>
                                                    <div class="amenity mb-1"><?= htmlspecialchars($uti['name']) ?></div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <div class="amenity mb-1 text-muted">Đang cập nhật</div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="hotels_content mt-1">
                                <div class="row">
                                    <div class="hotel-description">
                                        <?= $hotel['hot_description'] ?>
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
  <!-- Pagination -->
  <!-- <div class="pagination d-flex justify-content-center mt-4">
    <a href="#" class="pagination-nav me-2"><i class="fas fa-angle-double-left"></i></a>
    <a href="#" class="pagination-nav me-2"><i class="fas fa-angle-left"></i></a>
    <a href="#" class="active mx-1">1</a>
    <a href="#" class="mx-1">2</a>
    <a href="#" class="mx-1">3</a>
    <a href="#" class="pagination-nav ms-2"><i class="fas fa-angle-right"></i></a>
    <a href="#" class="pagination-nav ms-2"><i class="fas fa-angle-double-right"></i></a>
  </div> -->
</div>


<style>
  /* Hotel Card */
  .hotel-card {
    background: var(--white-color);
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 16px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
  }

  .image-container {
    position: relative;
    width: 100%;
    height: 250px;
    overflow: hidden;
    border-radius: 8px;
  }

  .owl-carousel {
    height: 100%;
  }

  .owl-carousel .item {
    height: 250px;
  }

  .owl-carousel .item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .owl-nav {
    opacity: 0;
    transition: opacity 0.3s ease;
  }

  .owl-nav button.owl-prev,
  .owl-nav button.owl-next {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 30px !important;
    height: 30px !important;
    background: rgba(255, 255, 255, 0.8) !important;
    border-radius: 50% !important;
    display: flex !important;
    align-items: center;
    justify-content: center;
  }

  .owl-nav button.owl-prev i,
  .owl-nav button.owl-next i {
    color: #000 !important;
    font-size: 14px !important;
  }

  .owl-nav button.owl-prev:hover i,
  .owl-nav button.owl-next:hover i {
    color: var(--secondary-color) !important;
  }

  .owl-nav button.owl-prev {
    left: 10px;
  }

  .owl-nav button.owl-next {
    right: 10px;
  }

  .carousel-control-next-icon {
    display: none;
  }

  .owl-dots {
    position: absolute;
    bottom: 10px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1;
  }

  .owl-dots .owl-dot span {
    width: 8px !important;
    height: 8px !important;
    margin: 4px !important;
    background: rgba(255, 255, 255, 0.5) !important;
  }

  .owl-dots .owl-dot.active span {
    background: #fff !important;
  }

  .hotel-card:hover {
    cursor: pointer;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  }

  .hotel-card:hover .owl-nav {
    opacity: 1;
  }

  .hotel-card:hover .owl-dots {
    opacity: 1;
  }

  .hotel-card:hover .hotel-name {
    color: var(--secondary-color);
  }

  .image-dots {
    display: none;
  }

  .owl-prev {
    left: 10px;
  }

  .owl-next {
    right: 10px;
  }

  .owl-dot span {
    background: rgba(255, 255, 255, 0.7) !important;
  }

  .owl-dot.active span {
    background: #fff !important;
  }

  .hotel-header {
    align-items: flex-start;
  }

  .hotel-name {
    font-size: 22px;
    font-weight: 600;
    color: var(--text-color);
    margin: 0;
  }

  .review-count {
    color: var(--text-lighter);
    font-size: 16px;
  }

  .hotel-location,
  .hotel-phone,
  .hotel-mail {
    display: flex;
    align-items: center;
    gap: 8px;
    color: var(--text-lighter);
  }

  .map-link {
    color: var(--primary-color);
    text-decoration: none;
  }

  .hotels_content {
    display: flex;
    flex-direction: column;
    width: 100%;
    white-space: wrap;
    background: linear-gradient(90deg, #F7FAFC 0%, #FFFFFF 100%);
    padding: 4px 24px;
  }

  .utilities h3 {
    white-space: wrap;
    font-size: 16px;
    font-weight: 500;
    margin-bottom: 8px;
  }

  .amenities {
    white-space: wrap;
    gap: 16px;
    margin-bottom: 8px;
  }

  .amenity {
    color: #28a745;
    font-size: 16px;
  }

  .booking-status {
    color: var(--text-lighter);
    font-size: 16px;
  }

  .discount_tag {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .price-section {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 8px;
  }

  .promo-tag {
    background: var(--secondary-color);
    color: var(--primary-color);
    padding: 6px 8px;
    border-radius: 4px;
    font-size: 14px;
  }
  .hotel-description {
    display: -webkit-box;
    -webkit-line-clamp: 5;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
  }
</style>