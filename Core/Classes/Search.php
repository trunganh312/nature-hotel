<?
/**
 * Class Search
 * @author Vietgoing
 * Created: 21/03/2023 
 **/

class Search extends Model {
    
    private $score      =   []; //Mảng chứa các điểm sẽ được tính cho câu sql LIKE khi bẻ theo chiều xuôi của kw
    private $score_reverse  =   []; //Mảng chứa các điểm tính theo chiều ngược
    private $max_word   =   5;  //Số lần tối đa của việc tách keyword để tránh bị overload query
    private $remove_word    =   []; //Các chữ sẽ remove khỏi từ khóa để search (VD từ khách sạn, tour...)
    
    function __construct() {
        parent::__construct();
        
        /**
         * Bẻ keyword ra rồi search lần lượt từ cụm dài => ngắn rồi cộng điểm vào cái nào nhiều điểm thì lấy
         * VD: Khách sạn Hà Nội, Khách sạn Hà, Khách sạn, Khách
         * Nếu chưa đủ max_word thì search tiếp: sạn Hà Nội, Hà Nội, Nội
         * Ý tưởng: Search tổng thể chung tất ở 1 form search, khi select từ dropdown thì sẽ check xem item chọn đó thuộc module nào
         * Nếu các module ko cần chọn ngày tháng thì redirect luôn, nếu là KS thì cho focus vào ô chọn Ngày/Tháng
         * Có thể: Ban đầu ẩn ô chọn ngày đi, nếu chọn KS thì mới show ra  
         **/
        
        $this->score    =   [
            1   =>  [20, 17],
            2   =>  [16, 13],
            3   =>  [12, 9],
            4   =>  [8, 5],
            5   =>  [4, 1]
        ];
        
        $this->score_reverse    =   [
            1   =>  [18, 16],
            2   =>  [15, 12],
            3   =>  [11, 8],
            4   =>  [7, 4],
            5   =>  [3, 1]
        ];
        
        $this->remove_word  =   [
            'khách sạn tại',
            'khách sạn ở',
            'khách sạn',
            'tour du lịch',
            'tour đi',
            'tour '
        ];
        
    }
    
    /**
     * Search::generateSQLSearch()
     * 
     * @param mixed $keyword
     * @param mixed $arr_field_search [field_1, field_2]
     * @return [field_1 => sql_1, field_2 => sql_2]
     */
    function generateSQLSearch($keyword, $arr_field_search) {
        
        //field_search truyền vào 1 mảng để tránh phải xử lý while nhiều ở các page mà search chung
        
        //Clear keyword
        $keyword    =   mb_strtolower($keyword);
        foreach ($this->remove_word as $k) {
            $keyword    =   str_replace($k, '', $keyword);
        }
        $PlaceModel =   new PlaceModel;
        $keyword    =   $PlaceModel->getMainName($keyword); //Remove các từ khóa kiểu xã, phường, thị trấn...
        
        //Remove các từ khóa kiểu Khách sạn, Tour
        $keyword    =   str_replace(['khách sạn ', 'tour '], '', $keyword);
        
        $keyword    =   trim($keyword);
        $keyword_clean  =   $keyword;   //Gán lại để cho vào mảng return
        
        //Nối thêm 1 từ bất kỳ vào keyword để ko cần phải check if else nhiều trong vòng while
        $keyword    =   $keyword . ' Vietgoing';
        
        $arr_diem = $arr_where  =   [];
        $count  =   1;
        while ($count <= $this->max_word) {
            //Mỗi 1 lượt vòng lặp sẽ bẻ bỏ 1 chữ cuối cùng đi
            $keyword    =   trim(substr($keyword, 0, strrpos($keyword, ' ')));
            if (!empty($keyword)) {
                foreach ($arr_field_search as $field) {
                    $arr_diem[$field][]     =   "IF($field LIKE '$keyword%', " . $this->score[$count][0] . ", 0)";
                    $arr_diem[$field][]     =   "IF($field LIKE '%$keyword%', " . $this->score[$count][1] . ", 0)";
                    //Định search kiểu WHERE OR nhưng thấy ra nhiều kết quả rác quá nên bỏ đi
                    if (count(explode(' ', $keyword)) >= 2) $arr_where[$field][]    =   "$field LIKE '%$keyword%'";
                }
                $count++;
            } else {
                break;
            }
        }
        
        //Nếu vẫn chưa hết tối đa lượt for cho phép thì search tiếp với kw bẻ theo chiều ngược
        if ($count < $this->max_word) {
            //Gán lại keyword ban đầu
            $keyword    =   $keyword_clean;
            while ($count <= $this->max_word) {
                //Mỗi 1 lượt vòng lặp sẽ bẻ bỏ 1 chữ cuối cùng đi
                $keyword    =   trim(substr($keyword, strpos($keyword, ' ')));
                if (!empty($keyword)) {
                    foreach ($arr_field_search as $field) {
                        $arr_diem[$field][]     =   "IF($field LIKE '$keyword%', " . $this->score_reverse[$count][0] . ", 0)";
                        $arr_diem[$field][]     =   "IF($field LIKE '%$keyword%', " . $this->score_reverse[$count][1] . ", 0)";
                        //Định search kiểu WHERE OR nhưng thấy ra nhiều kết quả rác quá nên bỏ đi
                        if (count(explode(' ', $keyword)) >= 2) $arr_where[$field][]    =   "$field LIKE '%$keyword%'";
                    }
                    $count++;
                } else {
                    break;
                }
            }
        }
        
        foreach ($arr_field_search as $key => $field) {
            unset($arr_field_search[$key]); //Unset field đi để gán lại thành câu query
            $arr_field_search[$field]   =   [
                'diem'  =>  (!empty($arr_diem[$field]) ? implode(" + ", $arr_diem[$field]) : "0") . " AS diem",
                'where' =>  "AND " . (!empty($arr_where[$field]) ? "(" . implode(" OR ", $arr_where[$field]) . ")" : "$field LIKE '%$keyword_clean%'")
                //'where' =>  "AND $field LIKE '%$keyword_clean%'"
            ];
        }
        
        //Gán luôn $keyword_clean vào mảng return
        $arr_field_search['keyword']    =   $keyword_clean;
        //dump($arr_field_search);
        //Return
        return $arr_field_search;
    }
    
}

?>