
<section class="services-area py-5">
    <div class="container">
        <div class="section-title text-center mb-5">
            <h2 class="fw-bold" style="color: var(--text-color, #000);">Khách sạn Của Chúng Tôi</h2>
            <p class="text-muted">Khám phá các khách sạn để trải nghiệm kỳ nghỉ trọn vẹn</p>
        </div>

        <div class="box-service">
            <div class="owl-carousel owl-theme">
            <!-- Hotel 1 -->
            <div class="item">
                <div class="card-area hotel-card">
                    <div class="position-relative">
                        <img src="https://ak-d.tripcdn.com/images/0203y12000buje9xu57DA_W_1125_2436_R5.webp" class="card-img-top" alt="The Shells Resort & Spa Phú Quốc">
                        
                    </div>
                    <div class="card-body p-3">
                        <h5 class="card-title">The Shells Resort & Spa Phú Quốc</h5>
                        <p class="card-text text-muted"><i class="fas fa-map-marker-alt me-1"></i> Phú Quốc</p>
                        <p class="card-text">Wi-Fi miễn phí, Tủ lạnh mini, Máy lạnh</p>
                        <div class="rating mb-2">
                        </div>
                        <p class="card-text price text-danger fw-bold">1.473.407 đ</p>
                        <p class="card-text text-muted small text-decoration-line-through">2.694.667 đ</p>
                    </div>
                </div>
            </div>

            <!-- Hotel 2 -->
            <div class="item">
                <div class="card-area hotel-card">
                    <div class="position-relative">
                        <img src="https://ak-d.tripcdn.com/images/0203y12000buje9xu57DA_W_1125_2436_R5.webp" class="card-img-top" alt="The Shells Resort & Spa Phú Quốc">
                        
                    </div>
                    <div class="card-body p-3">
                        <h5 class="card-title">The Shells Resort & Spa Phú Quốc</h5>
                        <p class="card-text text-muted"><i class="fas fa-map-marker-alt me-1"></i> Phú Quốc</p>
                        <p class="card-text">Wi-Fi miễn phí, Tủ lạnh mini, Máy lạnh</p>
                        <div class="rating mb-2">
                        </div>
                        <p class="card-text price text-danger fw-bold">1.473.407 đ</p>
                        <p class="card-text text-muted small text-decoration-line-through">2.694.667 đ</p>
                    </div>
                </div>
            </div>

            <!-- Hotel 3 -->
            <div class="item">
                <div class="card-area hotel-card">
                    <div class="position-relative">
                        <img src="https://ak-d.tripcdn.com/images/0203y12000buje9xu57DA_W_1125_2436_R5.webp" class="card-img-top" alt="The Shells Resort & Spa Phú Quốc">
                        
                    </div>
                    <div class="card-body p-3">
                        <h5 class="card-title">The Shells Resort & Spa Phú Quốc</h5>
                        <p class="card-text text-muted"><i class="fas fa-map-marker-alt me-1"></i> Phú Quốc</p>
                        <p class="card-text">Wi-Fi miễn phí, Tủ lạnh mini, Máy lạnh</p>
                        <div class="rating mb-2">
                        </div>
                        <p class="card-text price text-danger fw-bold">1.473.407 đ</p>
                        <p class="card-text text-muted small text-decoration-line-through">2.694.667 đ</p>
                    </div>
                </div>
            </div>

            <!-- Hotel 4 -->
            <div class="item">
                <div class="card-area hotel-card">
                    <div class="position-relative">
                        <img src="https://ak-d.tripcdn.com/images/0203y12000buje9xu57DA_W_1125_2436_R5.webp" class="card-img-top" alt="The Shells Resort & Spa Phú Quốc">
                        
                    </div>
                    <div class="card-body p-3">
                        <h5 class="card-title">The Shells Resort & Spa Phú Quốc</h5>
                        <p class="card-text text-muted"><i class="fas fa-map-marker-alt me-1"></i> Phú Quốc</p>
                        <p class="card-text">Wi-Fi miễn phí, Tủ lạnh mini, Máy lạnh</p>
                        <div class="rating mb-2">
                        </div>
                        <p class="card-text price text-danger fw-bold">1.473.407 đ</p>
                        <p class="card-text text-muted small text-decoration-line-through">2.694.667 đ</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-end mb-3 d-flex custom-nav">
            <button class="btn btn-outline-primary me-2" id="customPrevBtn"><i class="fas fa-chevron-left"></i></button>
            <button class="btn btn-outline-primary" id="customNextBtn"><i class="fas fa-chevron-right"></i></button>
        </div>
        </div>
    </div>
</section>

<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<!-- Owl Carousel JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

<!-- Owl Carousel Init -->
<script>
$(document).ready(function(){
  var owl = $(".owl-carousel");

  owl.owlCarousel({
    loop: true,
    margin: 20,
    nav: false, // tắt nav mặc định nếu dùng custom
    dots: false,
    slideBy: 1,
    responsive: {
      0: { items: 1 },
      768: { items: 2 },
      992: { items: 3 },
      1200: { items: 4 }
    }
  });

  // Custom next/prev buttons
  $("#customNextBtn").click(function() {
    owl.trigger('next.owl.carousel');
  });

  $("#customPrevBtn").click(function() {
    owl.trigger('prev.owl.carousel');
  });
});

</script>

