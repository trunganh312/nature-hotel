<?

use src\Facades\DB;

/**
 * Class Model
 * Version 1.0
 * Created by Vietgoing
 **/

class Model
{

    public static Model $instance;
    public $DB;
    private $error = [];

    function __construct()
    {
        global $DB;
        $this->DB = empty($DB) ? DB::getInstance() : $DB;
    }


    /**
     * Model::addError()
     *
     * @param mixed $error
     * @return void
     */
    function addError($error)
    {

        if (empty($error)) return;

        if (gettype($error) == 'array') {
            $this->error = array_merge($this->error, $error);
        } else {
            $this->error[] = $error;
        }
    }

    /**
     * Model::getError()
     *
     * @return
     */
    function getError()
    {
        return $this->error;
    }

    /**
     * Model::getRecordInfo()
     *
     * @param mixed $table
     * @param mixed $field_id
     * @param mixed $id
     * @param string $field
     * @return
     */
    function getRecordInfo($table, $field_id, $id, $field = '*', $where = '')
    {
        $query = "SELECT " . $field . " FROM " . $table . " WHERE " . $field_id . " = " . $id . $where;
        return $this->DB->query($query)->getOne();
    }

    /**
     * GeneralModel::getListData()
     * Lấy ra list data của 1 table theo điều kiện where
     * @param mixed $table
     * @param mixed $field
     * @param string $where : Ko bao g?m AND ho?c OR ? d?ng tru?c
     * @param string $order_by
     * @param string $type_return : key OR row
     * @return [$row] or [key => value]
     */
    function getListData($table, $field, $where = '', $order_by = '', $type_return = 'key', $limit = 0)
    {

        $sql_where = "1";

        if ($where != '') $sql_where .= " AND " . $where;
        if ($order_by != '') $order_by = " ORDER BY " . $order_by;

        //Nếu type_return = key thì cần gán 2 truường cần lấy thành id và name
        if ($type_return == 'key') {
            $exp = explode(',', $field);
            if (count($exp) == 2) {
                $field = trim($exp[0]) . " AS id, " . trim($exp[1]) . " AS name";
            } else {
                return [];
            }
        }

        $data = $this->DB->query("SELECT " . $field . "
                                        FROM " . $table . "
                                        WHERE " . $sql_where
            . $order_by . ($limit > 0 ? " LIMIT " . (int)$limit : ""))
            ->toArray();
        //Nếu muốn trả về là mảng ko có key thì return luôn
        if ($type_return == 'row') return $data;

        //Nếu muốn trả về mảng có dạng key => value
        $array_return = [];
        foreach ($data as $row) {
            $array_return[$row['id']] = $row['name'];
        }

        return $array_return;

    }

    /**
     * Model::getGroupInfo()
     * L?y các thông tin tên b?ng, prefix... tùy theo group hotel,tour...
     * @param mixed $group
     * @return void [tbl_booking, tbl_object, prefix_booking, prefix_object]
     */
    function getGroupInfo($group)
    {
        /** Tùy theo group mà l?y các b?ng khác nhau **/
        $info = [
            'tbl_booking' => '',
            'tbl_object' => '',
            'prefix_booking' => '',
            'prefix_object' => '',
            'name_object' => ''
        ];

        switch ($group) {
            case GROUP_TOUR:
                $info = [
                    'tbl_booking' => 'booking_tour',
                    'tbl_object' => 'tour',
                    'prefix_booking' => 'bkto_',
                    'prefix_object' => 'tou_',
                    'name_object' => 'Tour'
                ];
                break;

            case GROUP_HOTEL:
                $info = [
                    'tbl_booking' => 'booking_hotel',
                    'tbl_object' => 'hotel',
                    'prefix_booking' => 'bkho_',
                    'prefix_object' => 'hot_',
                    'name_object' => 'Khách sạn'
                ];
                break;

                case GROUP_TA:
                    $info = [
                        'tbl_booking' => 'booking_hotel',
                        'tbl_object' => 'hotel',
                        'prefix_booking' => 'bkho_',
                        'prefix_object' => 'hot_',
                        'name_object' => 'Khách sạn'
                    ];
                    break;

            case GROUP_TICKET:
                $info = [
                    'tbl_booking' => 'booking_ticket',
                    'tbl_object' => 'ticket',
                    'prefix_booking' => 'bkti_',
                    'prefix_object' => 'tic_',
                    'name_object' => 'Vé'
                ];
                break;
            
            case GROUP_OTHER:
                $info = [
                    'tbl_booking' => 'booking_other',
                    'prefix_booking' => 'bkot_',
                    'name_object' => 'Dịch vụ'
                ];
                break;

            default:
                return [];
                break;
        }

        return $info;
    }

    /**
     * Model::updateReviewScore()
     * Update di?m dánh giá c?a d?i tu?ng
     * @param mixed $group
     * @param mixed $item
     * @return void
     */
    function updateReviewScore($group, $item)
    {

        $group_info = $this->getGroupInfo($group);
        $table = $group_info['tbl_object'];
        $prefix = $group_info['prefix_object'];

        //Tính l?i di?m review c?a d?i tu?ng
        $sum_score = $this->DB->query("SELECT SUM(rev_score_1) AS score_1, SUM(rev_score_2) AS score_2, SUM(rev_score_3) AS score_3,
                                            SUM(rev_score_4) AS score_4, SUM(rev_score_5) AS score_5, SUM(rev_score_6) AS score_6, 
                                            SUM(rev_score_7) AS score_7, SUM(rev_score_8) AS score_8, SUM(rev_score_9) AS score_9
                                        FROM reviews
                                        WHERE rev_group = $group AND rev_item_id = $item AND rev_show = 1")
            ->getOne();
        //Tính t?ng review
        $total_review = $this->DB->count("SELECT COUNT(rev_id) AS total
                                                FROM reviews
                                                WHERE rev_group = $group AND rev_item_id = $item AND rev_show = 1");

        /** Update di?m trugn bình c?a t?ng tiêu chí c?a d?i tu?ng vào b?ng reviews score **/
        //Ki?m tra xem dã có b?n ghi di?m dánh giá trong b?ng chua
        $check = $this->DB->query("SELECT resc_id FROM reviews_score WHERE resc_group = $group AND resc_item_id = $item")->getOne();
        if (empty($check)) {

            //N?u chua có thì insert b?n ghi m?i
            $Query = new GenerateQuery('reviews_score');
            $Query->add('resc_group', DATA_INTEGER, $group)
                ->add('resc_item_id', DATA_INTEGER, $item);
            for ($i = 1; $i <= REVIEW_NUMBER_CRITERIA; $i++) {
                $Query->add('resc_score_' . $i, DATA_DOUBLE, $total_review > 0 ? round($sum_score['score_' . $i] / $total_review, 1) : 0);
            }
            if ($Query->validate()) {
                $this->DB->execute($Query->generateQueryInsert());
            } else {
                $this->addError($Query->getError());
            }

        } else {
            //N?u có r?i thì update di?m
            $arr_field = [];
            for ($i = 1; $i <= REVIEW_NUMBER_CRITERIA; $i++) {
                $arr_field[] = 'resc_score_' . $i . ' = ' . ($total_review > 0 ? round($sum_score['score_' . $i] / $total_review, 1) : 0);
            }

            $this->DB->execute("UPDATE reviews_score
                                SET " . implode(', ', $arr_field) . "
                                WHERE resc_id = " . $check['resc_id'] . "
                                LIMIT 1");
        }

        /** Update di?m dánh giá trung bình c?a d?i tu?ng ? b?ng c?a d?i tu?ng d? hi?n th? di?m dánh giá nhanh ko c?n query thêm **/
        $total_score = 0;
        for ($i = 1; $i <= REVIEW_NUMBER_CRITERIA; $i++) {
            $total_score += $sum_score['score_' . $i];
        }
        $this->DB->pass(false, true)->execute("UPDATE {$table}
                            SET {$prefix}review_score = " . ($total_review > 0 ? round($total_score / $total_review / REVIEW_NUMBER_CRITERIA, 1) : 0) . ", 
                                {$prefix}review_count = $total_review
                            WHERE {$prefix}id = $item
                            LIMIT 1");

    }

    /**
     * Model::updateCountView()
     * Update luot view cua doi tuong
     * @param mixed $table
     * @param mixed $id
     * @param bool $exclude_vg
     * @return
     */
    function updateCountView($table, $id, $exclude_vg = true)
    {
        //Ko count view các visit t? nv Vietgoing
        if ($exclude_vg && is_vg()) return;

        switch ($table) {
            case 'article':
                $field_id = 'art_id';
                $field_count = 'art_count_view';
                break;

            case 'tour':
                $field_id = 'tou_id';
                $field_count = 'tou_count_view';
                break;

            case 'destination':
                $field_id = 'des_id';
                $field_count = 'des_count_view';
                break;

            case 'hotel':
                $field_id = 'hot_id';
                $field_count = 'hot_count_view';
                break;

            case 'ticket':
                $field_id = 'tic_id';
                $field_count = 'tic_count_view';
                break;

            case 'collection':
                $field_id = 'col_id';
                $field_count = 'col_count_view';
                break;
        }

        if (!empty($field_id)) {
            $this->DB->execute("UPDATE $table SET $field_count = $field_count + 1 WHERE $field_id = $id LIMIT 1");
        }

    }

}

?>