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
    
}