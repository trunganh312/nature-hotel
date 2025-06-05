<div id="st-content-wrapper" class="search-result-page st-tours st-hotels">
    <div class="st-breadcrumb breadcrumb_border">
        <div class="container">
            <?=generate_navbar($arr_breadcrum)?>
        </div>
    </div>
    <div class="container">
        <div class="box_search_detail search_full search_hide_mb suggest_search" data-box="127">
            <?
            include('../../../layout/inc_box_search_tour.php');
            ?>
        </div>
        <div class="st-hotel-result">
            <div class="row mt40">
                <div class="col-lg-9 col-md-9 list_item_main">
                    <?
                    if ($collection_info['col_active'] == 1) {
                        ?>
                        <div class="toolbar">
                            <h1 class="search-string modern-result-string page_main_title" id="modern-result-string"><?=$page_h1?></h1>
                            <p class="short_intro"><?=$collection_info['col_description']?></p>
                        </div>
                        <div id="modern-search-result" class="modern-search-result" data-layout="1">
                            <div class="style-list">
                                <?
                                foreach ($list_tour as $row) {
                                    $row['param']   =   param_box_web(107);
                                    echo    $TourModel->showTourItemVertical($row);
                                }
                                ?>
                            </div>
                        </div>
                        <?
                    } else {
                        ?>
                        <div class="box_notfound">
                            <p>Danh sách <strong><?=$collection_info['col_name']?></strong> đang được Vietgoing cập nhật thêm nhằm đem lại những dịch vụ và trải nghiệm tốt hơn cho Quý khách hàng!</p>
                            <?
                            include('../../../layout/inc_not_found.php');
                            ?>
                        </div>
                        <?
                    }
                    ?>
                </div>
                <div class="col-lg-3 col-md-3 box_relate_right">
                    <?
                    if (!empty($list_relate)) {
                        ?>
                        <h2 class="title_relate_right break_border_mb">Khách sạn được ưa thích ở <a href="<?=$Router->listHotelCity($city_info, param_box_web(111))?>" title="Xem các khách sạn ở <?=$city_info['cit_name']?>" target="_blank"><?=$city_info['cit_name']?></a> <span>(<a href="<?=$Router->listHotelCity($city_info, param_box_web(111))?>" title="Xem các khách sạn ở <?=$city_info['cit_name']?>" target="_blank">Xem tất cả</a>)</span></h2>
                        <div class="services-grid services-nearby hotel-nearby grid grid-item">
                            <?
                            foreach ($list_relate as $row) {
                                $row['param']   =   param_box_web(110);
                                echo    $HotelModel->showHotelItem2($row, '');
                            }
                            ?>
                        </div>
                        <?
                    }
                    ?>
                </div>
            </div>
            <div class="">
                <div class="box_links box_relate_border">
                    <h3 class="suggest_title mt0">Xem thêm:</h3>
                    <div class="box_links__body row">
                        <?
                        //Gợi ý tour
                        if (!empty($district_info)) {
                            ?>
                            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                <a href="<?=$Router->listTourDistrict($district_info, param_box_web(108))?>" target="_blank"><i class="fas fa-building"></i>Tour du lịch <?=$place_name?></a>
                            </div>
                            <?
                        }
                        ?>
                        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                            <a href="<?=$Router->listTourCity($city_info, param_box_web(108))?>" target="_blank"><i class="fas fa-suitcase-rolling"></i>Tour du lịch <?=$city_info['cit_name']?></a>
                        </div>
                        <?
                        //Gợi ý KS
                        if (!empty($district_info)) {
                            ?>
                            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                <a href="<?=$Router->listHotelDistrict($district_info, param_box_web(108))?>" target="_blank"><i class="fas fa-building"></i>Tất cả khách sạn ở <?=$place_name?></a>
                            </div>
                            <?
                        }
                        ?>
                        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                            <a href="<?=($url_hotel_city . param_box_web(108, true, '&'))?>?" target="_blank"><i class="fas fa-building"></i>Tất cả khách sạn ở <?=$city_info['cit_name']?></a>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                            <a href="<?=($url_hotel_city . '?type=Resort' . param_box_web(108, true, '&')) ?>"><i class="fas fa-building"></i>Resort ở <?=$city_info['cit_name']?></a>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                            <a href="<?=($url_hotel_city . '?type=Homestay' . param_box_web(108, true, '&')) ?>"><i class="fas fa-building"></i>Homestay ở <?=$city_info['cit_name']?></a>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                            <a href="<?=($url_hotel_city . '?type=Khách+sạn' . param_box_web(108, true, '&')) ?>"><i class="fas fa-building"></i>Khách sạn ở <?=$city_info['cit_name']?></a>
                        </div>
                        <?
                        //Các KS theo hạng sao
                        for($star = 3; $star <= 5; $star++) {
                            ?>
                            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                <a href="<?=($url_hotel_city . '?star=' . $star . param_box_web(108, true, '&')) ?>"><i class="fas fa-building"></i>Khách sạn <?=$star ?> sao ở <?=$city_info['cit_name']?></a>
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
                                $total  =   count($list_collection);
                                foreach ($list_collection as $row) {
                                    $i++;
                                    ?>
                                    <p><i class="far fa-list-alt"></i><a href="<?=$Router->detailCollection($row, param_box_web(109))?>" title="<?=$row['col_name']?>"><?=$row['col_name']?></a></p>
                                    <?
                                    if ($i == ceil($total/2))    echo    '</div><div class="col-lg-6 col-md-12">';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <?
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?
/** Update visit để tính CTR **/
set_visit_page(150);
?>