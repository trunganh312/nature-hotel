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
        <h2>Khách sạn</h2>
        <div class="baler-info">
            <div class="row search-place">
                <div class="col-md-4">
                    <div class="place">
                        <label for="locationInput">Địa điểm</label>
                        <input type="text" class="search-input ui-widget" id="locationInput"
                            placeholder="Thành phố" value="">
                        <input type="hidden" id="selectedLocationId" name="locationId">
                        <div class="dropdown-panel" id="dropdownPanel">
                            <div class="box row">
                                <div class="location_box_left col-md-7">
                                    <div class="recent-title">
                                        <span>Địa điểm nổi bật</span>
                                    </div>
                                    <div class="popular-destinations">
                                        <?php if (!empty($data_city)): ?>
                                            <?php foreach ($data_city as $city): ?>
                                                <div class="destination" data-link="<?= htmlspecialchars($city['link']) ?>" data-city-name="<?= htmlspecialchars($city['name']) ?>">
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
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="date">
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
                        <input type="text" name="datetimes" style="opacity: 0; position: absolute; width: 100%;" />
                    </div>
                </div>
                 <div class="col-md-4" id="select_op" style=" max-width: 250px; margin-right: 20px;">
                    <div class="room-selector">
                        <h6>Số phòng, số khách</h6>
                        <div class="selected-option" id="selectedOption"><?= htmlspecialchars($search_room_qty) ?> phòng, <?= htmlspecialchars($search_adult_qty) ?> người lớn, <?= htmlspecialchars($search_child_qty) ?> trẻ em</div>
                        <input type="hidden" id="roomQty" name="roomQty" value="<?= htmlspecialchars($search_room_qty) ?>">
                        <input type="hidden" id="adultQty" name="adultQty" value="<?= htmlspecialchars($search_adult_qty) ?>">
                        <input type="hidden" id="childQty" name="childQty" value="<?= htmlspecialchars($search_child_qty) ?>">
                        <div class="dropdown-menu">
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
                    <div class="btnSreach">
                        <i class="fas fa-search"></i>
                    </div>
                </div>
                <div class="indicator"></div>
            </div>
        </div>
    </div>
</div>