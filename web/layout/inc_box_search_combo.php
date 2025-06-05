<div class="box_search">
	<div class="tour-search-form-home">
		<div class="search-form">
			<form action="<?=$search_action_form?>" class="form form_search_auto" method="get" id="form_search" onsubmit="return search_main(this);">
				<div class="row">
					<div class="col_search col_search_text">
						<div class="form-group dropdown has-icon dropdown_search_auto" id="dropdown_search">
							<i class="input-icon field-icon fal fa-search"></i>
							<div id="dropdown-destination">
								<label>Tìm combo tiết kiệm</label>
                                <div class="render">
                                    <input type="text" autocomplete="off" class="input_search form_input_text" value="<?=$search_keyword?>" data-position="combo" placeholder="Bạn đang muốn đi đâu?" />
                                    <span class="del_text">&#10005;</span>
                                </div>
                            </div>
						</div>
					</div>
					<div class="col_search col_search_button">
						<div class="form-button form-group">
							<button class="btn btn-search" type="submit">Tìm</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>