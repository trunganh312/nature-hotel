<div id="st-content-wrapper" class="search-result-page st-tours st-hotels">
    <div id="st-content-wrapper" class="search-result-page st-tours st-hotels">
    <div class="container">
        <div class="st-breadcrumb">
            <?=generate_navbar($arr_breadcrum)?>
        </div>
    </div>
    <div class="container">
        <div class="box_search_detail search_full search_hide_mb suggest_search" data-box="126">
            <?
            include('../../../layout/inc_box_search_hotel.php');
            ?>
        </div>
        <div class="st-hotel-result">
            <?
            if ($search_info['item_active'] == 1 && !empty($list_collection)) {
                ?>
                <div class="row mt40">
                    <div class="col-lg-12 col-md-12 list_item_main">
                        <div class="toolbar">
                            <h1 class="search-string modern-result-string page_main_title" id="modern-result-string"><?=$page_h1?></h1>
                        </div>
                        <div id="modern-search-result" class="modern-search-result" data-layout="1">
                            <div class="style-list">
                                <div class="list_collection_bottom list_collection_hot">
                                    <div class="box_list_col row">
                                        <div class="col-lg-6 col-md-12">
                                            <?
                                            $i  =   0;
                                            $column =   ceil(count($list_collection)/2);
                                            foreach ($list_collection as $row) {
                                                $i++;
                                                ?>
                                                <p><i class="far fa-list-alt"></i><a target="_blank" href="<?=$Router->detailCollection($row, param_box_web(170))?>" title="<?=$row['col_name']?>"><?=$row['col_name']?></a></p>
                                                <?
                                                if ($i == $column)    echo    '</div><div class="col-lg-6 col-md-12">';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <?
                                    if (!empty($search_info['cit_id'])) {
                                        
                                        $list_d =   $DB->query("SELECT dis_id, dis_name, dis_name, dis_hot
                                                                FROM district
                                                                WHERE dis_active = 1 AND dis_hot = 1 AND dis_city = " . $search_info['cit_id'])
                                                                ->toArray();
                                        foreach ($list_d as $d) {
                                            //Ko show URL của chính Quận/Huyện này
                                            if ($search_type == 'district' && $d['dis_id'] == $search_id) continue;
                                            ?>
                                            <h4 class="top_list_view_more"><i class="fal fa-hand-point-right"></i><a href="<?=$Router->listCollectionDistrict($d, param_box_web(170))?>">Xem thêm các bộ sưu tập ở <?=$PlaceModel->getDistrictName($d)?></a></h4>
                                            <?
                                        }
                                        //Nếu là Quận/Huyện thì show thêm link về list BST của City
                                        if ($search_type == 'district') {
                                            ?>
                                            <h4 class="top_list_view_more"><i class="fal fa-hand-point-right"></i><a href="<?=$Router->listCollectionCity($search_info, param_box_web(170))?>">Xem thêm các bộ sưu tập ở <?=$search_info['cit_name']?></a></h4>
                                            <?
                                        }
                                    }      
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div class="box_links box_relate_border">
                        <h3 class="suggest_title mt0">Xem thêm:</h3>
                        <div class="box_links__body row">
                            <?
                            if (!empty($search_info['dis_name'])) {
                                ?>
                                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                    <a href="<?=$Router->listHotelDistrict($search_info, param_box_web(104))?>" target="_blank"><i class="fas fa-building"></i>Khách sạn ở <?=$place_name?></a>
                                </div>
                                <?
                            }
                            ?>
                            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                <a href="<?=($url_hotel_city . param_box_web(104, true))?>" target="_blank"><i class="fas fa-building"></i>Khách sạn ở <?=$search_info['cit_name']?></a>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                <a href="<?=($url_hotel_city . '?type=Resort' . param_box_web(104, true, '&')) ?>"><i class="fas fa-building"></i>Resort ở <?=$search_info['cit_name']?></a>
                            </div>
                            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                <a href="<?=($url_hotel_city . '?type=Homestay' . param_box_web(104, true, '&')) ?>"><i class="fas fa-building"></i>Homestay ở <?=$search_info['cit_name']?></a>
                            </div>
                            <?
                            //Các KS theo hạng sao
                            for ($star = 3; $star <= 5; $star++) {
                                ?>
                                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                    <a href="<?=($url_hotel_city . '?star=' . $star . param_box_web(104, true, '&')) ?>"><i class="fas fa-building"></i>Khách sạn <?=$star ?> sao ở <?=$search_info['cit_name']?></a>
                                </div>
                                <?
                            }
                            ?>
                            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                <a href="<?=($Router->listTourCity($search_info, param_box_web(104)))?>" target="_blank"><i class="fas fa-building"></i>Tour du lịch <?=$search_info['cit_name']?></a>
                            </div>
                            <?
                            if (!empty($search_info['dis_name'])) {
                                ?>
                                <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                    <a href="<?=$Router->listTourDistrict($search_info, param_box_web(104))?>" target="_blank"><i class="fas fa-building"></i>Tour du lịch <?=$place_name?></a>
                                </div>
                                <?
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?
            } else {
                ?>
                <div class="box_notfound">
                    <p>Danh sách các bộ sưu tập HOT tại <strong><?=$place_name_full?></strong> đang được Vietgoing cập nhật thêm nhằm đem lại những dịch vụ và trải nghiệm tốt hơn cho Quý khách hàng!</p>
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