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
        margin-left: 10px;
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
        /* position: relative; */
        cursor: pointer;
    }

    .selected-option {
        max-width: 16px;
        white-space: nowrap;
    }

    .room-selector .dropdown-menu {
        display: none;
        position: absolute;
        top: 55px;
        left: 0;
        width: 300px;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        margin-top: 5px;
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

    .dropdown-menu.show {
        display: block;
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
    .daterangepicker {
        position: absolute !important;
        /* left: 37.5% !important; */
        width: 800px !important; /* Tăng chiều rộng để chứa 2 lịch */
        border: none !important; /* Bỏ viền mặc định */
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important; /* Thêm bóng */
        border-radius: 8px !important; /* Bo góc */
        /* top: 108% !important; */
    }
    .daterangepicker .drp-calendar {
        width: 400px !important;
        max-width: 400px !important; /* Tăng từ 270px lên */
    }
    .daterangepicker .calendar-table th,
    .daterangepicker .calendar-table td {
        min-width: 40px !important;
        width: 40px !important;
        height: 40px !important;
        line-height: 40px !important;
        font-size: 14px !important;
    }
    .daterangepicker .drp-calendar.left,
    .daterangepicker .drp-calendar.right {
        padding: 15px !important;
    }
    /* CSS cho daterangepicker để sử dụng biến màu từ root.css */
    .daterangepicker td.active, .daterangepicker td.active:hover {
        background-color: var(--secondary-color) !important;
        border-color: transparent !important;
        color: var(--white) !important;
    }
    
    /* .daterangepicker td.in-range {
        background-color: var(--accent-color-hover) !important;
        color: var(--black) !important;
    } */
    
    .daterangepicker td.available:hover, .daterangepicker th.available:hover {
        background-color: var(--secondary-color) !important;
        color: var(--white) !important;
    }

    
    .daterangepicker th.month {
        color: var(--primary-color) !important;
        font-weight: bold !important;
        font-size: 18px !important;
    }
    
    .daterangepicker .calendar-table .next span, 
    .daterangepicker .calendar-table .prev span {
        border-color: var(--primary-color) !important;
    } 
</style>

<div class="baler-box">
    <div class="container">
        <h2>Khách sạn</h2>
        <div class="baler-info">
            <div class="row search-place">
                <div class="col-md-4" style="border-right: 1px solid #e2e8f0">
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
                                        <div class="destination" data-city-id="1" data-city-name="Hà Nội">
                                            <img src="https://img.tripi.vn/cdn-cgi/image/width=640,height=640/https://gcs.tripi.vn/tripi-assets/mytour/images/locations/hanoi.png"
                                                alt="Hà Nội">
                                            <div class="destination-name">Hà Nội</div>
                                        </div>
                                        <div class="destination" data-city-id="2" data-city-name="Đà Nẵng">
                                            <img src="https://img.tripi.vn/cdn-cgi/image/width=640,height=640/https://gcs.tripi.vn/tripi-assets/mytour/images/locations/danang.png"
                                                alt="Đà Nẵng">
                                            <div class="destination-name">Đà Nẵng</div>
                                        </div>
                                        <div class="destination" data-city-id="3" data-city-name="Đà Lạt">
                                            <img src="https://img.tripi.vn/cdn-cgi/image/width=640,height=640/https://gcs.tripi.vn/tripi-assets/mytour/images/locations/da-lat.png"
                                                alt="Đà Lạt">
                                            <div class="destination-name">Đà Lạt</div>
                                        </div>
                                        <div class="destination" data-city-id="4" data-city-name="Phú Quốc">
                                            <img src="https://img.tripi.vn/cdn-cgi/image/width=640,height=640/https://gcs.tripi.vn/tripi-assets/mytour/images/locations/phu-quoc.png"
                                                alt="Phú Quốc">
                                            <div class="destination-name">Phú Quốc</div>
                                        </div>
                                        <div class="destination" data-city-id="5" data-city-name="Hội An">
                                            <img src="https://img.tripi.vn/cdn-cgi/image/width=640,height=640/https://gcs.tripi.vn/tripi-assets/mytour/images/locations/hoi-an.png"
                                                alt="Hội An">
                                            <div class="destination-name">Hội An</div>
                                        </div>
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
                            <div id="startDateText"><?php echo date('d/m/Y'); ?></div>
                        </div>
                        <div class="moon col-md-2">
                            <i class="fa-regular fa-moon-stars"></i>
                        </div>
                        <div class="return-date col-md-5 ms-4">
                            <h6>Ngày về</h6>
                            <div id="endDateText"><?php echo date('d/m/Y', strtotime('+1 day')); ?></div>
                        </div>
                        <input type="text" name="datetimes" style="opacity: 0; position: absolute; width: 100%;" />
                    </div>
                </div>
                <div class="col-md-4" id="select_op" style="border-left: 1px solid #e2e8f0; max-width: 250px">
                    <div class="room-selector">
                        <h6>Số phòng, số khách</h6>
                        <div class="selected-option" id="selectedOption">1 phòng, 2 người lớn, 0 trẻ em</div>
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
                <div class="col-md-1">
                    <div class="btnSreach">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                </div>
                <div class="indicator"></div>
            </div>
        </div>
    </div>
</div>