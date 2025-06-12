<div class="contact container">
    <div class="contact-title py-5">
        <h2>Liên hệ</h2>
    </div>
    <div class="contact-hero row">
        <div class="contact-hero__image col-lg-7">
            <img src="https://img.tripi.vn/cdn-cgi/image/width=1280,height=1280/https://gcs.tripi.vn/hms_prod/photo/img/474562FVZ/20230223-20230223_091640.jpg" alt="Nature Hotel Hà Nội">
        </div>
        <div class="contact-hero__content col-lg-5">
            <h2 class="contact-hero__title">Liên hệ với chúng tôi</h2>
            <p class="contact-hero__text">Đội ngũ D.Selene rất vui được hỗ trợ bạn với các đơn hàng, tư vấn phong cách, ý tưởng quà tặng và nhiều hơn nữa. Vui lòng chọn phương thức liên hệ yêu thích của bạn bên dưới</p>
        </div>
    </div>
    <div class="contact-methods row">
        <div class="contact-method col-lg-4">
            <div class="contact-method__icon">
                <i class="fas fa-phone-alt"></i>
            </div>
            <h3 class="contact-method__title">Gọi chúng tôi</h3>
            <p class="contact-method__subtitle">Giờ làm việc của D.Selene Client Relations theo giờ Việt Nam</p>
            <p class="contact-method__info">
                Các ngày trong tuần - từ 9 giờ sáng đến 5 giờ chiều<br>
                Chủ nhật - từ 10 giờ sáng đến 8 giờ tối
            </p>
            <a href="tel:19001234" class="contact-method__link">SĐT: 1900.1234</a>
        </div>
        
        <div class="contact-method col-lg-4">
            <div class="contact-method__icon">
                <i class="far fa-envelope"></i>
            </div>
            <h3 class="contact-method__title">Email</h3>
            <p class="contact-method__subtitle">Đại sứ D.Selene sẽ trả lời bạn sớm nhất có thể</p>
            <a href="mailto:info@dselene.com" class="contact-method__link">Email: info@dselene.com</a>
        </div>
        
        <div class="contact-method col-lg-4">
            <div class="contact-method__icon">
                <i class="fas fa-hashtag"></i>
            </div>
            <h3 class="contact-method__title">Mạng xã hội</h3>
            <p class="contact-method__subtitle">Theo dõi chúng tôi trên các mạng xã hội để nhận thông tin mới nhất</p>
            <div class="contact-method__social">
                <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-tiktok"></i></a>
            </div>
        </div>
    </div>
    <div class="contact-store row">
        <div class="contact-store__info col-lg-5">
            <h3 class="contact-store__title">Tìm chúng tôi</h3>
            <p class="contact-store__description">Find your nearest D.Selene store or authorized retailer</p>
            <a href="#" class="contact-store__link">Cửa hàng gần nhất <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="contact-store__map col-lg-7">
            <div class="map-placeholder">
                <iframe width="100%" height="450" style="border: 0" loading="lazy" allowfullscreen
                    src="https://maps.google.com/maps?q=<?= $hotel['hot_lat'] ?>,<?= $hotel['hot_lon'] ?>&hl=vi&z=14&output=embed">
                </iframe>
            </div>
        </div>
    </div>
    <form action="" class="contact-form">
        <h3 class="contact-form__title">Gửi thông tin cho chúng tôi</h3>
        
        <div class="form-group">
            <input type="text" id="name" class="form-control border-none" required placeholder="Nhập họ và tên">
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="form-group">
                    <input type="email" id="email" class="form-control border-none" required placeholder="Nhập email">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    <input type="tel" id="phone" class="form-control border-none" required placeholder="Nhập số điện thoại">
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <textarea id="message" rows="5" class="form-control border-none" required placeholder="Nhập nội dung"></textarea>
        </div>
        
        <button type="submit" class="btn-submit">Gửi đi</button>
    </form>
</div>