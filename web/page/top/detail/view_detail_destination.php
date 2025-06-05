<div id="st-content-wrapper" class="search-result-page st-tours">
    <div class="st-breadcrumb breadcrumb_border">
        <div class="container">
            <?=generate_navbar($arr_breadcrum)?>
        </div>
    </div>
    <div class="container">
        <div class="search_tab">
            <div class="box_search_tab search_full suggest_search" data-box="128">
                <?
                include('../../../layout/inc_box_search.php');
                ?>
            </div>
        </div>
        <div class="st-hotel-result">
            <div class="row mt40">
                <div class="col-lg-12 col-md-12">
                    <?
                    if ($collection_info['col_active'] == 1) {
                        ?>
                        <div class="toolbar">
                            <h1 class="search-string modern-result-string page_main_title" id="modern-result-string"><?=$page_h1?></h1>
                            <p class="short_intro"><?=$collection_info['col_description']?></p>
                        </div>
                        <div id="modern-search-result" class="modern-search-result box_list_dest">
                            <div class="row row-wrapper">
                                <?
                                foreach ($list_destination as $row) {
                                    $url    =   $Router->detailDestination($row, param_box_web(98));
                                    ?>
                                    <div class="col-lg-3 col-md-6 col-sm-6 col-xs-12 item-service grid-item has-matchHeight">
                                        <div class="service-border">
                                            <div class="thumb">
                                                <a href="<?=$url?>">
                                                    <img src="<?=$Router->srcDestination($row['des_image'], SIZE_MEDIUM)?>" class="img-responsive wp-post-image" alt="<?=$row['des_name']?>">
                                                </a>
                                            </div>
                                            <div class="des_item_info">
                                                <h4 class="service-title item_title_list"><a href="<?=$url?>" class="st-link c-main"><?=$row['des_name']?></a></h4>
                                                <p class="service-location">
                                                    <i class="input-icon field-icon fas fa-map-marker-alt"></i><?=$list_district_name[$row['des_district']]?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <?
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
            </div>
            <div class="">
                <div class="box_links box_relate_border">
                    <h3 class="suggest_title mt0">Xem thêm:</h3>
                    <div class="box_links__body row">
                        <?
                        if (!empty($district_info)) {
                            ?>
                            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                <a href="<?=$Router->listDestinationDistrict($district_info, param_box_web(99))?>" target="_blank"><i class="fas fa-map-marker-alt"></i>Địa danh du lịch ở <?=$place_name?></a>
                            </div>
                            <?
                        }
                        ?>
                        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                            <a href="<?=$Router->listDestinationCity($city_info, param_box_web(99))?>" target="_blank"><i class="fas fa-map-marker-alt"></i>Địa danh du lịch ở <?=$city_info['cit_name']?></a>
                        </div>
                        <?
                        if (!empty($district_info)) {
                            ?>
                            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                <a href="<?=$Router->listHotelDistrict($district_info, param_box_web(99))?>" target="_blank"><i class="fas fa-building"></i>Tất cả khách sạn ở <?=$place_name?></a>
                            </div>
                            <?
                        }
                        ?>
                        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                            <a href="<?=($url_hotel_city . param_box_web(99, true))?>?" target="_blank"><i class="fas fa-building"></i>Tất cả khách sạn ở <?=$city_info['cit_name']?></a>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                            <a href="<?=($url_hotel_city . '?type=Resort' . param_box_web(99, true, '&')) ?>"><i class="fas fa-building"></i>Resort ở <?=$city_info['cit_name']?></a>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                            <a href="<?=($url_hotel_city . '?type=Homestay' . param_box_web(99, true, '&')) ?>"><i class="fas fa-building"></i>Homestay ở <?=$city_info['cit_name']?></a>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                            <a href="<?=($url_hotel_city . '?type=Khách+sạn' . param_box_web(99, true, '&')) ?>"><i class="fas fa-building"></i>Khách sạn ở <?=$city_info['cit_name']?></a>
                        </div>
                        <?
                        //Các KS theo hạng sao
                        for($star = 3; $star <= 5; $star++) {
                            ?>
                            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                <a href="<?=($url_hotel_city . '?star=' . $star . param_box_web(99, true, '&')) ?>"><i class="fas fa-building"></i>Khách sạn <?=$star ?> sao ở <?=$city_info['cit_name']?></a>
                            </div>
                            <?
                        }
                        
                        //Gợi ý tour
                        if (!empty($district_info)) {
                            ?>
                            <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                                <a href="<?=$Router->listTourDistrict($district_info, param_box_web(99))?>" target="_blank"><i class="fas fa-building"></i>Tour du lịch <?=$place_name?></a>
                            </div>
                            <?
                        }
                        ?>
                        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                            <a href="<?=$Router->listTourCity($city_info, param_box_web(99))?>" target="_blank"><i class="fas fa-suitcase-rolling"></i>Tour du lịch <?=$city_info['cit_name']?></a>
                        </div>
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
                                    <p><i class="far fa-list-alt"></i><a href="<?=$Router->detailCollection($row, param_box_web(100))?>"><?=$row['col_name']?></a></p>
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
            <?
            /** Các KS liên quan **/
            if (!empty($list_relate)) {
                ?>
                <div class="">
                    <h2 class="title_box_relate text-center">Khách sạn được ưa thích ở <a href="<?=$Router->listHotelCity($city_info, param_box_web(102))?>" title="Khách sạn ở <?=$city_info['cit_name']?>" target="_blank"><?=$city_info['cit_name']?></a> <span><a href="<?=$Router->listHotelCity($city_info, param_box_web(102))?>" title="Xem tất cả các khách sạn ở <?=$city_info['cit_name']?>" target="_blank">Xem tất cả</a></span></h2>
                    <div class="services-grid services-nearby hotel-nearby grid mt20 grid-item">
                        <div class="row">
                            <?
                            foreach ($list_relate as $row) {
                                $row['param']   =   param_box_web(101);
                                echo    $HotelModel->showHotelItem2($row);
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

<?
/** Update visit để tính CTR **/
set_visit_page(149);
?>