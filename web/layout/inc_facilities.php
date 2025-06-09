<?php
$facilities = [
    'Vị trí' => [
        ['icon' => 'fas fa-city', 'name' => 'Gần trung tâm'],
        ['icon' => 'fas fa-water', 'name' => 'Gần biển'],
    ],
    'Tiện nghi' => [
        ['icon' => 'fas fa-swimming-pool', 'name' => 'Bể bơi ngoài trời'],
        ['icon' => 'fas fa-swimming-pool', 'name' => 'Bể bơi vô cực'],
        ['icon' => 'fas fa-home', 'name' => 'Phòng gia đình'],
        ['icon' => 'fas fa-umbrella-beach', 'name' => 'Ghế tắm nắng'],
        ['icon' => 'fas fa-elevator', 'name' => 'Thang máy'],
        ['icon' => 'fas fa-parking', 'name' => 'Bãi đỗ xe'],
        ['icon' => 'fas fa-wifi', 'name' => 'Wifi miễn phí'],
        ['icon' => 'fas fa-wheelchair', 'name' => 'Tiện nghi cho người khuyết tật'],
    ],
    'Dịch vụ' => [
        ['icon' => 'fas fa-plane', 'name' => 'Đưa đón sân bay miễn phí'],
        ['icon' => 'fas fa-bus', 'name' => 'Xe đưa đón theo lịch trình'],
        ['icon' => 'fas fa-chalkboard-teacher', 'name' => 'Phòng họp'],
        ['icon' => 'fas fa-users', 'name' => 'Tổ chức sự kiện'],
        ['icon' => 'fas fa-motorcycle', 'name' => 'Thuê xe máy'],
        ['icon' => 'fas fa-washer', 'name' => 'Giặt là'],
        ['icon' => 'fas fa-print', 'name' => 'Máy in/Photocopy'],
        ['icon' => 'fas fa-money-bill-wave', 'name' => 'Đổi tiền'],
    ],
    'Thể thao - Giải trí' => [
        ['icon' => 'fas fa-dumbbell', 'name' => 'Phòng GYM'],
        ['icon' => 'fas fa-spa', 'name' => 'Lớp tập Yoga'],
        ['icon' => 'fas fa-walking', 'name' => 'Khu đi bộ'],
    ],
    'Sức khỏe & Spa' => [
        ['icon' => 'fas fa-hot-tub', 'name' => 'Massage (Mát-xa)'],
        ['icon' => 'fas fa-spa', 'name' => 'Spa làm đẹp'],
    ],
    'Dịch vụ phòng' => [
        ['icon' => 'fas fa-tv', 'name' => 'Tivi'],
        ['icon' => 'fas fa-satellite-dish', 'name' => 'Truyền hình cáp'],
        ['icon' => 'fas fa-lock', 'name' => 'Két an toàn'],
        ['icon' => 'fas fa-desk', 'name' => 'Bàn làm việc'],
        ['icon' => 'fas fa-bath', 'name' => 'Bồn tắm'],
    ],
    'Cảnh quan' => [
        ['icon' => 'fas fa-umbrella-beach', 'name' => 'Bãi biển riêng'],
        ['icon' => 'fas fa-water', 'name' => 'Giáp biển'],
    ],
    'Ăn uống' => [
        ['icon' => 'fas fa-utensils', 'name' => 'Nhà hàng'],
        ['icon' => 'fas fa-glass-cheers', 'name' => 'Quầy bar'],
    ],
    'Ngôn Ngữ' => [
        ['icon' => 'fas fa-language', 'name' => 'Tiếng Việt'],
        ['icon' => 'fas fa-language', 'name' => 'Tiếng Anh'],
    ],
];
?>

<div class="facilities-section">
    <h5 class="title-section" id="box_hotel_attribute">Tiện ích</h5>
    <div class="facilities-list">
        <?php foreach ($facilities as $group_name => $items): ?>
            <div class="facility-group">
                <h3 class="group-title"><?php echo $group_name; ?></h3>
                <div class="row">
                    <?php foreach ($items as $item): ?>
                        <div class="col-6 col-sm-4 col-md-3">
                            <div class="facility-item">
                                <i class="<?php echo $item['icon']; ?>"></i>
                                <span><?php echo $item['name']; ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
    .facilities-section {
        background-color: var(--white);
        border-radius: 0.5rem;
        padding: 1rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }

    .facilities-section .title-section {
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .facility-group {
        margin-bottom: 10px;
    }

    .facility-group .group-title {
        color: var(--text-color);
        font-weight: 500;
        font-size: 1.125rem;
        margin-bottom: 1rem;
    }

    .facility-item {
        display: flex;
        align-items: center;
        padding: 0.5rem 0;
        color: var(--text-color);
    }

    .facility-item i {
        color: var(--accent-color);
        margin-right: 0.75rem;
        font-size: 14px;
    }

    .facility-item span {
        font-size: 14px;
        color: var(--text-light);
    }

    @media (max-width: 576px) {
        .facilities-section {
            padding: 1rem;
        }

        .facilities-section .title-section {
            font-size: 1.25rem;
        }

        .facility-group .group-title {
            font-size: 1rem;
        }

        .facility-item span {
            font-size: 0.875rem;
        }
    }
</style>