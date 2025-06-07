<header class="header-style-modern bg-light shadow-sm sticky-top">
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container custom-container-one">
            <!-- Logo -->
            <a class="navbar-brand" href="/">
                <img src="<?=$cfg_path_image?>logo.png" alt="Nature Hotel Logo" class="img-fluid" style="max-height: 50px;">
            </a>

            <!-- Toggler for mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#hotel_booking_menu" aria-controls="hotel_booking_menu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Mobile right icon -->
            <a href="javascript:void(0)" class="click-nav-right-icon d-lg-none ms-2">
                <i class="fas fa-ellipsis-v"></i>
            </a>

            <!-- Navbar Menu -->
            <div class="collapse navbar-collapse" id="hotel_booking_menu">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/gioi-thieu">Giới thiệu</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="locationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Địa điểm
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="locationDropdown">
                            <li><a class="dropdown-item" href="https://naturehotel.vn/vi/location/hanoi">Hà Nội</a></li>
                            <li><a class="dropdown-item" href="https://naturehotel.vn/vi/location/danang">Đà Nẵng</a></li>
                            <li class="dropdown-submenu">
                                <a class="dropdown-item dropdown-toggle" href="https://naturehotel.vn/vi/location/dalat">Đà Lạt</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="https://naturehotel.vn/vi/hotel/nature-hotel-le-hong-phong">Nature Hotel - Lê Hồng Phong</a></li>
                                    <li><a class="dropdown-item" href="https://naturehotel.vn/vi/hotel/nature-hotel-luong-the-vinh">Nature Hotel - Lương Thế Vinh</a></li>
                                    <li><a class="dropdown-item" href="https://naturehotel.vn/vi/hotel/nature-boutique-hotel-nguyen-thi-nghia">Nature Boutique Hotel - Nguyễn Thị Nghĩa</a></li>
                                    <li><a class="dropdown-item" href="https://naturehotel.vn/vi/hotel/nature-hotel-nam-ky-khoi-nghia">Nature Hotel - Nam Kỳ Khởi Nghĩa</a></li>
                                    <li><a class="dropdown-item" href="https://naturehotel.vn/vi/hotel/nature-hotel-phan-boi-chau">Nature Hotel - Phan Bội Châu</a></li>
                                    <li><a class="dropdown-item" href="https://naturehotel.vn/vi/hotel/nature-hotel-le-dai-hanh">Nature Hotel - Lê Đại Hành</a></li>
                                </ul>
                            </li>
                            <li><a class="dropdown-item" href="https://naturehotel.vn/vi/location/hoian">Hội An</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="servicesDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Dịch vụ
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="servicesDropdown">
                            <li><a class="dropdown-item" href="https://naturehotel.vn/vi/page/dich-vu-tour">Dịch vụ Tour</a></li>
                            <li><a class="dropdown-item" href="https://naturehotel.vn/vi/page/dua-don-van-chuyen">Dịch vụ đưa đón sân bay</a></li>
                            <li><a class="dropdown-item" href="https://naturehotel.vn/vi/page/dich-vu-cho-thue-xe-may">Dịch vụ thuê xe máy</a></li>
                            <li><a class="dropdown-item" href="https://naturehotel.vn/vi/page/dich-vu-giat-la">Dịch vụ giặt là</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="newsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Tin tức & Sự kiện
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="newsDropdown">
                            <li><a class="dropdown-item" href="https://naturehotel.vn/vi/posts/tuyen-dung">Tuyển dụng</a></li>
                            <li><a class="dropdown-item" href="https://naturehotel.vn/vi/posts/tin-tuc">Tin tức</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://naturehotel.vn/vi/contact">Liên hệ</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>