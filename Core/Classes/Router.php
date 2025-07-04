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
     * Router::srcHotel()
     * 
     * @param mixed $hotel_id
     * @param mixed $image_name
     * @param string $size
     * @return
     */
    function srcHotel($hotel_id, $image_name) {
        
        $src    =   DOMAIN_STATIC . '/hotel/' . $hotel_id . '/' . $image_name;
        
        return $src;
    }

     /**
     * Router::srcHotel()
     * 
     * @param mixed $hotel_id
     * @param mixed $image_name
     * @param string $size
     * @return
     */
    function srcRoom($room_id, $image_name) {
        
        $src    =   DOMAIN_STATIC . '/room/' . $room_id . '/' . $image_name;
        
        return $src;
    }

    function srcCity($image_name, $size = '') {
        
        if (!empty($image_name)) {
        
            if ($size != '')    $size   .=  '/';
            
            return  $this->domain . '/city/' . $size . $image_name;
        
        }
        
        return $this->path_theme_image . 'avatar.png';
    }

    function srcDistrict($image_name, $size = '') {
        
        if (!empty($image_name)) {
        
            if ($size != '')    $size   .=  '/';
            
            return  $this->domain . '/district/' . $size . $image_name;
        
        }
        
        return $this->path_theme_image . 'avatar.png';
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
     * Router::srcDocument()
     * 
     * @param mixed $image_name
     * @param string $size
     * @return
     */
    function srcDocument($image_name, $size = '') {
        
        if (!empty($image_name)) {
        
            if ($size != '')    $size   .=  '/';
            
            return  $this->domain . '/document/' . $size . $image_name;
        
        }
        
        return $this->path_theme_image . 'avatar.png';
    }
    
}