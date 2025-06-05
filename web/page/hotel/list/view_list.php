<div id="st-content-wrapper" class="search-result-page st-tours st-hotels">
    <div class="container">
        <div class="st-breadcrumb">
            <?=generate_navbar($arr_breadcrum)?>
        </div>
    </div>
    <div class="container">
        <div class="box_search_detail search_full search_hide_mb suggest_search" data-box="118">
            <?
            include('../../../layout/inc_box_search_hotel.php');
            ?>
        </div>
        <div class="st-hotel-result">
            <?
            if ($search_info['item_active'] == 1) {
                ?>
                <div class="row">
                    <div class="col-lg-3 col-md-3 sidebar-filter">
                        <div class="sidebar-item map-view-wrapper hidden-xs hidden-sm">
                            <div class="map-view event_hotel_list_open_map_pc" data-mode-view="<?=$mapview_type ?>" data-id="<?=$mapview_id ?>" data-lat="<?=array_get($location, 'lat') ?>" data-lng="<?=array_get($location, 'lng') ?>">
                                <i class="input-icon field-icon fas fa-map-marker-alt" aria-hidden="true"></i>
                                Xem trên bản đồ           
                            </div>
                        </div>
                        <div class="sidebar-item-wrapper">
                            <h3 class="sidebar-title">Lọc Khách sạn <span class="hidden-lg hidden-md close-filter"><i class="input-icon field-icon fas fa-times"></i></span></h3>
                            <div class="sidebar-item range-slider" style="padding-top: 0;">
                                <form action="<?=$url_canonical?>" method="GET">
                                    <div class="item-title">
                                        <h4>Giá</h4>
                                    </div>
                                    <div class="item-content">
                                        <input type="text" class="price_range" name="price_range" value="<?=$price_range ?>" data-min="0" data-max="<?=MAX_PRICE_TOUR?>" data-step="500000" data-postfix="₫"/>
                                        <div class="form-button text-center">
                                            <button class="btn btn-primary btn-search event_hotel_list_filter_price" type="submit">Cập nhật</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="sidebar-item st-icheck review-score">
                                <div class="item-title">
                                    <h4>Hạng sao</h4>
                                </div>
                                <div class="item-content">
                                    <ul>
                                        <?
                                        for ($i = 5; $i >= 1; $i--) {
                                            if (!in_array($i, $arr_distinct['star']))  continue;
                                            ?>
                                            <li class="st-icheck-item">
                                                <a href="<?=generate_url_filter(['star' => $i, PARAM_WEB => 25], ['group', 'id'])?>" class="<?=($filter_star == $i ? 'active' : '')?>">
                                                    <?=gen_star($i)?>
                                                    <span class="checkmark fcheckbox"></span>
                                                </a>
                                            </li>
                                            <?
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="sidebar-item st-icheck review-score">
                                <div class="item-title">
                                    <h4>Loại hình</h4>
                                </div>
                                <div class="item-content">
                                    <ul>
                                        <?
                                        foreach ($cfg_hotel_type as $id => $name) {
                                            if (!in_array($id, $arr_distinct['type']))  continue;
                                            ?>
                                            <li class="st-icheck-item">
                                                <a href="<?=generate_url_filter(['type' => $name, PARAM_WEB => 25], ['group', 'id']) ?>" class="<?=($type_id == $id ? 'active' : '')?>">
                                                    <?=$name ?>
                                                    <span class="checkmark fcheckbox"></span>
                                                </a>
                                            </li>
                                            <?
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="sidebar-item pag st-icheck filter_no_icon">
                                <div class="item-title">
                                    <h4>Quận/Huyện</h4>
                                </div>
                                <div class="item-content">
                                    <ul>
                                        <?
                                        $i  =   0;
                                        foreach ($arr_district_filter as $row) {
                                            $i++;
                                            ?>
                                            <li class="st-icheck-item">
                                                <a href="<?=$row['link']?>" title="Xem các khách sạn ở <?=$row['text']?>" <?=(!empty($search_info['dis_id']) && $search_info['dis_id'] == $row['id'] ? 'class="bold"' : '')?>><?=$row['text']?></a>
                                            </li>
                                            <?
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                            <?
                            foreach ($list_attribute as $atn => $attribute) {
                                if (empty($attribute['data']))  continue;
                                ?>
                                <div class="sidebar-item pag st-icheck">
                                    <div class="item-title has_toggle">
                                        <h4><?=$attribute['info']['name']?></h4>
                                        <i class="fas fa-chevron-up event_toggle_filter_attribute" aria-hidden="true"></i>
                                    </div>
                                    <div class="item-content">
                                        <ul>
                                            <?
                                            $i  =   0;
                                            foreach ($attribute['data'] as $atv_id => $value) {
                                                $i++;
                                                ?>
                                                <li class="st-icheck-item">
                                                    <a href="<?=generate_url_filter([$attribute['info']['alias'] => ($attribute['info']['type'] == ATTRIBUTE_SELECT ? $value['name'] : [$value['name']]), PARAM_WEB => 25], ['group', 'id'])?>" class="<?=(isset($attribute_selected[$atn]) && in_array($atv_id, $attribute_selected[$atn]) ? 'active' : '')?>">
                                                        <?=$value['name']?>
                                                        <span class="checkmark fcheckbox"></span>
                                                    </a>
                                                </li>
                                                <?
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                                <?
                            }
                            ?>
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-9">
                        <div class="toolbar">
                            <ul class="toolbar-action hidden-xs">
                                <li>
                                    <div class="form-extra-field dropdown ">
                                        <button class="btn btn-link dropdown" type="button" id="dropdownMenuSort" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <?=$sort_title?> <i class="fa fa-chevron-down arrow"></i>
                                        </button>
                                        <div class="dropdown-menu sort-menu" aria-labelledby="dropdownMenuSort">
                                            <div class="sort-title">
                                                <h3>Sắp xếp theo<span class="hidden-lg hidden-md hidden-sm close-filter"><i class="fa fa-times" aria-hidden="true"></i></span></h3>
                                            </div>
                                            <div class="sort-item st-icheck">
                                                <span class="title">Giá</span>
                                                <div class="st-icheck-item"><a href="<?=generate_url_filter(['sort' => 'price-asc', PARAM_WEB => 34], ['sort', 'group', 'id'])?>" class="<?=($sort_type == 'price-asc' ? 'active' : '')?>">Thấp - Cao<span class="checkmark"></span></a></div>
                                                <div class="st-icheck-item"><a href="<?=generate_url_filter(['sort' => 'price-desc', PARAM_WEB => 34], ['sort', 'group', 'id'])?>" class="<?=($sort_type == 'price-desc' ? 'active' : '')?>">Cao - Thấp<span class="checkmark"></span></a></div>
                                            </div>
                                            <div class="sort-item st-icheck">
                                                <span class="title">Lựa chọn</span>
                                                <div class="st-icheck-item"><a href="<?=generate_url_filter(['sort' => 'review', PARAM_WEB => 34], ['sort', 'group', 'id'])?>" class="<?=($sort_type == 'review' ? 'active' : '')?>">Đánh giá cao<span class="checkmark"></span></a></div>
                                                <div class="st-icheck-item"><a href="<?=generate_url_filter(['sort' => 'favorite', PARAM_WEB => 34], ['sort', 'group', 'id'])?>" class="<?=($sort_type == 'favorite' ? 'active' : '')?>">Được đặt nhiều<span class="checkmark"></span></a></div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <div class="dropdown-menu sort-menu sort-menu-mobile">
                                <div class="sort-title">
                                    <h3><?=$sort_title?> <span class="hidden-lg hidden-md close-filter"><i class="input-icon field-icon fas fa-times"></i></span></h3>
                                </div>
                                <div class="sort-item st-icheck">
                                    <span class="title">Giá</span>
                                    <div class="st-icheck-item"><a href="<?=generate_url_filter(['sort' => 'price-asc', PARAM_WEB => 34], ['sort', 'group', 'id'])?>" class="<?=($sort_type == 'price-asc' ? 'active' : '')?>">Thấp - cao<span class="checkmark"></span></a></div>
                                    <div class="st-icheck-item"><a href="<?=generate_url_filter(['sort' => 'price-desc', PARAM_WEB => 34], ['sort', 'group', 'id'])?>" class="<?=($sort_type == 'price-desc' ? 'active' : '')?>">Cao - thấp<span class="checkmark"></span></a></div>
                                </div>
                                <div class="sort-item st-icheck">
                                    <span class="title">Lựa chọn</span>
                                    <div class="st-icheck-item"><a href="<?=generate_url_filter(['sort' => 'review', PARAM_WEB => 34], ['sort', 'group', 'id'])?>" class="<?=($sort_type == 'review' ? 'active' : '')?>">Đánh giá cao<span class="checkmark"></span></a></div>
                                    <div class="st-icheck-item"><a href="<?=generate_url_filter(['sort' => 'favorite', PARAM_WEB => 34], ['sort', 'group', 'id'])?>" class="<?=($sort_type == 'favorite' ? 'active' : '')?>">Được đặt nhiều<span class="checkmark"></span></a></div>
                                </div>
                            </div>
                            <ul class="toolbar-action-mobile hidden-lg hidden-md">
                                <li><a href="#" class="btn btn-primary map-view event_hotel_list_open_map_mb" data-mode-view="<?=$mapview_type ?>" data-id="<?=$mapview_id ?>" data-lat="<?=array_get($location, 'lat') ?>" data-lng="<?=array_get($location, 'lng') ?>">Bản đồ</a></li>
                                <li><a href="#" class="btn btn-primary btn-sort event_hotel_list_open_sort"><?=$sort_title?></a></li>
                                <li><a href="#" class="btn btn-primary btn-filter event_hotel_list_open_filter">Bộ lọc</a></li>
                                <?
                                if ($has_filter) {
                                    ?>
                                    <li id="btn-clear-filter">
                                        <a href="<?=$place_url?>" title="Xóa bộ lọc"><i class="fal fa-times-circle"></i> Xóa bộ lọc</a>
                                    </li>
                                    <?
                                }
                                ?>
                            </ul>
                            <h1 class="search-string modern-result-string page_main_title" id="modern-result-string"><?=$page_h1?></h1>
                            <?
                            if ($has_filter) {
                                ?>
                                <p class="clear_filter_pc">
                                    <a href="<?=$place_url?>" title="Xóa bộ lọc"><i class="fal fa-times-circle"></i> Xóa bộ lọc</a>
                                </p>
                                <?
                            }
                            ?>
                        </div>
                        <div id="modern-search-result" class="modern-search-result">
                            <div class="style-list">
                                <?
                                if (!empty($list_hotel)) {
                                    $i  =   0;
                                    foreach ($list_hotel as $row) {
                                        $row['param']   =   param_box_web(24);
                                        echo    $HotelModel->showHotelItem($row, ++$i > 2 ? true : false);
                                        
                                        /** Show điểm để tìm cách order by cho hợp lý **/
                                        //echo    '<p>Price: ' . $row['has_price'] . ', Top: ' . $row['hot_top'] . ', Hot: ' . $row['hot_hot'] . ', Type: ' . $row['hot_type'] . ', Book: ' . $row['hot_count_booking'] . ', View: ' . $row['hot_count_view'] . '</p>';
                                        
                                    }
                                } else {
                                    echo    '<div class="col-xs-12">
                                                <div class="alert alert-warning mt15 none-tour">Không có kết quả nào được tìm thấy, vui lòng thay đổi các tiêu chí tìm kiếm để có kết quả tốt hơn!</div>
                                            </div>';
                                            
                                    //Nếu ko có bộ sưu tập gì thì show list các hotel/destination để ko bị Google tính duplicate content
                                    ?>
                                    <h3 class="link_list_parent">Bạn có thể tham khảo thêm:</h3>
                                    <?
                                    if ($search_type == 'district') {
                                        ?>
                                        <p class="link_list_parent">&bull; <a href="<?=$Router->listHotelCity($search_info, param_box_web(27))?>" title="Xem các khách sạn ở <?=$search_info['cit_name']?>">Khách sạn ở <?=$search_info['cit_name']?></a></p>
                                        <p class="link_list_parent">&bull; <a href="<?=$Router->listTourDistrict($search_info, param_box_web(27))?>" title="Tour du lịch <?=$search_info['dis_name']?>">Tour du lịch <?=($district_name != $search_info['cit_name'] ? $district_name : $search_info['dis_name'])?></a></p>
                                        <?
                                    }
                                    ?>
                                    <p class="link_list_parent">&bull; <a href="<?=$Router->listTourCity($search_info, param_box_web(27))?>" title="Tour du lịch <?=$search_info['cit_name']?>">Tour du lịch <?=$search_info['cit_name']?></a></p>
                                    <?
                                }
                                ?>
                            </div>
                        </div>
                        <?=generate_pagebar($total_record, $page_size, ['group', 'id'])?>
                    </div>
                </div>
                <div class="box_links box_relate_border">
                    <h3 class="suggest_title mt0">Xem thêm:</h3>
                    <div class="box_links__body row">
                        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                            <a href="<?=($place_url . '?type=Resort' . param_box_web(28, true, '&')) ?>" title="Xem các Resort ở <?=$search_info['name'] ?>"><i class="fas fa-building"></i>Resort ở <?=$search_info['name'] ?></a>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                            <a href="<?=($place_url . '?type=Homestay' . param_box_web(28, true, '&')) ?>" title="Xem các Homestay ở <?=$search_info['name'] ?>"><i class="fas fa-building"></i>Homestay ở <?=$search_info['name'] ?></a>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                            <a href="<?=($place_url . '?type=Khách+sạn' . param_box_web(28, true, '&')) ?>" title="Xem các Khách sạn ở <?=$search_info['name'] ?>"><i class="fas fa-building"></i>Khách sạn ở <?=$search_info['name'] ?></a>
                        </div>
                        <?
                        for($star = 3; $star <= 5; $star++) {
                            ?>
                            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                <a href="<?=($place_url . '?star=' . $star . param_box_web(28, true, '&')) ?>" title="Xem các khách sạn <?=$star ?> ở <?=$search_info['name'] ?>"><i class="fas fa-building"></i>Khách sạn <?=$star ?> sao ở <?=$search_info['name'] ?></a>
                            </div>
                            <?
                        }
                        ?>
                    </div>
                </div>
                <?
            
                //Show các Collection liên quan
                if (!empty($list_collection)) {
                    ?>
                    <div class="list_collection_bottom list_collection_hot box_relate_border">
                        <h3 class="suggest_title">Các bộ sưu tập được du khách chọn đặt nhiều:</h3>
                        <div class="box_list_col row">
                            <div class="col-lg-6 col-md-12">
                                <?
                                $i  =   0;
                                foreach ($list_collection as $row) {
                                    $i++;
                                    ?>
                                    <p><i class="far fa-list-alt"></i><a href="<?=$Router->detailCollection($row, param_box_web(26))?>" title="<?=$row['col_name']?>"><?=$row['col_name']?></a></p>
                                    <?
                                    if ($i == 3)    echo    '</div><div class="col-lg-6 col-md-12">';
                                }
                                ?>
                            </div>
                        </div>
                        <?
                        if (!empty($search_info['cit_id'])) {
                            $list_d =   $DB->query("SELECT dis_id, dis_name, dis_name_show, dis_hot
                                                    FROM district
                                                    WHERE dis_active = 1 AND dis_hot = 1 AND dis_city = " . $search_info['cit_id'])
                                                    ->toArray();
                            foreach ($list_d as $d) {
                                ?>
                                <h4 class="top_list_view_more"><i class="fal fa-hand-point-right"></i><a href="<?=$Router->listCollectionDistrict($d, param_box_web(26))?>">Xem thêm các bộ sưu tập ở <?=$PlaceModel->getDistrictName($d)?></a></h4>
                                <?
                            }  
                            ?> 
                            <h4 class="top_list_view_more"><i class="fal fa-hand-point-right"></i><a href="<?=$Router->listCollectionCity($search_info, param_box_web(26))?>">Xem thêm các bộ sưu tập ở <?=$search_info['cit_name']?></a></h4>
                            <?
                        }
                        ?>
                    </div>
                    <?
                }
                ?>
                <div class="row" style="margin: 0;">
                    <?
                    if(!empty($search_info['description'])) {
                        ?>
                        <div class="col-12 list_hotel__description box_relate_border">
                            <h3 class="suggest_title">Giới thiệu về <?=$place_name_full?>:</h3>
                            <div>
                                <?=$search_info['description'] ?>
                            </div>
                        </div>
                        <?
                    }
                    
                    //Show tour relate
                    if (!empty($list_tour)) {
                        ?>
                        <div class="st-overview-content st_tab_service clearfix">
                            <div class="st-content-over">
                                <div class="st-content">
                                    <div class="title">
                                        <h2 class="title_relate_tour title_box_relate mb-20">
                                            <a href="<?=$url_tour_list . param_box_web(30, true)?>" title="Xem tất cả các tour du lịch <?=$place_name_full?>" target="_blank">Tour du lịch <?=$place_name_full?></a> được đặt nhiều nhất
                                            <span><a href="<?=$url_tour_list . param_box_web(30, true)?>" title="Xem tất cả các tour du lịch <?=$place_name_full?>" target="_blank">Xem tất cả</a></span>
                                        </h2>
                                    </div>
                                </div>
                                <div class="st-tab-service-content">
                                    <div class="tab-content">
                                        <div role="tabpanel" class="tab-pane active" id="st_tours_ccv">
                                            <div id="tour-search-result" class="modern-search-result col-md-12" data-layout="1">
                                                <div class="row row-wrapper">
                                                    <div class="row row-wrapper list_item_hoz">
                                                        <?
                                                        foreach ($list_tour as $row) {
                                                            $row['param']   =   param_box_web(29);
                                                            echo    $TourModel->showTourItemHorizontal($row, 'col-lg-3 col-md-3 col-sm-6 col-xs-12 item-service grid-item has-matchHeight');
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?
                    }
                    
                    /** Bài viết liên quan **/
                    if (!empty($list_article)) {
                        ?>
                        <div class="col-12 box_links box_relate_article box_relate_border">
                            <h3>Cẩm nang du lịch <?=$place_name_full?> mới nhất</h3>
                            <div class="box_links__body row">
                                <?
                                foreach($list_article as $item) {
                                    ?>
                                    <div class="col-xs-12 col-sm-6">
                                        <a href="<?=$Router->detailArticle($item, param_box_web(31)) ?>" class="open-modal-destination" target="_tblank" data-type="article" data-id="<?=$item['art_id']?>" data-box="31">&bull;&nbsp;<?=$item['art_title'] ?></a>
                                    </div>
                                    <?
                                }
                                ?>
                            </div>
                        </div>
                        <?
                    }
                    /** Tour destination **/
                    if (!empty($list_destination)) {
                        ?>
                        <div class="st-map-wrapper box_links--margin box_top_dest">
                            <h2 class="title_box_relate">Điểm du lịch nổi tiếng ở <?=$place_name_full?> <span><a href="<?=$url_destination_list . param_box_web(33, true)?>" title="Xem tất cả các địa danh du lịch ở <?=$place_name_full?>" target="_blank">Xem tất cả</a></span></h2>
                            <div class="mt10">
                                <div class="owl-carousel owl-theme slider_dest_schedule">
                                    <?
                                    foreach ($list_destination as $row) {
                                        $url    =   $Router->detailDestination($row, param_box_web(32));
                                        ?>
                                        <div class="item">
                                            <a href="<?=$url?>" title="<?=$row['des_name']?>" class="open-modal-destination" target="_tblank" data-id="<?=$row['des_id']?>" data-box="32">
                                                <img data-src="<?=$Router->srcDestination($row['des_image'], SIZE_SMALL)?>" alt="<?=$row['des_name']?>" class="lazyload" />
                                            </a>
                                            <p><a href="<?=$url?>" title="<?=$row['des_name']?>" class="bold st-link c-main open-modal-destination" target="_tblank" data-id="<?=$row['des_id']?>"><?=$row['des_name']?></a></p>
                                        </div>
                                        <?
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?
                    }
                    
                    /** Các câu hỏi liên quan **/
                    if(!empty($question_relate)) {
                        ?>
                        <div class="st-faq box_relate_border">
                            <h3 class="st-section-title title_box_relate">Câu hỏi thường gặp khi đi du lịch <?=$place_name_full?></h3>
                            <?
                            foreach($question_relate as $question) {
                                ?>
                                <div class="item active">
                                    <div class="header">
                                        <i class="input-icon st-border-radius field-icon fal fa-question-circle"></i>
                                        <h5><?=$question['title'] ?></h5>
                                    </div>
                                    <div class="body">
                                        <?=$question['content'] ?>
                                    </div>
                                </div>
                                <?
                            }
                            ?>
                        </div>
                        <?
                    }
                    ?>
                </div>
                <?
            } else {
                ?>
                <div class="box_notfound">
                    <p>Các khách sạn tại <b><?=$place_name_full?></b> đang được Vietgoing cập nhật thêm nhằm đem lại những dịch vụ và trải nghiệm tốt hơn cho Quý khách hàng!</p>
                    <?
                    include('../../../layout/inc_not_found.php');
                    ?>
                </div>
                <?
            }
            ?>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_iframe" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="input-icon field-icon fa"><svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <defs></defs>
                    <g id="Ico_close" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" stroke-linecap="round" stroke-linejoin="round">
                        <g id="Group" stroke="#1A2B48" stroke-width="1.5">
                            <g id="close">
                                <path d="M0.75,23.249 L23.25,0.749" id="Shape"></path>
                                <path d="M23.25,23.249 L0.75,0.749" id="Shape"></path>
                            </g>
                        </g>
                    </g>
                    </svg></i>
                </button>
                <iframe width="100%" height="100%" frameborder="0" marginheight="0" marginwidth="0" src=""></iframe>
            </div>
        </div>
    </div>
</div>