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
                        <i class="fas fa-search"></i>
                    </div>
                </div>
                <div class="indicator"></div>
            </div>
        </div>
    </div>
</div>