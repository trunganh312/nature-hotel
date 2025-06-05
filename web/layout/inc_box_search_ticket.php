<div class="box_search search_single">
	<div class="tour-search-form-home">
		<div class="search-form">
			<form action="<?=$search_action_form?>" class="form form_search_auto" method="get" id="form_search_ticket" onsubmit="return search_main(this);">
				<div class="row">
					<div class="col_search col_search_text">
						<div class="form-group dropdown has-icon dropdown_search_auto" id="dropdown_search">
							<i class="input-icon field-icon fal fa-search"></i>
							<div id="dropdown-destination">
								<label>Tìm Vé máy bay, Vé cáp treo, Vé thăm quan...</label>
                                <div class="render">
                                    <input type="text" autocomplete="off" class="input_search form_input_text" value="<?=$search_keyword?>" data-position="ticket" placeholder="Bạn đang muốn đi đâu?" />
                                    <span class="del_text"><i class="fal fa-times"></i></span>
                                </div>
                            </div>
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