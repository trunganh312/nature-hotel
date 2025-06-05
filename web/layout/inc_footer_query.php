<div class="row list_area_city">
    <h4><?=(!empty($page_module) && $page_module == 'tour' ? 'Xem các tour du lịch tại' : 'Xem khách sạn tại')?>:</h4>
    <div class="wpb_column column_container col-md-2 col-sm-4 col-xs-6">
        <div class="vc_column-inner wpb_wrapper">
            <div class="vc_wp_custommenu">
                <div class="widget widget_nav_menu">
                    <ul>
                        <?
                        $stt    =   0;
                        $cities =   $DB->query("SELECT cit_id, cit_name FROM city WHERE cit_priority = 1 AND cit_active = 1")->toArray();
                        foreach ($cities as $row) {
                            //Nếu city đó có Quận/Huyện Hot thì sẽ lấy ra Quận/Huyện để show chứ ko show tên Tỉnh/TP. VD như Lào Cai thì show Sapa chứ show Lào Cai ko ai quan tâm.
                            $districts  =   $DB->query("SELECT dis_id, dis_name, dis_name_show
                                                        FROM district
                                                        WHERE dis_city = " . $row['cit_id'] . " AND dis_hot = 1
                                                        ORDER BY dis_order")
                                                        ->toArray();
                            if (!empty($districts)) {
                                foreach ($districts as $d) {
                                    $url    =   $Router->listHotelDistrict($d, param_box_web(10));
                                    $text   =   'Khách sạn ' . $d['dis_name_show'];
                                    if (!empty($page_module) && $page_module == 'tour') {
                                        $url    =   $Router->listTourDistrict($d, param_box_web(10));
                                        $text   =   'Tour du lịch ' . $d['dis_name_show'];
                                    }
                                    $stt++;
                                    echo    '<li>
                                                <a href="' . $url . '">' . $text . '</a>
                                            </li>';
                                }
                            } else {
                                $url    =   $Router->listHotelCity($row, param_box_web(10));
                                $text   =   'Khách sạn ' . $row['cit_name'];
                                if (!empty($page_module) && $page_module == 'tour') {
                                    $url    =   $Router->listTourCity($row, param_box_web(10));
                                    $text   =   'Tour du lịch ' . $row['cit_name'];
                                }
                                $stt++;
                                echo    '<li>
                                            <a href="' . $url . '">' . $text . '</a>
                                        </li>';
                                
                            }
                            
                            /** Ngắt các block **/
                            if ($stt % 5 == 0 && $stt < 30) {
                                echo    '</ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="wpb_column column_container col-md-2 col-sm-4 col-xs-6">
                            <div class="vc_column-inner wpb_wrapper">
                                <div class="vc_wp_custommenu">
                                    <div class="widget widget_nav_menu">
                                        <ul>';
                            }
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>