<?
include('../Core/Config/require_web.php');
exit('Feature Disabled!');
if (!$User->logged) {
    exit('Vui lòng đăng nhập để sử dụng tính năng này!');
}

$response   =   [
                'status'    =>  0,
                'content'   =>  ''
                ];

$record_id  =   getValue('id', GET_INT, GET_POST, 0);
$title      =   getValue('title', GET_STRING, GET_POST, '');
$content    =   getValue('content', GET_STRING, GET_POST, '');
$score      =   getValue('score', GET_ARRAY, GET_POST, []);
$group      =   getValue('group', GET_INT, GET_POST, 0);

if(!in_array($group, [GROUP_TOUR, GROUP_HOTEL])) return;

switch($group) {
    case GROUP_HOTEL; 
        $table  = 'hotel';
        $prefix = 'hot';
        $review_criteria = $cfg_review_criteria_hotel;
    break;

    default:
        $table  = 'tour';
        $prefix = 'tou';
        $review_criteria = $cfg_review_criteria_tour;
}

$record_info  =   $DB->query("SELECT {$prefix}_id FROM {$table} WHERE {$prefix}_id= $record_id AND {$prefix}_active = 1")->getOne();
if (empty($record_info)) {
    $response['content']    =   'Đối tượng đánh giá không tồn tại!';
    exit(json_encode($response));
}

$title      =   removeHTML($title);
$content    =   removeHTML($content);

//Gán các score vào array để khớp với field trong DB
$arr_score  =   [];
$i  =   1;
foreach ($score as $s) {
    $s  =   (int)$s;
    if ($s > 0 && $s <= REVIEW_MAX_SCORE) {
        $arr_score[$i++]    =   $s;
    }
    if ($i > REVIEW_NUMBER_CRITERIA)   break;
}
if (count($arr_score) < REVIEW_NUMBER_CRITERIA) {
    $response['content']    =   'Bạn vui lòng nhập đầy đủ và hợp lệ điểm của các tiêu chí đánh giá';
    exit(json_encode($response));
}

//Class query để insert đánh giá
$Query  =   new GenerateQuery('reviews');
$Query->add('rev_group', DATA_INTEGER, $group)
        ->add('rev_item_id', DATA_INTEGER, $record_id)
        ->add('rev_user_id', DATA_INTEGER, $User->id)
        ->add('rev_content', DATA_STRING, $content, 'Bạn chưa nhập nội dung đánh giá')
        ->add('rev_time_create', DATA_INTEGER, CURRENT_TIME)
        ->add('rev_show', DATA_INTEGER, 1);
foreach ($arr_score as $k => $v) {
    $Query->add('rev_score_' . $k, DATA_DOUBLE, $v);
}

if ($Query->validate()) {
    
    $insert =   $DB->execute($Query->generateQueryInsert());
    
    if ($insert > 0) {
        //Tính lại điểm review của đối tượng
        $Model->updateReviewScore($group, $record_id);
        
        /** Generate ra đoạn HTML để append vào list các đánh giá **/
        $response['status'] =   1;
        $html_star  =   '';
        foreach ($arr_score as $k => $s) {
            $html_star  .=  '<li>
                                <label>' . $review_criteria[$k] . '</label>
                                ' . gen_star($s) . '
                            </li>';
        }
        
        //Chỉ show 1 tên cuối cùng để bảo mật thông tin KH
        $name   =   $User->info['use_name'];
        $exp    =   explode(' ', $name);
        $count  =   count($exp);
        if ($count > 1) {
            $name   =   '... ' . $exp[$count - 1];
        }
        
        $response['content']    =   '<div class="comment-item">
                    <div class="comment-item-head">
                        <div class="media">
                            <div class="media-left">
                                <img alt="' . $name . '" src="' . $Router->srcUserAvatar($User->info['use_avatar'], SIZE_SMALL) . '" class="avatar avatar-50 photo avatar-default" height="50" width="50"/>
                            </div>
                        </div>
                    </div>
                    <div class="comment-item-body">
                        <p class="title st_tours">' . $name . ' - <span class="rv_date">' . date('d/m/Y') . '</span></p>
                        <div class="detail">
                            <div class="st-description">
                                ' . $content . '
                            </div>
                        </div>
                    </div>
                </div>';
    } else {
        $response['content']    =   'Chưa gửi được đánh giá, vui lòng thử lại!';
    }
} else {
    $response['content']    =   implode('<br>', $Query->getError());
}

echo    json_encode($response);
exit;
?>