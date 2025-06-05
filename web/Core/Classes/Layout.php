<?
/**
 * Class Layout
 * Ver 1.0
 */

class Layout extends Model {
    
    private $title, $description, $keywords, $amount, $currency, $images, $css, $js, $canonical, $index, $path_theme;
    
    public $configuration;
    /**
     * Layout::__construct()
     * 
     * @return void
     */
    function __construct() {
        
        parent::__construct();
        
        $this->title        =   BRAND_NAME;
        $this->description  =   BRAND_DOMAIN;
        $this->keywords     =   BRAND_DOMAIN;
        $this->amount       =   0;
        $this->currency     =   'VND';
        $this->images       =   [];
        //Default CSS, list từng file chứ ko scan để sắp xếp đúng thứ tự như trên template
        $this->css          =   [
                                'style',
                                'all',
                                'mapview',
                                'vietgoing'
                                ];
                                
        //Default JS
        $this->js           =   [];
        $this->canonical    =   '';
        $this->index        =   true;
        $this->configuration    =   [];

        $this->path_theme = '/theme/';
        
    }
    
    /**
     * Layout::setMenuData()
     * 
     * @param string $title
     * @return void
     */
    function setConfiguration($value) {
        $this->configuration    =   $value;
        return $this;
    }

    /**
     * Layout::setTitle()
     * 
     * @param string $title
     * @return void
     */
    function setTitle($title) {
        $this->title    =   $title;
        return $this;
    }
    
    /**
     * Layout::setDescription()
     * 
     * @param string $description
     * @return void
     */
    function setDescription($description) {
        $this->description  =   $description;
        return $this;
    }
    
    /**
     * Layout::setKeywords()
     * 
     * @param string $keyword
     * @return void
     */
    function setKeywords($keyword) {
        $this->keywords =   $keyword;
        return $this;
    }
    
    /**
     * Layout::setAmount()
     * 
     * @param number $amount
     * @return void
     */
    function setAmount($amount) {
        $this->amount   =   $amount;
        return $this;
    }
    
    /**
     * Layout::setCurrency()
     * 
     * @param string $currency
     * @return void
     */
    function setCurrency($currency) {
        $this->currency =   $currency;
        return $this;
    }
    
    /**
     * Layout::setImages()
     * 
     * @param array $images ['anh_1', 'anh_2',...]
     * @return void
     */
    function setImages($images) {
        $this->images   =   $images;
        return $this;
    }
    
    /**
     * Layout::setCSS()
     * Set cac file CSS se su dung cho page.
     * @param array $css array [file_1, file_2], cac file se ghep them voi cac file css default
     * Ten file ko bao gom .css
     * @return void
     */
    function setCSS($css) {
        $this->css  =   array_merge($this->css, $css);
        return $this;
    }
    
    /**
     * Layout::setJS()
     * Set cac fil JS se su dung cho page
     * @param array $js ['file_1', 'file_2',...], cac file se ghep them voi cac file css default
     * Ten file ko bao gom .js
     * @return void
     */
    function setJS($js) {
        $this->js   =   array_merge($this->js, $js);
        return $this;
    }
    
    /**
     * Layout::setCanonical()
     * 
     * @param string $canonical http://domain.com
     * @return void
     */
    function setCanonical($canonical) {
        $this->canonical    =   $canonical;
        return $this;
    }
    
    
    /**
     * Layout::setIndex()
     * Set INDEX hoac NOINDEX cho 1 page
     * @param mixed $boolean
     * @return void
     */
    function setIndex($boolean) {
        $this->index    =   $boolean;
        return $this;
    }
    
    /**
     * Layout::setReadCSS()
     * Đọc file CSS load ra the <style> hay là Load từng file CSS
     * @param mixed $boolean
     * @return void
     */
    function setReadCSS($boolean) {
        return $this;
    }
    
    
    /**
     * Layout::loadHead()
     * Generate ra noi dung trong the <head></head>
     * @return string
     */
    function loadHead() {
        global $cfg_theme_version_css;
        
        $head = '<meta charset="UTF-8">
                 <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0, user-scalable=no" id="viewport" />
                 <meta http-equiv="x-ua-compatible" content="IE=edge">';
        $head .= '<title>' . $this->title . '</title>' . PHP_EOL;
        $head .= '<meta name="description" content="' . str_replace('"', "'", $this->description) . '" />' . PHP_EOL;
        $head .= '<meta name="keywords" content="' . str_replace('"', "'", $this->keywords) . '" />' . PHP_EOL;
        
        if ($this->index) {
            $head .= '<meta name="robots" content="INDEX,FOLLOW" />' . PHP_EOL;
        } else {
            $head .= '<meta name="robots" content="NOINDEX" />' . PHP_EOL;
        }
        
        $head .= '<link rel="shortcut icon" href="' . DOMAIN_WEB . (is_pro() ? '/favicon_vg.png' : '/favicon_dev.png') . '" type="image/x-icon" />' . PHP_EOL;
        $head .= '<meta property="og:type" content="website" />' . PHP_EOL;
        $head .= '<meta property="og:title" content="' . $this->title . '" />' . PHP_EOL;
        $head .= '<meta property="og:description" content="' . $this->description . '" />' . PHP_EOL;
        $head .= '<meta property="og:site_name" content="' . BRAND_DOMAIN . '" />' . PHP_EOL;
        $head .= '<meta name="p:domain_verify" content="f5e875619438d0b11eb084e3a0ac7dc0"/>' . PHP_EOL;
        
        if ($this->canonical != '') {
            $head .= '<meta property="og:url" content="' . $this->canonical . '" />' . PHP_EOL;
            $head .= '<link rel="canonical" href="' . $this->canonical . '" />' . PHP_EOL;
        }
        
        if ($this->amount > 0) {
            $head .= '<meta property="product:price:amount" content="' . $this->amount . '" />' . PHP_EOL;
            $head .= '<meta property="product:price:currency" content="' . $this->currency . '" />' . PHP_EOL;
        }
        
        if (!empty($this->images)) {
            $head .= '<meta property="og:image" content="' . $this->images['src'] . '" />' . PHP_EOL;
            $head .= '<meta property="og:image:alt" content="' . $this->images['alt'] . '" />' . PHP_EOL;
        }
        
        $head .= '<link rel="dns-prefetch" href="//maps.googleapis.com" /><link rel="dns-prefetch" href="//cdn.jsdelivr.net" /><link rel="dns-prefetch" href="//apis.google.com" />';
        $head .= '<meta name="facebook-domain-verification" content="vwe6phc4umrbrmt3w7w7remq2yc1pu" />' . PHP_EOL;
        
        // Load tất cả CSS
        $css_folder = $_SERVER['DOCUMENT_ROOT'] . '/theme/css/';
        $css_files = glob($css_folder . '*.css');
        
        if (!empty($css_files)) {
            // Trang cần index: Đọc nội dung CSS và thêm vào <style>
            $head .= '<style>';
            foreach ($css_files as $file) {
                $content = file_get_contents($file);
                $head .= $content;
            }
            $head .= '</style>';
        }
        
        // Biến JS toàn trang
        global $cfg_date_checkin, $cfg_date_checkout;
        $head .= '<script>var vg_domain = "' . DOMAIN_WEB . '"; var vg_path_profile = "' . DOMAIN_WEB . '/profile/' . '"; var vg_checkin = "' . $cfg_date_checkin . '"; var vg_checkout = "' . $cfg_date_checkout . '";</script>';
        
        return $head;
    }
    
    /**
     * Layout::loadFooter()
     * Load cac noi dung o footer (Js...)
     * @return
     */
    function loadFooter() {
        global $cfg_theme_version_js;
        
        $js_folder = $_SERVER['DOCUMENT_ROOT'] . '/theme/js/';
        $js_files = glob($js_folder . '*.js');
        $footer = '';
        
        if (!empty($js_files)) {
            foreach ($js_files as $file) {
                $filename = basename($file, '.js');
                $js_path = $this->path_theme . 'js/' . $filename . '.js';
                $version = isset($cfg_theme_version_js[$filename]) ? '?v=' . $cfg_theme_version_js[$filename] : '?v=' . time();
                $footer .= '<script src="' . $js_path . $version . '"></script>' . PHP_EOL;
            }
        }
        
        // Load JS Map
        $footer .= $this->loadJSMap();
        
        // Load toastr JS
        $footer .= toastr();
        
        return $footer;
    }
    
    /**
     * Layout::loadJSGA()
     * Load JS GA
     * @return
     */
    private function loadJSGA() {
        
        $js =   '';
        
        return $js;
    }
    
    
    /**
     * Layout::loadJSFB()
     * Load FB chat
     * @return
     */
    function loadJSFB() {
        $js =   '<!-- Facebook Pixel Code -->
                <script>
                !function(f,b,e,v,n,t,s)
                {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version="2.0";
                n.queue=[];t=b.createElement(e);t.async=!0;
                t.src=v;s=b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t,s)}(window,document,"script",
                "https://connect.facebook.net/en_US/fbevents.js");
                 fbq("init", "3051888008360813"); 
                fbq("track", "PageView");
                </script>
                <noscript>
                 <img height="1" width="1" 
                src="https://www.facebook.com/tr?id=3051888008360813&ev=PageView
                &noscript=1"/>
                </noscript>
                <!-- End Facebook Pixel Code -->' . PHP_EOL;
        
        return $js;
    }

    /**
     * Layout::loadJSMap()
     * 
     * @return
     */
    function loadJSMap() {
        return '<script src="https://maps.googleapis.com/maps/api/js?key='. 123 .'&libraries=places&region=VN&language=vi" defer ></script>';
    }
}
?>