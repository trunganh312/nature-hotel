<?php
use src\Models\Hotel;

// Lấy danh sách thành phố có khách sạn
$cities_with_hotels = Hotel::where('hot_active', 1)
    ->select('cit_id', 'cit_name')
    ->join('cities', 'hot_city', 'cit_id')
    ->toArray();

// Loại bỏ trùng lặp trong PHP
$unique_cities = [];
$city_ids = [];
foreach ($cities_with_hotels as $city) {
    if (!in_array($city['cit_id'], $city_ids)) {
        $city_ids[] = $city['cit_id'];
        $unique_cities[] = $city;
    }
}

$data_cities = [];
foreach ($unique_cities as $city) {
    $slug = to_slug($city['cit_name']);
    // Lấy danh sách khách sạn cho thành phố này
    $city_hotels = Hotel::where('hot_active', 1)
        ->where('hot_city', $city['cit_id'])
        ->select('hot_id', 'hot_name')
        ->toArray();

    // Loại bỏ khách sạn trùng tên (hoặc trùng id tuỳ ý bạn)
    $unique_hotels = [];
    $hotel_names = [];
    foreach ($city_hotels as $hotel) {
        if (!in_array($hotel['hot_name'], $hotel_names)) {
            $hotel_names[] = $hotel['hot_name'];
            $hotel['link'] = '/hotel-' . $hotel['hot_id'] . '-' . to_slug($hotel['hot_name']) . '.html';
            $unique_hotels[] = $hotel;
        }
    }

    $data_cities[] = [
        'id' => $city['cit_id'],
        'name' => $city['cit_name'],
        'link' => '/city-' . $city['cit_id'] . '-' . $slug . '.html',
        'hotels' => $unique_hotels,
    ];
}
$data_cities = array_values($data_cities);
?>

<header class="header-style-modern bg-light shadow-sm sticky-top">
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container custom-container-one">
            <!-- Logo -->
            <a class="navbar-brand" href="/">
                <img src="<?= $cfg_path_image ?>logo.png" alt="Nature Hotel Logo" class="img-fluid" style="max-height: 50px;">
            </a>

            <!-- Toggler for mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#hotel_booking_menu" aria-controls="hotel_booking_menu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Mobile right icon -->
            <!-- <a href="javascript:void(0)" class="click-nav-right-icon d-lg-none ms-2">
                <i class="fas fa-ellipsis-v"></i>
            </a> -->

            <!-- Navbar Menu -->
            <div class="collapse navbar-collapse" id="hotel_booking_menu">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Trang chủ</a>
                    </li>
                    <!-- Place -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="locationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Địa điểm
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="locationDropdown">
                            <?php if (empty($data_cities)): ?>
                                <li><a class="dropdown-item" href="#">Chưa có địa điểm</a></li>
                            <?php else: ?>
                                <?php foreach ($data_cities as $city): ?>
                                    
                                    <?php $has_submenu = !empty($city['hotels']); ?>
                                    <li class="<?php echo $has_submenu ? 'dropdown-submenu' : ''; ?>">
                                        <a class="dropdown-item <?php echo $has_submenu ? 'dropdown-toggle' : ''; ?>" href="<?php echo htmlspecialchars($city['link']); ?>">
                                            <?php echo htmlspecialchars($city['name']); ?>
                                        </a>
                                        <?php if ($has_submenu): ?>
                                            <ul class="dropdown-menu">
                                                <?php foreach ($city['hotels'] as $index => $hotel): 
                                                    ?>
                                                    <li>
                                                        <a class="dropdown-item" href="<?php echo htmlspecialchars($hotel['link']); ?>">
                                                            <?php echo htmlspecialchars($hotel['hot_name']); ?>
                                                        </a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/khach-san.html">Khách sạn</a>
                    </li>
                    <!-- introduce -->
                    <li class="nav-item">
                        <a class="nav-link" href="/introduce.html">Giới thiệu</a>
                    </li>
                    
                    <!-- Service -->
                    <!-- <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="servicesDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Dịch vụ
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="servicesDropdown">
                            <li><a class="dropdown-item" href="https://naturehotel.vn/vi/page/dich-vu-tour">Dịch vụ Tour</a></li>
                            <li><a class="dropdown-item" href="https://naturehotel.vn/vi/page/dua-don-van-chuyen">Dịch vụ đưa đón sân bay</a></li>
                            <li><a class="dropdown-item" href="https://naturehotel.vn/vi/page/dich-vu-cho-thue-xe-may">Dịch vụ thuê xe máy</a></li>
                            <li><a class="dropdown-item" href="https://naturehotel.vn/vi/page/dich-vu-giat-la">Dịch vụ giặt là</a></li>
                        </ul>
                    </li> -->

                    <!-- News -->
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="/news.html">Tin tức</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contact.html">Liên hệ</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>