<?
/**
 * Class AttributeModel
 * Version 1.0
 * @author Vietgoing
 */

class AttributeModel extends Model {
    
    private $list_attr_group    =   []; //Mảng lưu các attribute của group để cache ko cần phải query lại
    private $list_attr_value    =   []; //Mảng lưu các value của attribute để cache ko cần query lại
    const   ATTR_HOTEL_LOCATION  =   14;  //ID của attribute Vị trí KS (Show KS gần trung tâm, gần sân bay...)
    const   COLUMN_SERVICE  =   1;  //Column lưu giá trị của dịch vụ khách sạn
    const   COLUMN_LOCATION =   8;  //Column lưu giá trị của vị trí KS
    
    function __construct() {
        parent::__construct();
    }
    
    /**
     * AttributeModel::getAttributeOfId()
     * Lấy ra các attribute đang có của 1 đối tượng
     * @param mixed $id: record ID
     * @param mixed $group: (Hotel, Room Tour...)
     * @param string $where: Câu SQL thêm, co bao gom AND
     * @return
     */
    function getAttributeOfId($id, $group, $where = "") {
        
        $id     =   (int)$id;
        $group  =   (int)$group;
        $arr_attribute  =   [];
        
        //Tùy theo lấy attribute của hotel, room, tour, ticket... mà join với bảng data tương ứng
        switch ($group) {
            case GROUP_HOTEL:
                $table  =   'hotel';
                $prefix =   'hot_';
                break;
            case GROUP_ROOM:
                $table  =   'room';
                $prefix =   'roo_';
                break;
            case GROUP_TOUR:
                $table  =   'tour';
                $prefix =   'tou_';
                break;
            default:
                return [];
                break;
        }
        
        //Lấy thông tin bản ghi
        //Chỉ lấy ID và các Column có chứa giá trị Attribute để cho nhẹ dữ liệu
        $arr_column =   [];
        for ($i = 1; $i <= 15; $i++) {
            $arr_column[]   =   $prefix . 'col_' . $i;
        }
        
        $record_info    =   $this->DB->pass()->query("SELECT " . $prefix . "id, " . implode(", ", $arr_column) . "
                                                FROM $table
                                                WHERE " . $prefix . "id = $id")
                                                ->getOne();
        
        if (empty($record_info))    return [];
        
        //Lấy các attribute và value của group, rồi foreach, cái nào & với value của column thì là có
        $data   =   $this->DB->query("SELECT attribute_name.*, atv_id, atv_name, atv_icon, atv_value_hexa
                                        FROM attribute_name
                                        INNER JOIN attribute_value ON atn_id = atv_attribute_id
                                        WHERE atn_group = $group AND atn_sennet_active = 1 $where
                                        ORDER BY atn_order, atv_order, atn_name, atv_name")
                                        ->toArray();
        
        foreach ($data as $row) {
            
            //Tên của column chứa value
            $col    =   $prefix . 'col_' . $row['atn_column'];
            $value_col  =   intval($record_info[$col]);
            $value_hexa =   intval($row['atv_value_hexa']);
            
            //Tùy theo kiểu dữ liệu của attribute là multi hay select mà check dữ liệu để lấy ra
            if ($row['atn_type'] == ATTRIBUTE_SELECT) {
                
                if ($value_col == $value_hexa) {
                    $arr_attribute[$row['atn_id']]  =   [
                                                        'info'  =>  [
                                                                'name'  =>  $row['atn_name'],
                                                                'type'  =>  $row['atn_type'],
                                                                'param' =>  $row['atn_alias_search'],
                                                                'require'   =>  $row['atn_require'] == 1 ? true : false
                                                            ],
                                                        'data'  =>  [
                                                                $row['atv_id']  =>  [
                                                                                    'name'  =>  $row['atv_name'],
                                                                                    'icon'  =>  $row['atv_icon'],
                                                                                    'value' =>  $row['atv_value_hexa']
                                                                                    ]
                                                            ]
                                                        ];
                }
                
            } else if ($row['atn_type'] == ATTRIBUTE_MULTI) {
                
                if ($value_col >= $value_hexa && $value_col & $value_hexa) {
                    
                    //Nếu chưa có mảng của attribute_name này thì khởi tạo mảng mới
                    if (!isset($arr_attribute[$row['atn_id']])) {
                        $arr_attribute[$row['atn_id']]  =   [
                                                            'info'  =>  [
                                                                    'name'  =>  $row['atn_name'],
                                                                    'type'  =>  $row['atn_type'],
                                                                    'param' =>  $row['atn_alias_search'],
                                                                    'require'   =>  $row['atn_require'] == 1 ? true : false
                                                                ],
                                                            'data'  =>  []   
                                                            ];
                    }
                    
                    //Nếu có rồi thì chỉ cần gán thêm các values vào list
                    $arr_attribute[$row['atn_id']]['data'][$row['atv_id']]  =   [
                                                                                'name'  =>  $row['atv_name'],
                                                                                'icon'  =>  $row['atv_icon'],
                                                                                'value' =>  $row['atv_value_hexa']
                                                                                ];
                
                }
                
            }
        }
        
        //Return
        return $arr_attribute;
    }
    
    /**
     * AttributeModel::getAttributeOfGroup()
     * Lấy ra các attribute của 1 group
     * @param mixed $group: Hotel, Room, Tour...
     * @param string $where: Câu SQL truyền thêm vào, bao gồm cả AND
     * @param bool $active: Chỉ lấy các value được active hay lấy tấ cả
     * @return array
     */
    function getAttributeOfGroup($group, $where = "", $active = true) {
        
        //Nếu có mảng chứa rồi thì lấy luôn để ko bị query lại
        $key_md5    =   md5($group . $where);
        if (isset($this->list_attr_group[$key_md5])) {
            return $this->list_attr_group[$key_md5];
        }
        
        $group  =   (int)$group;
        $arr_attribute  =   [];
        
        //Lấy ra tất cả các attribute_value của group
        $data   =   $this->DB->query("SELECT attribute_name.*, atv_id, atv_name, atv_icon, atv_value_hexa
                                        FROM attribute_name
                                        INNER JOIN attribute_value ON atn_id = atv_attribute_id
                                        WHERE atn_group = " . $group . $where . ($active ? " AND atn_sennet_active = 1 AND atv_active = 1" : "") . "
                                        ORDER BY atn_order ASC, atv_order ASC, atn_name ASC, atv_name ASC")
                                        ->toArray();
        foreach ($data as $row) {
            //Nếu chưa có mảng của attribute_name này thì khởi tạo mảng mới
            if (!isset($arr_attribute[$row['atn_id']])) {
                $arr_attribute[$row['atn_id']]  =   [
                                                    'info'  =>  [
                                                                'name'      =>  $row['atn_name'],
                                                                'type'      =>  $row['atn_type'],
                                                                'param'     =>  $row['atn_alias_search'],
                                                                'column'    =>  $row['atn_column'],
                                                                'require'   =>  $row['atn_require'] == 1 ? true : false
                                                                ],
                                                    'data'  =>  []
                                                    ];
            }
            
            //Nếu có rồi thì chỉ cần gán thêm các values vào list
            $arr_attribute[$row['atn_id']]['data'][$row['atv_id']]  =   [
                                                                        'name'  =>  $row['atv_name'],
                                                                        'icon'  =>  $row['atv_icon'],
                                                                        'value' =>  $row['atv_value_hexa']
                                                                        ];
            
        }
        
        //Gán vào mảng cache để nếu cần dùng thì ko cần query lại
        $this->list_attr_group[$key_md5]    =   $arr_attribute;
        
        //Return
        return $arr_attribute;
    }
    
    /**
     * AttributeModel::getValueOfAttribute()
     * 
     * @param mixed $attr_id
     * @param string $field
     * @return
     */
    function getValueOfAttribute($attr_id, $field = 'atv_id, atv_name, atv_value_hexa, atv_icon') {
        
        if (empty($this->list_attr_value[$attr_id])) {
            $this->list_attr_value[$attr_id]    =   $this->DB->query("SELECT $field
                                                                        FROM attribute_value
                                                                        WHERE atv_attribute_id = $attr_id AND atv_active = 1
                                                                        ORDER BY atv_order")
                                                                        ->toArray();
        }
        
        return $this->list_attr_value[$attr_id];
    }
    
    /**
     * AttributeModel::generateSQLFilter()
     * Xử lý bộ lọc theo attribute
     * @param mixed $group
     * @return ['query', 'meta', 'selected', 'canonical']
     */
    function generateSQLFilter($group) {
        
        $return =   [
            'query'     =>  '',
            'meta'      =>  '',
            'selected'  =>  [],
            'canonical' =>  []
        ];
        
        switch ($group) {
            case GROUP_HOTEL:
                $prefix =   'hot_';
                break;
            case GROUP_TOUR:
                $prefix =   'tou_';
                break;
            default:
                return $return;
        }
        
        //Lưu ý quan trọng: Câu SQL phải viết đúng chính xác 100% so với ở trang filter để lấy được data từ cache ra cho nhẹ
        $list_attribute =   $this->getAttributeOfGroup($group, " AND atn_show_filter = 1 AND atv_hot = 1");
        
        $filter_attribute_selected  =   []; //Mảng chứa các Attribute người dùng đang lọc
        
        foreach ($list_attribute as $atn_id => $atn) {
            if (!isset($_GET[$atn['info']['param']]))   continue;   //Nếu ko có param thì continure luôn cho nhẹ
            
            //Cột lưu giá trị của Attribute
            $col    =   $prefix . 'col_' . $atn['info']['column'];
            
            //Lấy các giá trị search
            $value_search   =   [];
            switch ($atn['info']['type']) {
                
                //Search dạng 1 giá trị
                case ATTRIBUTE_SELECT:
                    //Có một số attribute SELECT trước đây để là Multi đã được index ở Google cho phép chọn nhiều nên bị thành lỗi code, cho lấy 1 giá trị đầu tiên để query thôi
                    if (gettype($_GET[$atn['info']['param']]) == 'array') {
                        $arr_value  =   getValue($atn['info']['param'], GET_ARRAY, GET_GET, []);
                        $value  =   isset($arr_value[0]) ? $arr_value[0] : '';
                    } else {
                        //Nếu là các link mới thì chỉ chọn 1 giá trị nên get value theo dạng string bình thường
                        $value  =   getValue($atn['info']['param'], GET_STRING, GET_GET, '');
                    }
                    $value  =   mb_strtolower($value, 'UTF-8');
                    if (!empty($value)) {
                        foreach ($atn['data'] as $atv_id => $atv) {
                            //Do đã lấy hết các attribute_value ra rồi nên check theo tên luôn để đỡ phải query
                            if ($value == mb_strtolower($atv['name'], 'UTF-8')) {
                                
                                //Gán vào mảng các attribute được selected
                                $return['selected'][$atn_id][]  =   $atv_id;
                                
                                //Gán vào câu SQL của attribute
                                $return['query']    .=  " AND $col = " . (int)$atv['value'];
                                
                                //Nếu attribute này được tick cho join vào các thẻ meta thì lấy ra text để gán vào
                                if ($atn['info']['join_meta'] != 0) {
                                    $return['meta'] .=  ($atn['info']['text_meta'] != '' ? ' ' . $atn['info']['text_meta'] : '') . ' ' . $value;
                                }
                                
                                //Gán vào Canonical
                                if ($atn['info']['canonical'] == 1) {
                                    $return['canonical'][$atn['info']['param']] =   $atv['name'];
                                }
                                
                                //Nếu attribute là dạng select 1 giá trị thì chỉ cần search đúng giá trị đó là thôi ko cần phải duyệt các value khác nữa
                                break;  //Break của mảng $atn['data']
                            }
                        }
                    }
                break;
                
                //Search dạng multi
                case ATTRIBUTE_MULTI:
                    $arr_value  =   getValue($atn['info']['param'], GET_ARRAY, GET_GET, []);
                    
                    if (!empty($arr_value)) {
                        
                        //Khi generate ra câu SQL thì sẽ có dạng AND col_1 >= 10 AND col_1 & 10 AND col_1 >= 20 AND col_1 & 20, nên sẽ tách thành các câu SQL riêng
                        //cho từng attribute để ko bị lặp lại cái AND col_1 >= 10&20, chỉ lấy 1 value lớn nhất để chỉ có 1 lần duy nhất col_1 >= 20
                        $sql_atn    =   "";
                        $max_value  =   0;
                        //Mảng chứa riêng các Attribute_Value của riêng Attribute_Name này, để duyệt lại 1 lượt theo thứ tự của attribute_value đã select
                        //rồi lấy ra mảng canonical để ko phải query từ DB
                        $arr_selected   =   [];
                        
                        foreach ($arr_value as $value) {
                            $value  =   mb_strtolower($value, 'UTF-8');
                            
                            foreach ($atn['data'] as $atv_id => $atv) {
                                //dump($value . '-' . mb_strtolower($atv['name'], 'UTF-8'));
                                if ($value == mb_strtolower($atv['name'], 'UTF-8')) {
                                    
                                    //Gán vào mảng các attribute được selected
                                    $return['selected'][$atn_id][]  =   $atv_id;
                                    $arr_selected[] =   $atv_id;    //Tách ra mảng riêng để nhìn code cho nó minh bạch
                                    
                                    //Gán vào câu SQL của attribute
                                    $sql_atn    .=  " AND $col & " . (int)$atv['value'];
                                    if ($atv['value'] > $max_value) $max_value  =  $atv['value'];
                                    
                                    //Nếu attribute này được tick cho join vào các thẻ meta thì lấy ra text để gán vào
                                    if ($atn['info']['join_meta'] != 0) {
                                        $return['meta'] .=  ($atn['info']['text_meta'] != '' ? ' ' . $atn['info']['text_meta'] : '') . ' ' . $value;
                                    }
                                }
                            }
                            
                        }
                        
                        //Search multi (hexa value) thì phải thêm điều kiện >= giá trị lớn nhất
                        $return['query']    .=  " AND $col >= $max_value";
                        $return['query']    .=  $sql_atn;
                        
                        //Gán vào Canonical
                        if ($atn['info']['canonical'] == 1 && !empty($arr_selected)) {
                            $return['canonical'][$atn['info']['param']] =   [];
                            //Duyệt lại mảng $atn[data] vì mảng này đã sắp xếp theo thứ tự rồi nên sắp xếp canonical cũng theo thứ tự đó
                            foreach ($atn['data'] as $atv_id => $atv) {
                                if (in_array($atv_id, $arr_selected)) {
                                    $return['canonical'][$atn['info']['param']][]   =   $atv['name'];
                                }
                            }
                        }
                        
                    }
                    
                break;
            }
            //dump($value_search);
            
        }
        
        //Return
        return $return;
    }
    
    /**
     * AttributeModel::showAttributeHot()
     * 
     * @param mixed $attributes
     * @return
     */
    function showAttributeHot($attributes) {
        $html   =   '';
        
        foreach ($attributes as $atn_id => $atn) {
            $value  =   '';
            foreach ($atn['data'] as $atv) {
                $value  .=  ', ' . $atv['name'];
            }
            $value  =   substr($value, 2);
            if ($atn_id == 17)  $value  =   'View ' . str_replace('View ', '', $value);
            
            $html   .=  '<p class="room_attr_hot"><span><i class="' . $atv['icon'] . '"></i></span>' . $value .'</p>';
        }
        
        //Return
        return $html;
        
    }
    
}
?>