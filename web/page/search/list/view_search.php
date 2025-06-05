<div id="st-content-wrapper" class="search-result-page">
    <div class="container">
        <div class="box_search_detail search_single suggest_search" data-box="130">
            <div class="box_search">
            	<div class="tour-search-form-home">
            		<div class="search-form">
            			<form action="<?=$search_action_form?>" class="form form_search_auto" method="get" id="form_search" onsubmit="return search_main(this);">
            				<div class="row">
            					<div class="col_search col_search_text">
            						<div class="form-group dropdown has-icon dropdown_search_auto" id="dropdown_search">
            							<i class="input-icon field-icon fal fa-search"></i>
            							<div id="dropdown-destination">
            								<label>Tìm kiếm theo từ khóa</label>
                                            <div class="render">
                                                <input type="text" autocomplete="off" name="q" class="input_search form_input_text" value="<?=$keyword?>" data-position="hotel" placeholder="Bạn đang muốn đi đâu?" />
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
        </div>
        <div class="st-hotel-result">
            <div class="title_search_result">
                <h1 class="">Kết quả tìm kiếm: <?=$keyword?></h1>
            </div>
            <?
            if (mb_strlen($keyword) >= 2) {
                $total_result   =   0;
                $keyword    =   $search_data['keyword'];    //Keyword đã clean dùng để search
                
                /** Tìm tên Tỉnh/TP, Quận/Huyện **/
                $data_city  =   $DB->query("SELECT cit_id, cit_name, " . $search_data['cit_search_data']['diem'] . "
                                            FROM city
                                            WHERE cit_active = 1 " . $search_data['cit_search_data']['where'] . "
                                            ORDER BY diem DESC
                                            LIMIT 10")
                                            ->toArray();
                
                $data_district  =   $DB->query("SELECT dis_id, dis_name, dis_name_show, " . $search_data['dis_search_data']['diem'] . "
                                                FROM district
                                                WHERE dis_active = 1 " . $search_data['dis_search_data']['where'] . "
                                                ORDER BY diem DESC, dis_hot DESC
                                                LIMIT 10")
                                                ->toArray();
                
                if (!empty($data_city) || !empty($data_district)) {
                    ?>
                    <div class="box_search_result">
                        <h2 class="link_list_parent">Điểm đến:</h2>
                        <?
                        //KS
                        $i  =   0;
                        $arr_special    =   ['Resort', 'Homestay'];
                        foreach ($data_city as $row) {
                            $i++;
                            echo    '<h3 class="link_list_parent"' . ($i == 1 ? ' style="margin-top: 0"' : '') . '>' . $row['cit_name'] . ':</h3>';
                            
                            //Nếu trong cụm keyword search có những từ kiểu Homestay, Resort... thì ưu tiên show list đó lên trước
                            foreach ($arr_special as $k) {
                                if (stripos($keyword, $k) !== false) {
                                    echo    '<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                &bull; <a href="' . $Router->listHotelCity($row, array_merge(['type' => $k], param_box_web(112))) . '" title="Xem các ' . $k . ' ở ' . $row['cit_name'] . '">' . $k . ' ở ' . $row['cit_name'] . '</a>
                                            </div>';
                                }
                            }
                            
                            echo    '<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                        &bull; <a href="' . $Router->listHotelCity($row, param_box_web(112)) . '" title="Xem các khách sạn ở ' . $row['cit_name'] . '">Khách sạn ở ' . $row['cit_name'] . '</a>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                        &bull; <a href="' . $Router->listTourCity($row, param_box_web(112)) . '" title="Xem các tour du lịch ' . $row['cit_name'] . '">Tour du lịch ' . $row['cit_name'] . '</a>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                        &bull; <a href="' . $Router->listComboCity($row, param_box_web(112)) . '" title="Xem các combo du lịch tiết kiệm ở ' . $row['cit_name'] . '">Combo du lịch tiết kiệm ở ' . $row['cit_name'] . '</a>
                                    </div>';
                            $total_result++;
                        }
                        foreach ($data_district as $row) {
                            echo    '<h3 class="link_list_parent">' . $row['dis_name'] . ':</h3>';
                            
                            //Nếu trong cụm keyword search có những từ kiểu Homestay, Resort... thì ưu tiên show list đó lên trước
                            foreach ($arr_special as $k) {
                                if (stripos($keyword, $k) !== false) {
                                    echo    '<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                                &bull; <a href="' . $Router->listHotelDistrict($row, array_merge(['type' => $k], param_box_web(112))) . '" title="Xem các ' . $k . ' ở ' . $row['dis_name'] . '">' . $k . ' ở ' . $row['dis_name'] . '</a>
                                            </div>';
                                }
                            }
                            echo    '<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                        &bull; <a href="' . $Router->listHotelDistrict($row, param_box_web(112)) . '" title="Xem các khách sạn ở ' . $row['dis_name'] . '">Khách sạn ở ' . $row['dis_name'] . '</a>
                                    </div>
                                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
                                        &bull; <a href="' . $Router->listTourDistrict($row, param_box_web(112)) . '" title="Xem các tour du lịch ' . $row['dis_name'] . '">Tour du lịch ' . $row['dis_name'] . '</a>
                                    </div>';
                            $total_result++;
                        }
                        ?>
                    </div>
                    <?
                }
                
                /** Tìm các KS **/
                $data   =   $DB->query("SELECT hot_id, hot_name, " . $search_data['hot_data_search']['diem'] . "
                                        FROM hotel
                                        WHERE hot_active = 1 " . $search_data['hot_data_search']['where'] . "
                                        ORDER BY diem DESC, hot_top DESC, hot_hot DESC
                                        LIMIT 20")
                                        ->toArray();
                if (!empty($data)) {
                    ?>
                    <div class="box_search_result">
                        <h2 class="link_list_parent">Khách sạn:</h2>
                        <?
                        //KS
                        foreach ($data as $row) {
                            echo    '<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">&bull; <a href="' . $Router->detailHotel($row, param_box_web(112)) . '" title="Xem thông tin ' . $row['hot_name'] . '">' . $row['hot_name'] . '</a></div>';
                            $total_result++;
                        }
                        ?>
                    </div>
                    <?
                }
                
                /** Tìm các tour **/
                $data   =   $DB->query("SELECT tou_id, tou_name, tou_group, " . $search_data['tou_search_data']['diem'] . "
                                        FROM tour
                                        WHERE tou_active = 1 " . $search_data['tou_search_data']['where'] . "
                                        ORDER BY diem DESC, tou_top DESC, tou_hot DESC
                                        LIMIT 10")
                                        ->toArray();
                //Tách ra thành box combo tiết kiệm riêng để tránh query 2 lần
                $arr_combo  =   [];
                if (!empty($data)) {
                    ?>
                    <div class="box_search_result">
                        <h2 class="link_list_parent">Tour du lịch:</h2>
                        <?
                        //KS
                        foreach ($data as $row) {
                            if ($row['tou_group'] == CATE_TOUR_COMBO) {
                                $arr_combo[]    =   $row;
                                continue;
                            }
                            echo    '<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">&bull; <a href="' . $Router->detailTour($row, param_box_web(112)) . '" title="Xem thông tin ' . $row['tou_name'] . '">' . $row['tou_name'] . '</a></div>';
                            $total_result++;
                        }
                        ?>
                    </div>
                    <?
                }
                
                /** Show các combo các combo **/
                if (!empty($arr_combo)) {
                    ?>
                    <div class="box_search_result">
                        <h2 class="link_list_parent">Combo tiết kiệm:</h2>
                        <?
                        //KS
                        foreach ($arr_combo as $row) {
                            echo    '<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">&bull; <a href="' . $Router->detailTour($row, param_box_web(112)) . '" title="Xem thông tin ' . $row['tou_name'] . '">' . $row['tou_name'] . '</a></div>';
                            $total_result++;
                        }
                        ?>
                    </div>
                    <?
                }
                
                /** Tìm các vé **/
                $data   =   $DB->query("SELECT tic_id, tic_name, " . $search_data['tic_search_data']['diem'] . "
                                        FROM ticket
                                        WHERE tic_active = 1 " . $search_data['tic_search_data']['where'] . "
                                        ORDER BY diem DESC, tic_top DESC, tic_hot DESC
                                        LIMIT 10")
                                        ->toArray();
                if (!empty($data)) {
                    ?>
                    <div class="box_search_result">
                        <h2 class="link_list_parent">Vé dịch vụ:</h2>
                        <?
                        //KS
                        foreach ($data as $row) {
                            echo    '<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">&bull; <a href="' . $Router->detailTicket($row, param_box_web(112)) . '" title="Xem thông tin ' . $row['tic_name'] . '">' . $row['tic_name'] . '</a></div>';
                            $total_result++;
                        }
                        ?>
                    </div>
                    <?
                }
                
                /** Tìm địa danh du lịch **/
                $data   =   $DB->query("SELECT des_id, des_name, " . $search_data['des_search_data']['diem'] . "
                                        FROM destination
                                        WHERE des_active = 1 " . $search_data['des_search_data']['where'] . "
                                        ORDER BY diem DESC, des_count_view DESC
                                        LIMIT 10")
                                        ->toArray();
                if (!empty($data)) {
                    ?>
                    <div class="box_search_result">
                        <h2 class="link_list_parent">Địa danh du lịch:</h2>
                        <?
                        foreach ($data as $row) {
                            echo    '<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">&bull; <a href="' . $Router->detailDestination($row, param_box_web(112)) . '" title="Xem thông tin ' . $row['des_name'] . '">' . $row['des_name'] . '</a></div>
                                    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">&bull; <a href="' . $Router->listTourDestination($row, param_box_web(112)) . '" title="Xem các tour du lịch đi ' . $row['des_name'] . '">Tour du lịch ' . $row['des_name'] . '</a></div>';
                            $total_result++;
                        }
                        ?>
                    </div>
                    <?
                }
                
                /** Tìm bài viết **/
                $data   =   $DB->query("SELECT art_id, art_title, " . $search_data['art_search_data']['diem'] . "
                                        FROM article
                                        WHERE art_active = 1 " . $search_data['art_search_data']['where'] . "
                                        ORDER BY diem DESC, art_count_view DESC
                                        LIMIT 10")
                                        ->toArray();
                if (!empty($data)) {
                    ?>
                    <div class="box_search_result">
                        <h2 class="link_list_parent">Bài viết:</h2>
                        <?
                        //KS
                        foreach ($data as $row) {
                            echo    '<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">&bull; <a href="' . $Router->detailArticle($row, param_box_web(112)) . '" title="Xem thông tin ' . $row['art_title'] . '">' . $row['art_title'] . '</a></div>';
                            $total_result++;
                        }
                        ?>
                    </div>
                    <?
                }
                
                //Nếu ko có kết quả nào
                if ($total_result == 0) {
                    ?>
                    <div class="box_search_result">
                        <h3>Không có kết quả nào phù hợp, vui lòng thay đổi từ khóa tìm kiếm để có kết quả khác!</h3>
                    </div>
                    <?
                }
                
            } else {
                ?>
                <div class="box_search_result">
                    <h3>Từ khóa phải có tối thiểu 2 chữ cái trở lên! Vui lòng thử lại!</h3>
                </div>
                <?
            }
            ?>
        </div>
    </div>
</div>