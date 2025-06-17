<?php
use src\Models\Hotel;

// Lấy dữ liệu từ cookies
$search_location = $_COOKIE['search_location'] ?? '';
$search_location_id = $_COOKIE['search_location_id'] ?? '';
$search_checkin = $_COOKIE['search_checkin'] ?? $cfg_date_checkin;
$search_checkout = $_COOKIE['search_checkout'] ?? $cfg_date_checkout;
$search_room_qty = $_COOKIE['search_room_qty'] ?? '1';
$search_adult_qty = $_COOKIE['search_adult_qty'] ?? '2';
$search_child_qty = $_COOKIE['search_child_qty'] ?? '0';

$search = Hotel::where('hot_active', 1)
    ->select('cit_name', 'cit_id', 'cit_image', 'hot_id', 'hot_name', 'hot_address_full', 'hot_picture')
    ->join('cities', 'hot_city', 'cit_id')
    ->toArray();
$data_city = [];
foreach ($search as $hotel) {
    $name = $hotel['cit_name'];
    if (!isset($data_city[$name])) {
        $slug = to_slug($name);
        $data_city[$name] = [
            'name' => $name,
            'value' => 1,
            'link' => '/city-' . $hotel['cit_id'] . '-' . $slug . '.html',
            'img' => $hotel['cit_image']
        ];
    } else {
        $data_city[$name]['value']++;
    }
}
$data_city = array_values($data_city);
?>

<div class="baler-box">
    <div class="container">
        <div class="baler-info">
            <div class="row search-place search-responsive">
                <div class="col-md-4 search_width_100 search-padding_tablet indicator_search">
                    <div class="place">
                        <label for="locationInput">Địa điểm</label>
                        <input class="search-input ui-widget" id="locationInput" placeholder="Thành phố"
                            value="">
                        <input type="hidden" id="selectedLocationId" name="locationId">
                    </div>
                </div>
                <div class="col-md-4 nopadding_mobile search_width_100 indicator_search">
                    <div class="date search-padding_tablet">
                        <div class="travel-date col-md-5 me-3">
                            <h6>Ngày đến</h6>
                            <div id="startDateText"><?php echo $cfg_date_checkin; ?></div>
                        </div>
                        <div class="moon col-md-2">
                            <i class="fas fa-moon"></i>
                        </div>
                        <div class="return-date col-md-5 ms-4">
                            <h6>Ngày về</h6>
                            <div id="endDateText"><?php echo $cfg_date_checkout; ?></div>
                        </div>
                        <input name="datetimes" style="opacity: 0; position: absolute; width: 100%;" />
                    </div>
                </div>
                <div class="col-md-3 col-12 mb-3 mb-md-0 nopadding_mobile search_width_100 indicator_search" id="select_op">
                    <div class="room-selector search-padding_tablet">
                        <h6>Số phòng, số khách</h6>
                        <div class="selected-option" id="selectedOption"><?= htmlspecialchars($search_room_qty) ?>
                            phòng, <?= htmlspecialchars($search_adult_qty) ?> người lớn,
                            <?= htmlspecialchars($search_child_qty) ?> trẻ em
                        </div>
                        <input type="hidden" id="roomQty" name="roomQty"
                            value="<?= htmlspecialchars($search_room_qty) ?>">
                        <input type="hidden" id="adultQty" name="adultQty"
                            value="<?= htmlspecialchars($search_adult_qty) ?>">
                        <input type="hidden" id="childQty" name="childQty"
                            value="<?= htmlspecialchars($search_child_qty) ?>">
                    </div>
                </div>
                <div class="btnSreach col-md-2">
                    <i class="fas fa-search"></i>
                </div>
            </div>
            <!-- Slide menu for mobile and tablet -->
            <div class="slide-menu" id="slideMenu">
                <div class="slide-menu-header">
                    <span class="slide-menu-title"></span>
                    <button class="slide-menu-close"><i class="fas fa-times"></i></button>
                </div>
                <div class="slide-menu-content" id="slideMenuContent">
                    <!-- Location content -->
                    <div class="location-content" style="display: none;">
                        <div class="recent-title">
                            <span>Địa điểm nổi bật</span>
                        </div>
                        <div class="popular-destinations">
                            <?php if (!empty($data_city)): ?>
                                <?php foreach ($data_city as $city): ?>
                                    <div class="destination" data-link="<?= htmlspecialchars($city['link']) ?>"
                                        data-city-name="<?= htmlspecialchars($city['name']) ?>">
                                        <img src="<?= $cfg_path_image ?>city/<?= $city['img'] ?>"
                                            alt="<?= htmlspecialchars($city['name']) ?>">
                                        <div class="destination-name"><?= htmlspecialchars($city['name']) ?></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- Room selector content -->
                    <div class="room-selector-content" style="display: none;">
                        <div class="option">
                            <h4>Đi một mình</h4>
                            <p>1 phòng, 1 người lớn</p>
                        </div>
                        <div class="option">
                            <h4>Đi cặp đôi/2 người</h4>
                            <p>1 phòng, 2 người lớn</p>
                        </div>
                        <div class="option has-detail" data-type="family">
                            <h4>Đi theo gia đình</h4>
                            <div class="detail-panel">
                                <div class="detail-item">
                                    <label>Số phòng</label>
                                    <div class="counter">
                                        <button class="minus">-</button>
                                        <span>1</span>
                                        <button class="plus">+</button>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <label>Người lớn</label>
                                    <div class="counter">
                                        <button class="minus">-</button>
                                        <span>2</span>
                                        <button class="plus">+</button>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <label>Trẻ em</label>
                                    <div class="counter">
                                        <button class="minus">-</button>
                                        <span>0</span>
                                        <button class="plus">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="option has-detail" data-type="group">
                            <h4>Đi theo nhóm</h4>
                            <div class="detail-panel">
                                <div class="detail-item">
                                    <label>Số phòng</label>
                                    <div class="counter">
                                        <button class="minus">-</button>
                                        <span>1</span>
                                        <button class="plus">+</button>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <label>Người lớn</label>
                                    <div class="counter">
                                        <button class="minus">-</button>
                                        <span>2</span>
                                        <button class="plus">+</button>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <label>Trẻ em</label>
                                    <div class="counter">
                                        <button class="minus">-</button>
                                        <span>0</span>
                                        <button class="plus">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="option has-detail" data-type="business">
                            <h4>Đi công tác</h4>
                            <div class="detail-panel">
                                <div class="detail-item">
                                    <label>Số phòng</label>
                                    <div class="counter">
                                        <button class="minus">-</button>
                                        <span>1</span>
                                        <button class="plus">+</button>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <label>Người lớn</label>
                                    <div class="counter">
                                        <button class="minus">-</button>
                                        <span>2</span>
                                        <button class="plus">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Dropdown panel for PC -->
            <div class="dropdown-panel" id="dropdownPanel">
                <div class="box row">
                    <div class="location_box_left col-md-7">
                        <div class="recent-title">
                            <span>Địa điểm nổi bật</span>
                        </div>
                        <div class="popular-destinations">
                            <?php if (!empty($data_city)): ?>
                                <?php foreach ($data_city as $city): ?>
                                    <div class="destination" data-link="<?= htmlspecialchars($city['link']) ?>"
                                        data-city-name="<?= htmlspecialchars($city['name']) ?>">
                                        <img src="<?= $cfg_path_image ?>city/<?= $city['img'] ?>"
                                            alt="<?= htmlspecialchars($city['name']) ?>">
                                        <div class="destination-name"><?= htmlspecialchars($city['name']) ?></div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Dropdown menu for PC -->
            <div class="dropdown-menu" id="roomSelectorDropdown">
                <div class="option">
                    <h4>Đi một mình</h4>
                    <p>1 phòng, 1 người lớn</p>
                </div>
                <div class="option">
                    <h4>Đi cặp đôi/2 người</h4>
                    <p>1 phòng, 2 người lớn</p>
                </div>
                <div class="option has-detail" data-type="family">
                    <h4>Đi theo gia đình</h4>
                    <div class="detail-panel">
                        <div class="detail-item">
                            <label>Số phòng</label>
                            <div class="counter">
                                <button class="minus">-</button>
                                <span>1</span>
                                <button class="plus">+</button>
                            </div>
                        </div>
                        <div class="detail-item">
                            <label>Người lớn</label>
                            <div class="counter">
                                <button class="minus">-</button>
                                <span>2</span>
                                <button class="plus">+</button>
                            </div>
                        </div>
                        <div class="detail-item">
                            <label>Trẻ em</label>
                            <div class="counter">
                                <button class="minus">-</button>
                                <span>0</span>
                                <button class="plus">+</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="option has-detail" data-type="group">
                    <h4>Đi theo nhóm</h4>
                    <div class="detail-panel">
                        <div class="detail-item">
                            <label>Số phòng</label>
                            <div class="counter">
                                <button class="minus">-</button>
                                <span>1</span>
                                <button class="plus">+</button>
                            </div>
                        </div>
                        <div class="detail-item">
                            <label>Người lớn</label>
                            <div class="counter">
                                <button class="minus">-</button>
                                <span>2</span>
                                <button class="plus">+</button>
                            </div>
                        </div>
                        <div class="detail-item">
                            <label>Trẻ em</label>
                            <div class="counter">
                                <button class="minus">-</button>
                                <span>0</span>
                                <button class="plus">+</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="option has-detail" data-type="business">
                    <h4>Đi công tác</h4>
                    <div class="detail-panel">
                        <div class="detail-item">
                            <label>Số phòng</label>
                            <div class="counter">
                                <button class="minus">-</button>
                                <span>1</span>
                                <button class="plus">+</button>
                            </div>
                        </div>
                        <div class="detail-item">
                            <label>Người lớn</label>
                            <div class="counter">
                                <button class="minus">-</button>
                                <span>2</span>
                                <button class="plus">+</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="indicator"></div>
        </div>
    </div>
</div>

<style>
.baler-box {
    position: absolute;
    top: 87%;
    left: 50%;
    transform: translate(-50%, 0%);
    width: 100%;
    z-index: 1;
}

.baler-box h2 {
    color: white;
    font-size: 38px;
    margin-bottom: 20px;
    font-weight: 600;
    padding-left: 62px;
}

.baler-info {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin: 0 auto;
    max-width: 1200px;
    position: relative;
}

.search-place {
    padding: 12px 26px;
    display: flex;
    align-items: center;
}

.place>label {
    color: #4A5568;
    font-size: 14px;
    line-height: 14px;
}

.place>input {
    font-size: 18px;
    font-weight: 600;
    border: 0;
    height: 1.6em;
    width: 100%;
    margin: 0;
    display: block;
    padding: 0 !important;
    background: none;
    color: #333;
}

.place input::placeholder {
    font-size: 18px;
    color: #888888;
    opacity: 0.9;
    font-weight: 500;
}

.place>input:focus {
    outline: none;
    box-shadow: none;
}

.dropdown-panel {
    display: none;
    position: absolute;
    top: 110%;
    left: 0.2%;
    width: 800px;
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    z-index: 10001;
}

.dropdown-panel.show {
    display: block;
}

.dropdown-panel .box {
    display: flex;
}

.recent-title {
    padding: 0 12px;
    display: flex;
    justify-content: space-between;
    font-weight: bold;
    color: #333;
    align-items: center;
}

.recent-title span {
    font-size: 18px;
}

.popular-destinations {
    gap: 4px;
    display: grid;
    margin-top: 8px;
    grid-template-columns: repeat(6, 1fr);
}

.destination {
    width: 120px;
    gap: 6px;
    cursor: pointer;
    display: flex;
    padding: 4px 8px;
    transition: all 0.3s;
    align-items: center;
    border-radius: 8px;
    flex-direction: column;
}

.destination img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
}

.destination-name {
    margin-top: 5px;
    font-size: 14px;
}

.date {
    gap: 10px;
    cursor: pointer;
    position: relative;
    display: flex;
    align-items: center;
    border: none;
}

.travel-date,
.return-date {
    align-items: center;
    justify-content: center;
    flex: 1;
}

.moon {
    color: #666;
    margin-left: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.travel-date>h6,
.return-date>h6,
.room-selector>h6 {
    color: #4a5568;
    font-size: 14px;
    line-height: 14px;
    margin: 5px 0 3px;
}

.travel-date>div,
.return-date>div,
.room-selector>div {
    font-weight: 700;
    font-size: 17px;
}

.btnSreach {
    width: 88px;
    height: 45px;
    background: var(--primary-color);
    color: white;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 10px 20px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    transition: background 0.3s ease, color 0.3s ease;
}

.btnSreach>i {
    font-size: 25px;
    color: white;
}

.btnSreach:hover {
    background: var(--secondary-color);
    color: rgba(255, 255, 255, 0.8);
}

.room-selector {
    cursor: pointer;
}

.selected-option {
    max-width: 16px;
    white-space: nowrap;
}

.dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    right: 8%;
    width: 300px;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    margin-top: 5px;
}

.dropdown-menu.show {
    display: block;
}

.option {
    padding: 12px 16px;
    border-bottom: 1px solid #e2e8f0;
    transition: background 0.2s;
}

.option:last-child {
    border-bottom: none;
}

.option:hover {
    background: #f8fafc;
}

.option h4 {
    margin: 0;
    font-size: 14px;
    color: #1a202c;
    font-weight: 500;
}

.option p {
    margin: 4px 0 0;
    font-size: 12px;
    color: #64748b;
}

.detail-panel {
    display: none;
    padding: 15px;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    margin-top: 10px;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.detail-item:last-child {
    margin-bottom: 0;
}

.detail-item label {
    font-size: 14px;
    color: #4a5568;
}

.counter {
    display: flex;
    align-items: center;
    gap: 10px;
}

.counter button {
    width: 28px;
    height: 28px;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    background: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.counter button:hover {
    background: #f1f5f9;
}

.counter span {
    min-width: 20px;
    text-align: center;
}

.option.active .detail-panel {
    display: block;
}

.indicator {
    border-radius: 1px;
    position: absolute;
    bottom: 0;
    left: 0;
    height: 3px;
    background-color: var(--primary-color);
    transition: all 0.3s ease;
    width: 0;
    opacity: 0;
}

/* Slide menu styles - Cập nhật để chiếm toàn màn hình */
.slide-menu {
    position: fixed;
    top: 0;
    left: -100%;
    width: 100%; /* Chiếm toàn màn hình */
    max-width: none; /* Bỏ giới hạn max-width */
    height: 100vh;
    background: white;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
    z-index: 10002;
    transition: left 0.3s ease;
    display: none;
}

.slide-menu.show {
    left: 0;
    display: block;
}

.slide-menu-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    border-bottom: 1px solid #e2e8f0;
    background: #f8fafc;
}

.slide-menu-title {
    font-size: 18px;
    font-weight: 600;
    color: #1a202c;
}

.slide-menu-close {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: #4a5568;
}

.slide-menu-content {
    padding: 15px;
    overflow-y: auto;
    height: calc(100% - 60px);
}

/* Tinh chỉnh popular-destinations trong slide-menu */
.slide-menu .popular-destinations {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 10px;
    margin-top: 10px;
}

.slide-menu .destination {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 8px;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.2s;
}

.slide-menu .destination:hover {
    background: #f1f5f9;
}

.slide-menu .destination img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 8px;
}

.slide-menu .destination-name {
    font-size: 14px;
    text-align: center;
    color: #1a202c;
}

/* DateRangePicker styles for mobile */
.daterangepicker {
    z-index: 10003 !important;
}

/* Media tablet */
@media (min-width: 576px) and (max-width: 991.98px) {
    .place {
        margin-bottom: 10px !important;
    }
    .date,
    .room-selector {
        margin-bottom: 20px !important;
    }
    .baler-info {
        top: 230px !important;
    }
    .search-padding_tablet {
        padding: 0 30px !important;
    }
    .search_width_100 {
        width: 100% !important;
    }
    .baler-box {
        top: 35%;
    }
    .search-responsive {
        display: flex;
        flex-direction: column;
    }
    .btnSreach {
        max-width: 100%;
        width: 100%;
        margin-top: 15px;
        margin: 0;
    }
    .search-place {
        padding: 6px 12px 12px 12px;
    }
    .nopadding_mobile {
        padding: 0;
    }
    .dropdown-panel,
    .dropdown-menu {
        display: none !important;
    }
    .date {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        padding: 10px;
        border-bottom: 1px solid #e0e0e0;
        border-radius: 8px;
        background-color: white;
    }
    .travel-date,
    .return-date {
        width: auto !important;
        margin: 0 !important;
        flex: 1;
    }
    .moon {
        width: auto !important;
        margin: 0 15px !important;
    }
    .room-selector {
        padding: 10px;
        border-bottom: 1px solid #e0e0e0;
        border-radius: 8px;
        background-color: white;
        width: 100%;
    }
    #select_op {
        max-width: 100%;
        margin-right: 0 !important;
    }
    .daterangepicker {
        position: fixed !important;
        top: 50% !important;
        left: 50% !important;
        right: auto !important;
        bottom: auto !important;
        transform: translate(-50%, -50%) !important;
        width: 95% !important;
        max-width: 800px !important;
        margin: 0 !important;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }
    .daterangepicker .drp-calendar {
        width: 48% !important;
        max-width: 48% !important;
        margin: 0 1% !important;
        font-size: 11px !important;
    }
    .daterangepicker .drp-calendar.right {
        display: block !important;
    }
    .daterangepicker td,
    .daterangepicker th {
        width: 25px !important;
        height: 25px !important;
        min-width: 25px !important;
        line-height: 25px !important;
        font-size: 10px !important;
        padding: 0 !important;
    }
    .daterangepicker .drp-calendar.left,
    .daterangepicker .drp-calendar.right {
        padding: 5px !important;
        border-right: none !important;
    }
    .daterangepicker th.month {
        font-size: 12px !important;
        padding: 0 !important;
    }
    .daterangepicker .prev,
    .daterangepicker .next {
        width: 20px !important;
        height: 20px !important;
        line-height: 20px !important;
        font-size: 12px !important;
    }
}

@media (max-width: 576px) {
    .baler-info {
        padding: 5px 20px;
    }
    .page_home .baler-box { 
        top: 50% !important;
    }
    .baler-box {
        top: 35%;
    }
    .search-responsive {
        display: flex;
        flex-direction: column;
    }
    .btnSreach {
        max-width: 100%;
        width: 100%;
        margin-top: 15px;
        margin: 0;
    }
    .search-place {
        padding: 6px 12px 12px 12px;
    }
    .nopadding_mobile {
        padding: 0;
    }
    .dropdown-panel,
    .dropdown-menu {
        display: none !important;
    }
    .date {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        padding: 10px;
        border-bottom: 1px solid #e0e0e0;
        border-radius: 8px;
        background-color: white;
    }
    .travel-date,
    .return-date {
        width: auto !important;
        margin: 0 !important;
        flex: 1;
    }
    .moon {
        width: auto !important;
        margin: 0 15px !important;
    }
    .room-selector {
        padding: 10px;
        border-bottom: 1px solid #e0e0e0;
        border-radius: 8px;
        background-color: white;
        width: 100%;
    }
    #select_op {
        max-width: 100%;
        margin-right: 0 !important;
    }
    .daterangepicker {
        position: fixed !important;
        top: 50% !important;
        left: 50% !important;
        right: auto !important;
        bottom: auto !important;
        transform: translate(-50%, -50%) !important;
        width: 95% !important;
        max-width: 300px !important;
        margin: 0 !important;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }
    .daterangepicker .drp-calendar {
        width: 48% !important;
        max-width: 48% !important;
        margin: 0 1% !important;
        font-size: 11px !important;
    }
    .daterangepicker .drp-calendar.right {
        display: block !important;
    }
    .daterangepicker td,
    .daterangepicker th {
        width: 25px !important;
        height: 25px !important;
        min-width: 25px !important;
        line-height: 25px !important;
        font-size: 10px !important;
        padding: 0 !important;
    }
    .daterangepicker .drp-calendar.left,
    .daterangepicker .drp-calendar.right {
        padding: 5px !important;
        border-right: none !important;
    }
    .daterangepicker th.month {
        font-size: 12px !important;
        padding: 0 !important;
    }
    .daterangepicker .prev,
    .daterangepicker .next {
        width: 20px !important;
        height: 20px !important;
        line-height: 20px !important;
        font-size: 12px !important;
    }
}
</style>