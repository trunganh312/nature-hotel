<?
/**
 * Class PlaceModel
 * Version 1.0
 */

class PlaceModel extends Model{
    
    private $all_city   =   [];
    private $city_area  =   []; //List các city được chia theo vùng
    
    function __construct() {
        parent::__construct();
    }
    
    /**
     * PlaceModel::getAllCity()
     * 
     * @return
     */
    function getAllCity() {
        
        //Nếu chưa có mảng all_city thì mới cần phải query lại để lấy từ DB ra
        if (empty($this->all_city)) {
            $this->all_city =   $this->getListData('cities', 'cit_id, cit_name', '', 'cit_name');
        }
        
        return $this->all_city;
    }
    
    /**
     * PlaceModel::getDistrictOfCity()
     * 
     * @param mixed $city_id
     * @return
     */
    function getDistrictOfCity($city_id) {
        $data   =   $this->getListData('district', 'dis_id, dis_name', 'dis_city = ' . (int)$city_id, 'dis_name');
        return $data;
    }
    
    
    /**
     * PlaceModel::getWardOfDistrict()
     * 
     * @param mixed $district_id
     * @return
     */
    function getWardOfDistrict($district_id) {
        $data   =   $this->getListData('ward', 'war_id, war_name', 'war_district = ' . (int)$district_id, 'war_name');
        return $data;
    }
    
    /**
     * PlaceModel::getCityArea()
     * Lấy DS các tỉnh/TP được chia theo vùng
     * @return
     */
    function getCityArea() {
        return $this->city_area;
    }
    
    /**
     * PlaceModel::getDestinationImage()
     * 
     * @param mixed $destination_id
     * @param string $field
     * @return
     */
    function getDestinationImage($destination_id, $field = "*") {
        $data   =   $this->DB->query("SELECT $field FROM destination_image WHERE dei_destination_id = " . (int)$destination_id . " ORDER BY dei_order")
                                ->toArray();
        return $data;
    }
    
    /**
     * PlaceModel::getDestinationArticle()
     * Lấy các bài viết liên quan đến địa danh
     * @param mixed $destination_id
     * @param string $field
     * @param string $order_by
     * @param integer $limit
     * @return [row]
     */
    function getDestinationArticle($destination_id, $field = "art_id, art_title, art_image", $order_by = "", $limit = 5) {
        $data   =   $this->DB->query("SELECT $field
                                        FROM destination_article
                                        INNER JOIN article ON dear_article_id = art_id
                                        WHERE dear_destination_id = " . (int)$destination_id .
                                        ($order_by != "" ? " ORDER BY $order_by" : "") .
                                        ($limit != "" ? " LIMIT $limit" : ""))
                                        ->toArray();
        
        return $data;
    }

    
    /**
     * PlaceModel::getDestinationByLatLon()
     * Lấy địa điểm trong bán kính km theo lat,lon
     * @param mixed $city_id
     * @param integer $destination_id
     * @param mixed $lat
     * @param mixed $lon
     * @param string $field
     * @param integer $km
     * @param integer $size
     * @return
     */
    public function getDestinationByLatLon ($city_id, $destination_id, $lat, $lon, $field = "des_id, des_name, des_image, des_address_full", $km = 15, $size = 4) {
        return $this->DB->query("SELECT $field , 
                        ST_Distance_Sphere( point ('{$lon}', '{$lat}'), 
                        point(des_lon, des_lat)) * .000621371192 
                        as `distance_in_km` 
                    FROM destination
                    WHERE des_city = {$city_id} AND des_active = 1 AND des_id <> {$destination_id}
                    HAVING `distance_in_km` <= {$km}
                    ORDER BY distance_in_km ASC
                    LIMIT {$size}")
                ->toArray();
    }

    
    /**
     * PlaceModel::showDestination()
     * Hiển thị địa điểm
     * @param mixed $row
     * @return
     */
    function showDestination($row, $open_popup = false) {
        global  $Router;
        
        $src    =   $Router->srcDestination($row['des_image'], SIZE_MEDIUM);
        $url    =   $Router->detailDestination($row, !empty($row['param']) ? $row['param'] : []);
        $row["des_address_full"] = explode(',', $row["des_address_full"]);
        //Một số chỗ sẽ ko cho mở link mới mà mở popup
        $modal  =   ($open_popup ? ' class="open-modal-destination" target="_tblank" data-id="' . $row["des_id"] . '"' : '');

        $row["des_address_tmp"] = $row["des_address_full"][count($row["des_address_full"])-2] .', '. $row["des_address_full"][count($row["des_address_full"])-1];

        $html   ='<div class="col-xs-12 col-sm6 col-md-3 list_item_hoz has-matchHeight item">
                    <div class="service-border">
                        <div class="featured-image">
                            <a href="' . $url . '" title="' . $row["des_name"] . '"' . $modal . '>
                                <img width="680" height="510" data-src="' . $src . '" class="lazyload img-responsive wp-post-image" alt="' . $row['des_name'] . '" src="' . $Router->noImageSmall() . '" />
                            </a>
                        </div>
                        <h4 class="title plr15"><a href="' . $url . '" title="' . $row["des_name"] . '"' . $modal . ' class="st-link c-main">' . $row["des_name"] . '</a></h4>
                        <div class="sub-title plr15"><i class="input-icon field-icon fas fa-map-marker-alt"></i>' . $row["des_address_tmp"] . '</div>'. 
                        (empty($row['distance_in_km']) ? '' : ('<div class="sub-title plr15"><i class="input-icon field-icon fas fa-compass"></i> Cách đây '. showDistanceText($row['distance_in_km'])) .'</div>') .'
                    </div>
                </div>';
        //Return HTML
        return $html;
    }
    
    /**
     * PlaceModel::getMainName()
     * Remove các chữ Quận huyện, xã... chỉ lấy tên của địa danh
     * @param mixed $name
     * @return
     */
    function getMainName($name) {
        $name   =   str_replace(['Xã ', 'Thị trấn ', 'Thị Trấn ', 'Huyện ', 'Thị xã ', 'Thị Xã ', 'Thành phố ', 'Thành Phố '], '', $name);
        
        //Nếu là các tên kiểu Quận 1, Quận 2, Phường 1, Phường 2...... thì ko remove chữ Quận/Phường đi
        $exp    =   explode('Phường ', $name);
        if (!isset($exp[1])) {
            $exp    =   explode('Quận ', $name);
        }
        if (isset($exp[1]) && intval(trim($exp[1])) <= 0) $name   =   trim($exp[1]);
        
        return trim($name);
    }
    
    /**
     * PlaceModel::getDistrictName()
     * 
     * @param mixed $row
     * @param bool $remove_type: Remove cac chu Quan/Huyen/Thi xa...
     * @return
     */
    function getDistrictName($row, $remove_type = false) {
        if (!empty($row['dis_name_show'])) {
            return $row['dis_name_show'];
        }
        
        //Nếu là địa danh HOT thì xóa các chữ Quận huyện... đi
        if ($row['dis_hot'] == 1 || $remove_type)   $row['dis_name']    =   $this->getMainName($row['dis_name']);
        
        return $row['dis_name'];
    }
    
    /**
     * PlaceModel::getCityHotName()
     * Lay dia danh hot nhat cua 1 tinh (VD: Nha Trang - Khanh Hoa, Kien Trang - Phu Quoc)
     * @param mixed $name
     * @return string $name
     */
    function getCityHotName($name) {
        switch ($name) {
            case 'Kiên Giang':
                return 'Phú Quốc';
            break;
            
            case 'Khánh Hòa':
                return  'Nha Trang';
            break;
            
            case 'Lào Cai':
                return  'Sapa';
            break;
            
            case 'Bình Định':
                return  'Quy Nhơn';
            break;
            
            case 'Quảng Ninh':
                return  'Hạ Long';
            break;
            
            default:
                return $name;
        }
    }
    
}