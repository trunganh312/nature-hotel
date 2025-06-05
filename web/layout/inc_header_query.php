<nav id="st-main-menu">
    <div class="top_sub_mb">
        <a href="<?=DOMAIN_WEB?>" class="back-menu"><i class="fas fa-chevron-left"></i></a>
        <a class="mb_link_home" href="<?=DOMAIN_WEB?>"><img src="<?=$cfg_path_image?>logo/logo-h.png" alt="<?=BRAND_DOMAIN?>"></a>
    </div>
    <ul id="main-menu" class="menu main-menu">
        <li id="menu-item-home" class="menu-item menu-item-home page_item current_page_item">
            <a href="<?=DOMAIN_WEB . param_box_web(96, true)?>">Trang chủ</a>
        </li>
        <li id="menu-item-hotel" class="menu-item menu-item-has-children has-mega-menu">
            <a href="javascript:;">Khách sạn <i class="fas fa-chevron-down"></i></a>
            <ul class="sub-menu mega-menu">
                <div class="dropdown-menu-inner">
                    <div class="vc_row wpb_row st bg-holder">
                        <div class="container">
                            <div class="row">
                                <div class="st-mega wpb_column column_container col-md-10">
                                    <div class="vc_column-inner wpb_wrapper">
                                        <div class="vc_row wpb_row vc_inner panel_menu sub_menu_link">
                                            <?
                                            /** Lấy ra các khu vực của VN để lấy các tỉnh/TP chia theo từng block hiển thị list tour/destination **/
                                            $cities =   $DB->query("SELECT are_id, are_name, cit_id, cit_name, cit_area
                                                                    FROM cities
                                                                    INNER JOIN area ON (cit_area = are_id)
                                                                    WHERE cit_show_menu = 1 AND cit_active = 1
                                                                    ORDER BY are_order, cit_order, cit_name")
                                                                    ->toArray();
                                            //Dùng 1 biến đánh dấu ID của area để tách thành các block (nhằm giảm phải query trong vòng for)
                                            $area_id    =   0;
                                            $stt        =   0;
                                            foreach ($cities as $row) {
                                                if ($row['cit_area'] != $area_id) {

                                                    //Nếu số item lẻ thì sinh thêm 1 item rỗng để có border nhìn cho ko bị lệch trên PC
                                                    if ($stt % 2 != 0) {
                                                        echo    '<li class="menu-item empty_item">&nbsp;</li>';
                                                    }
                                                    //Reset lại STT để bắt đầu 1 block mới
                                                    $stt    =   0;

                                                    //Nếu Area ID bị thay đổi thì phải đóng block trước đó lại
                                                    if ($area_id > 0) {
                                                        echo    '</ul>
                                                                </div>
                                                                </div>
                                                            </div>
                                                        </div>';
                                                    }
                                                    //Gán lại Area ID để tách block sau
                                                    $area_id    =   $row['cit_area'];
                                                    
                                                    //Mở block đầu tiên
                                                    echo    '<div class="wpb_column column_container col_t_' . $area_id . '">
                                                            <div class="vc_column-inner">
                                                                <div class="wpb_wrapper">
                                                                    <div class="wpb_text_column wpb_content_element menu_top_title" >
                                                                        <div class="wpb_wrapper">
                                                                            <p><strong>' . $row['are_name'] . '</strong><i class="fal fa-plus"></i></p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="widget widget_nav_menu">
                                                                        <ul class="menu">';
                                                }
                                                
                                                //Nếu city đó có Quận/Huyện Hot thì sẽ lấy ra Quận/Huyện để show chứ ko show tên Tỉnh/TP. VD như Lào Cai thì show Sapa chứ show Lào Cai ko ai quan tâm.
                                                $districts  =   $DB->query("SELECT dis_id, dis_name, dis_name_show
                                                                            FROM district
                                                                            WHERE dis_city = " . $row['cit_id'] . " AND dis_hot = 1
                                                                            ORDER BY dis_order")
                                                                            ->toArray();
                                                if (!empty($districts)) {
                                                    foreach ($districts as $d) {
                                                        $stt++;
                                                        echo    '<li class="menu-item">
                                                                    <a href="' . $Router->listHotelDistrict($d, param_box_web(4)) . '">' . $d['dis_name_show'] . '</a>
                                                                </li>';
                                                    }
                                                } else {
                                                    $stt++;
                                                    echo    '<li class="menu-item">
                                                                <a href="' . $Router->listHotelCity($row, param_box_web(4)) . '">' . $row['cit_name'] . '</a>
                                                            </li>';
                                                    
                                                }
                                            }
                                            //Đóng block cuối
                                            echo    '</ul>
                                                        </div>
                                                        </div>
                                                    </div>
                                                </div>';
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </ul>
        </li>
    </ul>
</nav>