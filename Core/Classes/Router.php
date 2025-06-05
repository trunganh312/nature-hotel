<?php

/**
 * Class Router
 * Generate ra cac URL
 * Created by Vietgoing.com
 */

class Router {
    
    private $domain;
    private $path_theme_image;
    
    function __construct() {
        $this->domain   =   DOMAIN_STATIC;
        
        //Nếu ký tự cuối cùng là / thì bỏ đi
        if (substr($this->domain, -1) == '/') {
            $this->domain   =   substr($this->domain, 0, -1);
        }
        
        $this->path_theme_image =   $this->domain . '/theme/images/';
    }
    
    /**
     * Router::srcTicket()
     * 
     * @param mixed $ticket_id
     * @param mixed $image_name
     * @param string $size
     * @return
     */
    function srcTicket($ticket_id, $image_name, $size = '') {
        
        if ($size != '')    $size   .=  '/';
        
        $src    =   DOMAIN_STATIC . '/ticket/' . $size . $image_name;
        
        return $src;
    }
    
    /**
     * Router::srcDestination()
     * 
     * @param mixed $image_name
     * @param string $size
     * @return
     */
    function srcDestination($image_name, $size = '') {
        
        if ($size != '')    $size   .=  '/';
        
        $src    =   DOMAIN_STATIC . '/destination/' . $size . $image_name;
        
        return $src;
        
    }
    
    /**
     * Router::srcImageTour()
     * 
     * @param mixed $tour_id
     * @param mixed $image_name
     * @param string $size
     * @return
     */
    function srcTour($tour_id, $image_name, $size = '') {
        
        if ($size != '')    $size   .=  '/';
        
        $src    =   DOMAIN_STATIC . '/tour/' . get_folder_of_image($tour_id) . $size . $image_name;
        
        return $src;
    }

    /**
     * Router::srcHotel()
     * 
     * @param mixed $hot_id
     * @param mixed $image_name
     * @param string $size
     * @return
     */
    function srcHotel($hot_id, $image_name, $size = '') {
        
        if ($size != '')    $size   .=  '/';
        
        $src    =   DOMAIN_STATIC . '/image/hotel/' . get_folder_of_image($hot_id) . $size . $image_name;
        
        return $src;
    }

    /**
     * Router::srcRoom()
     * 
     * @param mixed $room_id
     * @param mixed $image_name
     * @param string $size
     * @return
     */
    function srcRoom($room_id, $image_name, $size = '') {
        
        if ($size != '')    $size   .=  '/';
        
        $src    =   DOMAIN_STATIC . '/image/room/' . get_folder_of_image($room_id) . $size . $image_name;
        
        return $src;
    }
    
    /**
     * Router::srcCompany()
     * 
     * @param mixed $image_name
     * @param string $size
     * @return string path to image
     */
    function srcCompany($image_name, $size = '') {
        
        if ($size != '')    $size   .=  '/';
        
        $src    =   DOMAIN_STATIC . '/image/company/' . $size . $image_name;
        
        return $src;
    }

    function srcDepartment($image_name, $size = '') {
        
        if ($size != '')    $size   .=  '/';
        
        $src    =   DOMAIN_STATIC . '/image/department/' . $size . $image_name;
        
        return $src;
    }
    
    /**
     * Router::srcCategory()
     * 
     * @param mixed $image_name
     * @param string $size
     * @return
     */
    function srcCategory($image_name, $size = '') {
        if ($size != '')    $size   .=  '/';
        
        $src    =   DOMAIN_STATIC . '/category/' . $size . $image_name;
        
        return $src;
    }
    
    /**
     * Router::srcPlace()
     * 
     * @param mixed $image_name
     * @param string $size
     * @return
     */
    function srcPlace($image_name, $size = '') {
        if ($size != '')    $size   .=  '/';
        
        $src    =   DOMAIN_STATIC . '/place/' . $size . $image_name;
        
        return $src;
    }
    
    /**
     * Router::srcUserAvatar()
     * 
     * @param mixed $image_name
     * @param string $size
     * @return
     */
    function srcUserAvatar($image_name, $size = '') {
        
        if (!empty($image_name)) {
        
            if ($size != '')    $size   .=  '/';
            
            return  $this->domain . '/image/user/' . $size . $image_name;
        
        }
        
        return $this->path_theme_image . 'avatar.png';
    }
    
    /**
     * Router::srcBanner()
     * 
     * @param mixed $image_name
     * @return
     */
    function srcBanner($image_name) {
        return DOMAIN_STATIC . '/banner/' . $image_name;
    }
    
    /**
     * Router::srcDeal()
     * 
     * @param mixed $image_name
     * @return
     */
    function srcDeal($image_name) {
        return DOMAIN_STATIC . '/deal/' . $image_name;
    }

     /**
     * Router::srcCccd()
     * 
     * @param mixed $image_name
     * @param string $size
     * @return
     */
    function srcCccd($image_name, $size = '') {
        
        if ($size != '')    $size   .=  '/';
        
        $src    =   DOMAIN_STATIC . '/image/cccd/' . $size . $image_name;
        
        return $src;
        
    }

     /**
     * Router::srcUnc()
     * 
     * @param mixed $image_name
     * @param string $size
     * @return
     */
    function srcUnc($image_name, $size = '') {
        
        if ($size != '')    $size   .=  '/';
        
        $src    =   DOMAIN_STATIC . '/image/unc/' . $size . $image_name;
        
        return $src;
        
    }

     /**
     * Router::srcPromotion()
     * 
     * @param mixed $image_name
     * @param string $size
     * @return
     */
    function srcPromotion($image_name, $size = '') {
        
        if ($size != '')    $size   .=  '/';
        
        $src    =   DOMAIN_STATIC . '/image/promotion/' . $size . $image_name;
        
        return $src;
        
    }

    /**
     * Router::srcAdminAvatar()
     * 
     * @param mixed $image_name
     * @param string $size
     * @return
     */
    function srcAdminAvatar($image_name, $size = '') {
        
        if (!empty($image_name)) {
        
            if ($size != '')    $size   .=  '/';
            
            return  $this->domain . '/image/admin/' . $size . $image_name;
        
        }
        
        return $this->path_theme_image . 'avatar.png';
    }
    /**
     * Router::srcCkeditor()
     * 
     * @param mixed $image_name
     * @param string $size
     * @return
     */
    function srcCkeditor($image_name, $size = '') {
        
        if (!empty($image_name)) {
        
            if ($size != '')    $size   .=  '/';
            
            return  $this->domain . '/image/ckeditor/' . $size . $image_name;
        
        }
        
        return $this->path_theme_image . 'avatar.png';
    }

    /**
     * Router::srcDocument()
     * 
     * @param mixed $image_name
     * @param string $size
     * @return
     */
    function srcDocument($image_name, $size = '') {
        
        if (!empty($image_name)) {
        
            if ($size != '')    $size   .=  '/';
            
            return  $this->domain . '/image/document/' . $size . $image_name;
        
        }
        
        return $this->path_theme_image . 'avatar.png';
    }

    function srcCity($image_name, $size = '') {
        
        if (!empty($image_name)) {
        
            if ($size != '')    $size   .=  '/';
            
            return  $this->domain . '/image/city/' . $size . $image_name;
        
        }
        
        return $this->path_theme_image . 'avatar.png';
    }

    function srcDistrict($image_name, $size = '') {
        
        if (!empty($image_name)) {
        
            if ($size != '')    $size   .=  '/';
            
            return  $this->domain . '/image/district/' . $size . $image_name;
        
        }
        
        return $this->path_theme_image . 'avatar.png';
    }

     /**
     * Router::listHotelDistrict()
     * 
     * @param mixed $row
     * @return
     */
    function listHotelDistrict($row, $param = []) {
        $url    =   $this->domain . '/hotel/district-' . $row['dis_id'] . '-' . to_slug($row['dis_name']) . '.html';
        if (!empty($param)) $url    .=  '?' . http_build_query($param);
        return $url;
    }

    /**
     * Router::listHotelCity()
     * 
     * @param mixed $row
     * @return
     */
    function listHotelCity($row, $param = []) {
        $url    =   $this->domain . '/hotel/city-' . $row['cit_id'] . '-' . to_slug($row['cit_name']) . '.html';
        if (!empty($param)) $url    .=  '?' . http_build_query($param);
        return $url;
    }
    
}