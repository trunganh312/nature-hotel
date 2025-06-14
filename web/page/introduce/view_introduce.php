<?php
// Khai báo biến toàn cục
global $glob;

// Thiết lập tiêu đề trang
$glob['title'] = "Giới thiệu - Nature Hotel | Chuỗi khách sạn cao cấp tại Việt Nam";
?>

<!-- Banner chính -->
<div class="introduce-banner">
    <img src="https://img.tripi.vn/cdn-cgi/image/width=1280,height=1280/https://gcs.tripi.vn/hms_prod/photo/img/474562FVZ/20230223-20230223_091640.jpg" alt="Nature Hotel Banner">
    <div class="banner-overlay">
        <h1>NATURE HOTEL</h1>
        <p>Trải nghiệm nghỉ dưỡng độc đáo và thư thái nhất tại chuỗi khách sạn Nature Hotel</p>
    </div>
</div>

<!-- Nội dung giới thiệu -->
<div class="introduce-content">
    <!-- Phần Giới thiệu chung -->
    <div class="about-section">
        <div class="about-text">
            <h2>Về NATURE HOTEL</h2>
            <p>NATURE HOTEL là chuỗi khách sạn thuộc Công Ty Nature Hospitality nổi tiếng với những nơi lưu trú có vị trí đắc địa và thiết kế riêng biệt ở các thành phố du lịch nổi tiếng tại Việt Nam.</p>
            <p>Với mong muốn mang đến cho khách hàng những trải nghiệm hiện đại, thoải mái và dịch vụ tốt nhất, NATURE HOTEL luôn cố gắng, tận tâm, chu đáo, để trở thành nơi lưu trú an toàn, đáng tin cậy cho khách hàng trong mỗi chuyến du lịch nghỉ dưỡng, khám phá hay công tác của du khách trong và ngoài nước.</p>
            <p>Mỗi khách sạn trong chuỗi NATURE HOTEL đều được thiết kế và trang bị đầy đủ tiện nghi hiện đại, kết hợp với không gian thanh bình hòa hợp với thiên nhiên, tạo nên trải nghiệm nghỉ dưỡng tuyệt vời cho quý khách hàng.</p>
        </div>
        <div class="about-image">
            <img src="../../nature-hotel.jpg" alt="Nature Hotel - Khách sạn hòa mình với thiên nhiên">
        </div>
    </div>
    
    <!-- Phần Đặc điểm nổi bật -->
    <div class="feature-section">
        <h2>Điểm Nổi Bật Của Chúng Tôi</h2>
        <div class="features-container">
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <h3>Vị Trí Đắc Địa</h3>
                <p>Các chi nhánh NATURE HOTEL đều nằm ở vị trí trung tâm, thuận tiện cho việc tham quan, mua sắm và trải nghiệm cuộc sống địa phương.</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-bed"></i>
                </div>
                <h3>Phòng Nghỉ Tiện Nghi</h3>
                <p>Các phòng được thiết kế hiện đại, đầy đủ tiện nghi cao cấp, mang lại sự thoải mái tối đa cho quý khách trong suốt thời gian lưu trú.</p>
            </div>
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-concierge-bell"></i>
                </div>
                <h3>Dịch Vụ Chuyên Nghiệp</h3>
                <p>Đội ngũ nhân viên được đào tạo bài bản, tận tâm và chuyên nghiệp, luôn sẵn sàng đáp ứng mọi nhu cầu của quý khách.</p>
            </div>
        </div>
    </div>
    
<!-- Phần Các chi nhánh -->
<div class="locations-section">
    <h2>Các Chi Nhánh NATURE HOTEL</h2>
    <div class="hotel-locations">
        <?php foreach ($data_hotels as $hotel): ?>
            <div class="location-item">
                <a href="<?= $hotel['link'] ?>">
                    <div class="location-image">
                    <img src="<?= htmlspecialchars($hotel['img']) ?>" alt="<?= htmlspecialchars($hotel['name']) ?>">
                </div>
                <div class="location-info">
                    <h3><?= htmlspecialchars($hotel['name']) ?></h3>
                    <p><?= htmlspecialchars($hotel['intro']) ?></p>
                    <p>Địa chỉ: <?= htmlspecialchars($hotel['address']) ?></p>
                    <p class="location-contact">Liên hệ: <?= htmlspecialchars($hotel['phone']) ?></p>
                </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>
    
    <!-- Phần Call to Action -->
    <div class="cta-section">
        <div class="cta-content">
            <h2>Trải Nghiệm Kỳ Nghỉ Tuyệt Vời Cùng NATURE HOTEL</h2>
            <p>Hãy đến với chuỗi thương hiệu NATURE HOTEL để trải nghiệm những kỳ nghỉ dưỡng độc đáo và thư thái nhất! Đặt phòng ngay hôm nay để nhận được những ưu đãi hấp dẫn.</p>
            <button class="introduce-cta-button"><a href="/khach-san.html" style="color: #fff;">ĐẶT PHÒNG NGAY</a></button>
        </div>
    </div>
    
    <!-- Phần Thông tin liên hệ -->
    <div class="about-section">
        <div class="about-image">
            <img src="https://img.tripi.vn/cdn-cgi/image/width=1280,height=1280/https://gcs.tripi.vn/hms_prod/photo/img/474562FVZ/20230223-20230223_091640.jpg" alt="Liên hệ Nature Hotel">
        </div>
        <div class="about-text">
            <h2>Thông Tin Liên Hệ</h2>
            <p><strong>Công ty cổ phần Nature Hospitality</strong></p>
            <p>Địa chỉ: 62 Khúc Thừa Dụ, Phường Dịch Vọng, Quận Cầu Giấy, Thành phố Hà Nội, Việt Nam</p>
            <p>Điện thoại: (+84) 0243 223 2223</p>
            <p>Email: datphong@naturehotel.vn</p>
            <p>Website: naturehotel.vn</p>
        </div>
    </div>
</div>