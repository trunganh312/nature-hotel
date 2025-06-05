<div class="box_search">
	<div class="tour-search-form-home">
		<div class="search-form">
			<form action="<?=$search_action_form?>" class="form form_search_auto" method="get" id="form_search_hotel" onsubmit="return search_main(this);">
				<div class="row">
					<div class="col_search col_search_text">
						<div class="form-group dropdown has-icon dropdown_search_auto" id="dropdown_search">
							<i class="input-icon field-icon fal fa-search"></i>
							<div id="dropdown-destination">
								<label>Tìm tên Khách sạn, Tỉnh/TP, Địa danh...</label>
                                <div class="render">
                                    <input type="text" autocomplete="off" class="form_input_text input_search" value="<?=$search_keyword?>" data-position="hotel" placeholder="Bạn đang muốn đi đâu?" />
                                    <span class="del_text"><i class="fal fa-times"></i></span>
                                </div>
                                <span class="change_date_mb event_search_change_date">Đổi ngày<i class="fal fa-angle-down"></i></span>
                            </div>
						</div>
                        <p class="show_mb find_near_by"><a href="<?=$cfg_path_hotel_near?>"><i class="far fa-location"></i> Tìm khách sạn ở gần đây</a></p>
					</div>
					<div class="col_search col_search_calendar">
                        <div class="form-group form-date-field form-date-search clearfix has-icon" data-format="DD/MM/YYYY">
						    <i class="input-icon field-icon fal fa-calendar-alt"></i>    
						    <div class="date-wrapper clearfix">
	                            <div class="check-in-wrapper">
	                                <label>Từ ngày - Đến ngày</label>
	                                <input type="text" inputmode="none" class="vietgoing-check-in-out form_input_text" value="<?=$cfg_date_checkin .' - '. $cfg_date_checkout ?>" />
                                    <span class="total_night">(<span><?=$cfg_total_night?></span> đêm)</span>
	                            </div>
	                        </div>
	                        <input type="hidden" name="checkin" value="<?=$cfg_date_checkin?>" />
	                        <input type="hidden" name="checkout" value="<?=$cfg_date_checkout ?>" />
						</div>
					</div>
					<div class="col_search col_search_button">
						<div class="form-button form-group">
							<button class="btn btn-search" type="submit">Tìm</button>
                            <input type="hidden" class="input_box_web" name="" />
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>