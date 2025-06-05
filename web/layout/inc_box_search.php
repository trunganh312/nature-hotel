<div class="container-fluid">
    <div class="row">
        <div class="search-form-wrapper">
            <div class="search-form-text">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="st_hotel" class="<?=(empty($page_module) || ($page_module != 'tour' && $page_module != 'ticket') ? 'active' : '')?>"><a href="#st_hotel" aria-controls="st_hotel" role="tab" data-toggle="tab" class="event_tab_search" title="Chuyển sang tìm kiếm khách sạn">Khách sạn</a></li>
                    <li role="st_tours" class="<?=(!empty($page_module) && $page_module == 'tour' ? 'active' : '')?>"><a href="#st_tours" aria-controls="st_tours" role="tab" data-toggle="tab" class="event_tab_search" title="Chuyển sang tìm kiếm tour">Tour</a></li>
                    <li role="st_ticket" class="<?=(!empty($page_module) && $page_module == 'ticket' ? 'active' : '')?>"><a href="#st_ticket" aria-controls="st_ticket" role="tab" data-toggle="tab" class="event_tab_search" title="Chuyển sang tìm kiếm vé máy bay, vé cáp treo...">Vé</a></li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane <?=(empty($page_module) || ($page_module != 'tour' && $page_module != 'ticket') ? 'active' : '')?>" id="st_hotel">
                        <?
                        include('inc_box_search_hotel.php');
                        ?>
                    </div>
                    <div role="tabpanel" class="tab-pane <?=(!empty($page_module) && $page_module == 'tour' ? 'active' : '')?>" id="st_tours">
                        <?
                        include('inc_box_search_tour.php');
                        ?>
                    </div>
                    <div role="tabpanel" class="tab-pane <?=(!empty($page_module) && $page_module == 'ticket' ? 'active' : '')?>" id="st_ticket">
                        <?
                        include('inc_box_search_ticket.php');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>