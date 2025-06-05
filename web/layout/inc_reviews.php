<?
// Tham số default của $inc_reviews_data;
$inc_reviews_data_default = [
    'group'     =>  GROUP_HOTEL,
    'page'      =>  1,
    'item'      =>  0,
    'page_size' =>  10
];

$inc_reviews_data = array_merge($inc_reviews_data_default, $inc_reviews_data, ['page'=> getValue('page', GET_INT, GET_GET, $inc_reviews_data_default['page'])]);
$inc_reviews_data['page'] = $inc_reviews_data['page'] < 1 ? 1 : $inc_reviews_data['page'];
$inc_reviews_data['from'] = ($inc_reviews_data['page']-1)*$inc_reviews_data['page_size'];
$total_record = $DB->count("SELECT COUNT(1) AS total 
                            FROM reviews 
                            WHERE rev_show = 1 AND rev_group = " . $inc_reviews_data['group'] . " 
                                AND rev_item_id = " . $inc_reviews_data['item']);
?>

<div class="review-pagination">
    <div class="summary"></div>
    <div id="reviews-list" class="review-list">
        <?
        /** Lấy các review **/
        $data_review    =   $DB->query("SELECT reviews.*, use_name, use_avatar, use_vg_staff, use_sex
                                        FROM reviews
                                        INNER JOIN user ON rev_user_id = use_id
                                        WHERE rev_group = " . $inc_reviews_data['group'] . " AND rev_item_id = " . $inc_reviews_data['item'] . "
                                            AND rev_show = 1
                                        ORDER BY rev_time_create DESC
                                        LIMIT {$inc_reviews_data['from']}, {$inc_reviews_data['page_size']}")
                                        ->toArray();
        
        foreach ($data_review as $row) {
            //Chỉ show 1 tên cuối cùng để bảo mật thông tin KH
            //$name   =   ($row['use_vg_staff'] == 1 ? $row['rev_user_name'] : $row['use_name']);
            $name   =   $row['rev_user_name'];
            
            if (!empty($name)) {
                $exp    =   explode(' ', $name);
                $count  =   count($exp);
                if ($count > 1) {
                    $name   =   '... ' . $exp[$count - 1];
                }
            } else {
                $name   =   'Ẩn Danh';
            }

            //Nếu là khách sạn thì show thêm loại phòng mà khách đặt và số đêm ở.
            $room_stay  =   '';
            if ($row['rev_group'] == GROUP_HOTEL) {
                $arr_room   =   [];
                $list_rooms =   $DB->query("SELECT roo_name
                                        FROM booking_hotel_room
                                        INNER JOIN room ON bhr_room_id = roo_id
                                        WHERE bhr_booking_hotel_id = " . $row['rev_booking_id'])
                                        ->toArray();
                foreach($list_rooms as $item) {
                    $arr_room[] =   $item['roo_name'];
                }
                if (empty($arr_room)) {
                    save_log('error_review_booking_404.cfn', 'Empty Room, Booking ID: ' . $row['rev_booking_id']);
                }
                $room_stay  =   implode(', ', $arr_room);

                $room_stay  =   '<p class="fs13"><i class="fal fa-bed-alt"></i> ' . $room_stay . '.</p>';

                //Lấy thông tin của BK
                $bk =   $DB->query("SELECT bkho_checkin, bkho_checkout
                                    FROM booking_hotel
                                    WHERE bkho_id = " . $row['rev_booking_id'])
                                    ->getOne();
                if (!empty($bk)) {
                    if ($bk['bkho_checkout'] > $bk['bkho_checkin']) {
                        $room_stay  .=  '<p class="fs13"><i class="fal fa-calendar"></i> ' . floor(($bk['bkho_checkout'] - $bk['bkho_checkin'])/86400) . ' đêm tháng ' . date('m/Y', $bk['bkho_checkout']) . '.</p>';
                    }
                } else {
                    save_log('error_review_booking_404.cfn', 'Booking ID: ' . $row['rev_booking_id']);
                }
            }

            //Lấy Avatar cho phù hợp
            $avatar =   'avatar.png';
            if ($row['use_vg_staff'] != 1) {
                if ($row['use_sex'] == SEX_MALE) {
                    $avatar =   'avatar-male.png';
                } else if ($row['use_sex'] == SEX_FEMALE) {
                    $avatar =   'avatar-female.png';
                }
            }
            $avatar =   $cfg_path_image . $avatar;
            ?>
            <div class="comment-item">
                <div class="comment-item-head">
                    <div class="media">
                        <div class="media-left">
                            <img alt="Đánh giá <?=(isset($object_name) ? $object_name : 'dịch vụ du lịch')?>" src="<?=$avatar?>" class="avatar avatar-50 photo avatar-default" height="50" width="50" loading="lazy"/>
                        </div>
                    </div>
                </div>
                <div class="comment-item-body">
                    <p class="title st_tours"><b><?=$name?></b> <span class="rv_date">- Ngày đánh giá: <?=date('d/m/Y', $row['rev_time_create'])?>.</span></p><?=$room_stay?>
                    <div class="detail">
                        <div class="st-description">
                            <p><?=$row['rev_content']?><?=(substr($row['rev_content'], 0, -1) != '.' ? '.' : '')?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?
        }
        ?>
        
    </div>
</div>
<?=generate_pagebar($total_record, $inc_reviews_data['page_size'], [], false)?>
<?
// Unset biến rác

?>