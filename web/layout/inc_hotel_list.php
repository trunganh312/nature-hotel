<div class="col-md-9">
  <div id="hotels_list">
    <!-- Hotel Card 1 -->
    <?php if (!empty($hotels)): ?>
        <?php foreach ($hotels as $hotel): ?>
            <div class="hotel-card mb-4">
                <div class="row">
                    <div class="col-md-4">
                        <a href="<?= $hotel['link'] ?>">
                          <div class="image-container">
                            <div class="owl-carousel owl-theme list-carousel" id="carousel-<?= $hotel['hot_id'] ?>">
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
                        </a>
                    </div>
                    <div class="col-md-8">
                        <div class="hotel-content">
                            <div class="hotel-header">
                                <div class="hotel_name_title d-flex align-items-center">
                                    <a href="<?= $hotel['link'] ?>">
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
                                        <span>105 Nguyễn Thị Minh Khai, Phuong 3, Quan 5, Tp HCM</span>
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
                                                    <div class="amenity mb-1"><i class="fa fa-wifi" ></i><?= htmlspecialchars($uti['name']) ?></div>
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
            <span class="price-value" ><?= number_format(100000, 0, ',', '.') ?> đ</span>
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


