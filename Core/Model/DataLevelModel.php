<?
/**
 * DataLevel Model
 * Version 1.0
 */

class DataLevelModel extends Model {
    
    //Mảng chưa tất cả các category
    private $all_cate   =   [];
    
    //Mảng chứa tất cả các category được sắp xếp theo thứ tự
    private $all_level  =   [];
    private $stt;
    
    //Một số mảng để lưu cache, tránh bị query lặp lại
    private $cache_all_level    =   []; //List all category da sap xep level
    private $cache_array_child  =   []; //List All category child của 1 cate
    
    //Tên table của Model và prefix của field
    private $table;
    private $prefix;

    //Các biến public
    public  $field_id   =   '';
    public  $field_name =   '';
    public  $field_parent_id    =   '';
    public  $field_is_parent    =   '';
    public  $field_active   =   '';
    public  $field_order    =   '';
    public  $list_field =   '';
    private $sql_default    =   ""; //Câu SQL mặc định để loại các Phòng/Ban default đi
    
    /**
     * DataLevel::__construct()
     * 
     * @return void
     */
    function __construct($table = 'category') {
        
        parent::__construct();
        
        $this->table    =   $table;
        switch ($this->table) {

            case 'admins_group':
            case 'users_group':
                $this->prefix   =   'gro_';
                break;

            case 'admins_department':
                $this->prefix   =   'dep_';
                break;
            
            case 'users_department':
                $this->prefix   =   'dep_';
                $this->sql_default  =   " AND dep_is_default = 0";
                break;

            default:
                $this->prefix   =   'cat_';
                break;
        }
        
        //Các trường dùng thường xuyên sẽ khai báo ở đây cho gọn
        $this->field_id         =   $this->prefix . 'id';
        $this->field_name       =   $this->prefix . 'name';
        $this->field_parent_id  =   $this->prefix . 'parent_id';
        $this->field_is_parent  =   $this->prefix . 'is_parent';
        $this->field_active     =   $this->prefix . 'active';
        $this->field_order      =   $this->prefix . 'order';
        $this->list_field   =   "{$this->field_id}, {$this->field_name}, {$this->field_parent_id}, {$this->field_is_parent}, {$this->field_active}, {$this->field_order}";
        
        //Khởi tạo từ -1 để ++ cho thành 0 để bắt đầu
        $this->stt  =   -1;
    }
    
    /**
     * DataLevel::getAllLevel()
     * Lay ra toan bo cac category va sap xep theo level
     * @param string $field: Cac truong can lay
     * @param string $sql: Cau SQL (ko bao gom AND ở đầu)
     * @param integer $id_highest: ID cua cấp cao nhất cần lấy
     * @return array
     */
    function getAllLevel($sql = "", $id_highest = 0, $field = "") {
        
        if (empty($field)) {
            $field  =   $this->list_field;
        }
        
        /** Nếu có cache rồi thì return luôn **/
        //if (!empty($this->cache_all_level[$id_highest])) return $this->cache_all_level[$id_highest];
        
        if (empty($sql)) $sql    =   "1";
        
        //Nếu chưa có mảng cache của all cate thì lấy ra toàn bộ category theo câu SQL
        if (empty($this->all_cate)) {
            
            $data =   $this->DB->pass(false)->query("SELECT	$field
                                        FROM {$this->table}
                                        WHERE $sql {$this->sql_default}
                                        ORDER BY {$this->field_order}")
                                        ->toArray();
            
            foreach ($data as $row){
    			$this->all_cate[$row[$this->field_parent_id]][$row[$this->field_id]] =	$row;
    		}
        }
		
		//Sắp xếp và gán Level
		$this->sortLevel($this->all_cate, $id_highest);
		
        //Gán vào mảng cache level để nếu cần thì dùng lại
        $this->cache_all_level[$id_highest]  =   $this->all_level;
        
		//Sau khi sort thì trả về mảng chứa toàn bộ cate sau khi đã được sắp xếp theo level
		return $this->all_level;
	}
    
    /**
     * DataLevel::getAllLevelStep()
     * Lay ra array chua toan bo cac category va sap xep theo level va gan them |--|-- o dau
     * @param integer $id_highest: ID của cấp cao nhất
     * @param string $sql: Cau SQL (ko bao gom AND ở đầu)
     * @param string $field: Các field cần lấy
     * @return
     */
    function getAllLevelStep($sql = "", $id_highest = 0, $field = "", $text_level = '|--') {
        
        if (empty($field)) {
            $field  =   $this->list_field;
        }
        
        $array  =   $this->getAllLevel($sql, $id_highest, $field);
        return $this->genLevelText($array, $text_level);
    }
	
    /**
     * DataLevel::getAllLevelArray()
     * Return array all level (Ko bao gom |--|--)
     * @return []
     */
    function getAllLevelArray() {
        return $this->all_level;
    }
	
    /**
     * DataLevel::sortLevel()
     * Sap xep va gan level cho cac category
     * @param mixed $array: Mang chua toan bo cac category can sort
     * @param integer $id_highest: Index cua level bat dau can sort
     * @param integer $level: Level khoi tao
     * @return void
     */
    function sortLevel($array, $id_highest = 0, $level = -1){
		if(array_key_exists($id_highest, $array)){
			$level++;
			foreach($array[$id_highest] as $key => $value){
				$this->stt++;
                $this->all_level[$this->stt]            =	$value;
				$this->all_level[$this->stt]['level']	=	$level;
				
				//Lap lai qua trinh lay level cua cac phan tu la con cua phan tu $key
				$this->sortLevel($array, $key, $level);
			}
		}
	}
    
    /**
     * DataLevel::genLevelText()
     * Gán mảng all_level thành dạng id => --|--|Tên danh mục
     * @param mixed $array_cate
     * @param string $text: Đoạn string sẽ gán theo cấp level
     * @return array
     */
    function genLevelText($array_cate = [], $text = '|--') {
        $arr_category_level     =   [];
        
        foreach ($array_cate as $key => $row) {
            $level   =  '';
            for ($i = 0; $i < $row['level']; $i++) $level  .=  $text;
            
            $row['name_level']  =   $level . $row[$this->field_name];
            
            $arr_category_level[$row[$this->field_id]]    =   $row['name_level'];
        }
        
        return $arr_category_level;
    }
    
    
    /**
     * DataLevel::getChild()
     * Lay list category la cap con truc tiep cua 1 cate
     * @param string $field: (default: ID, Name)
     * @param string $where
     * @param string $order_by: (default: Name)
     * @param integer $type_return: 'key' - Tra ve dang key => value (Chi co 2 truong id va name), 'row' - Tra ve dang array chua cac row
     * @return array || string list
     */
    function getChild($field = '', $where = '', $order_by = '', $type_return = 'key') {
        
        //Mặc định là lấy theo trường ID và Name
        if (empty($field)) {
            $field  =   "{$this->field_id}, {$this->field_name}";
        }
        
        if (empty($order_by)) {
            $order_by   =   $this->field_name;
        }
        
        //Nếu ko truyền câu query
        if (empty($where)) {
            $where  =   "1";
        }
        
        $data   =   $this->DB->pass()->query("SELECT $field
                                        FROM {$this->table}
                                        WHERE $where {$this->sql_default}
                                        ORDER BY $order_by")
                                        ->toArray();
        //Nếu muốn trả về là mảng ko có key thì return luôn
        if ($type_return == 'row')   return $data;
        
        //Nếu muốn trả về là mảng có key la ID và value là tên
        $array_cate =   [];
        foreach ($data as $row) {
            $array_cate[$row[$this->field_id]] =   $row[$this->field_name];
        }
        
        return $array_cate;
    }
    
    /**
     * DataLevel::getAllChild()
     * Lay toan bo cac ID cua cac category cap con, chau, chut cua 1 cate
     * @param mixed $iCat
     * @param bool $self
     * @param string $return: Co 3 gia tri: 'row': Return [row], 'key' => [k => v], 'id': [id, id]
     * @return array
     */
    function getAllChild($iCat, $self = true, $return = 'row') {
        
        //Data return
        
        //Nếu đã có mảng cache này rồi thì dùng luôn, ko thì query lại
        if (isset($this->cache_array_child[$iCat])) {
            $array_return   =   $this->cache_array_child[$iCat];
        } else {
            
            $array_return   =   [];
            
            //Nếu lấy cả ID của chính cate cha
            if ($self) {
                $data   =   $this->DB->pass(false, true)->query("SELECT " . $this->list_field . "
                                                FROM " . $this->table . "
                                                WHERE " . $this->field_id . " = " . $iCat)
                                                ->getOne();
                if (empty($data)) {
                    return $array_return;
                }
                
                $array_return[] =   $data;
            }
            
            //Lấy các ID cấp con gần nhất
            //Dùng đệ quy hoặc while thì bao quát hơn nhưng chưa chuyển sang được nên viết tạm 5 lần như này cho nhanh :D
            $list_child_1   =   $this->getChild($this->list_field, $this->field_parent_id . ' = ' . $iCat, $this->field_order, 'row');
            
            foreach ($list_child_1 as $child_1) {
                
                //Gán vào mảng chính
                $array_return[] =   $child_1;
                
                //Lấy cấp 2
                $list_child_2   =   $this->getChild($this->list_field, $this->field_parent_id . ' = ' . $child_1[$this->field_id], $this->field_order, 'row');
                foreach ($list_child_2 as $child_2) {
                    $array_return[] =   $child_2;
                    
                    //Lấy cấp 3
                    $list_child_3   =   $this->getChild($this->list_field, $this->field_parent_id . ' = ' . $child_2[$this->field_id], $this->field_order, 'row');
                    foreach ($list_child_3 as $child_3) {
                        $array_return[] =   $child_3;
                    
                        //Lấy cấp 4
                        $list_child_4   =   $this->getChild($this->list_field, $this->field_parent_id . ' = ' . $child_3[$this->field_id], $this->field_order, 'row');
                        foreach ($list_child_4 as $child_4) {
                            $array_return[] =   $child_4;
                            
                            //Lấy cấp 5
                            $list_child_5   =   $this->getChild($this->list_field, $this->field_parent_id . ' = ' . $child_4[$this->field_id], $this->field_order, 'row');
                            foreach ($list_child_5 as $child_5) {
                                $array_return[] =   $child_5;
                            }
                        }
                    }
                }
            }
            
            //Gán vào mảng cache để dùng lại
            $this->cache_array_child[$iCat] =   $array_return;
        }
        
        //Return
        switch ($return) {
            case 'key':
                $data_return    =   [];
                foreach ($array_return as $row) {
                    $data_return[$row[$this->field_id]] =   $row[$this->field_name];
                }
                return $data_return;
            break;
            
            case 'id':
                $data_return    =   [];
                foreach ($array_return as $row) {
                    $data_return[]  =   $row[$this->field_id];
                }
                return $data_return;
            break;
            
            default:
                return $array_return;
            break;
        }
    }
    
    /**
     * DataLevel::getAllParentLevel()
     * Lay ra cac cap cha cua 1 cate theo level de generate Navigate bar
     * @param mixed $cate_id
     * @return [row]
     */
    function getAllParentLevel($cate_id) {
        
        //Array return
        $arr_level  =   [];
        
        //Lấy thông tin của cate chính
        $row    =   $this->getRecordInfo($this->table, $this->field_id, $cate_id, $this->list_field);
        
        if (!empty($row)) {
            
            $arr_level[]    =   $row;
            
            //Flag để đánh dấu, lấy đến khi cat_parent_id = 0 là cấp cha cao nhất
            $parent =   $row[$this->field_parent_id];
            while($parent > 0) {
                $row    =   $this->getRecordInfo($this->table, $this->field_id, $parent, $this->list_field);
                if (!empty($row)) {
                    $arr_level[]    =   $row;;
                    $parent =   $row[$this->field_parent_id];
                }
            }
            
        }
        
        //Đảo ngược lại thứ tự để đẩy cấp cha cao nhất lên đầu
        $arr_level  =   array_reverse($arr_level);
        
        return $arr_level;
    }
    
}
?>