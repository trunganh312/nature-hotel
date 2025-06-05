<?

use src\Services\CommonService;

/**
 * Class Layout
 * version 1.0
 */

class Layout
{

    public $path_theme;
    private $popup  =   false;
    private $page_title =   'Vietgoing';
    private $note_title =   '';
    private $title_button   =   []; //Các nút hiển thị trên title ở góc phải (VD nút Thêm mới)
    
    /**
     * Layout::__construct()
     * 
     * @return void
     */
    function __construct()
    {
        $this->path_theme   =   DOMAIN_USER . '/theme/';
    }
    
    /**
     * Layout::setPopup()
     * 
     * @param mixed $bool
     * @return void
     */
    function setPopup($bool) {
        $this->popup    =   $bool;
        return $this;
    }
    
    /**
     * Layout::setTitleButton()
     * Add thêm các button/link ở góc phải trên title của page
     * @param mixed $button: string OR array
     * @return void
     */
    function setTitleButton($button) {
        $this->title_button =   $button;
        return $this;
    }
    
    /**
     * Layout::loadHead()
     * Load title, cac file CSS... cua the <head>
     * @param string $title
     * @param string $add_more string. VD: <link rel="stylesheet" href="file.css" />
     * @return string html cua the <head>
     */
    function loadHead($title = 'SENNET System', $add_more = '')
    {
        global  $cfg_theme_version_css, $cfg_theme_version_js;

        //Load CSS
        $css    =   ['all', 'daterangepicker', 'summernote', 'thickbox', 'select2', 'select2-bootstrap4.min', 'toastr', 'adminlte', 'table', 'style', 'font', 'waitMe.min', 'jquery-ui', 'ant-design-vue-reset', 'jquery-autocomplete'];

        $head   =  '<title>' . $title . '</title>
                    <meta charset="UTF-8" />
                    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, initial-scale=1.0, user-scalable=no" id="viewport" />
                    <meta name="robots" content="NOINDEX" />
                    <link rel="icon" href="' . $this->getFavicon()  . '" type="image/x-icon" />';

        foreach ($css as $file) {
            $head   .=  '<link rel="stylesheet" href="' . $this->path_theme . 'css/' . $file . '.css' . (isset($cfg_theme_version_css[$file]) ? '?v=' . $cfg_theme_version_css[$file] : '?v=' . CURRENT_TIME) . '" />';
        }
        
        //Load JS
        $js =   [
            'jquery', 'jquery-ui', 'bootstrap', 'jeditable', 'moment', 'inputmask', 'daterangepicker', 'countdown', 'summernote', 'select2.full.min', 'toastr', 'customfile', 'adminlte', 'thickbox', 'table', 'waitMe.min', 'main',
            'jquery-autocomplete'
        ];

        $head .=  '<script>var domain_user = "' . DOMAIN_USER . '";</script>';

        foreach ($js as $file) {
            $head   .=  '<script src="' . $this->path_theme . 'js/' . $file . '.js' . (isset($cfg_theme_version_js[$file]) ? '?v=' . $cfg_theme_version_js[$file] : '?v=' . CURRENT_TIME) . '"></script>';
        }

        // Nhập các file từ manifest.json (chứa các file đã được build ra từ webpack)
        $manifest = CommonService::decode(file_get_contents(PATH_ROOT . '/public/theme/bundle/manifest.json'));
        foreach ($manifest as $file) {
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            if ($extension === 'js') {
                $head .= '<script type="text/javascript" src="/theme/bundle/' . $file . '"></script>';
            } elseif ($extension === 'css') {
                $head .= '<link rel="stylesheet" href="/theme/bundle/' . $file . '">';
            }
        }

        //Nếu add thêm file
        $head   .=  $add_more;

        return $head;
    }

    /**
     * Layout::getFavicon()
     * Lay favicon theo tung environment de nhan dien, tranh bi test nham moi truong
     * @return
     */
    function getFavicon()
    {

        if (is_dev()) {
            return DOMAIN_USER . '/theme/img/favicon.png';
        }

        return DOMAIN_USER . '/favicon.png';
    }


    /**
     * Layout::titlePage()
     * 
     * @param mixed $page_title
     * @param mixed $add_link: String hoặc Array: Chen them doan link, button
     * @return HTML of title page
     */
    function titlePage($page_title, $add_link = [])
    {
        
        $html_title =   '<div class="container-fluid title_header">
        					<div class="row">
        						<div class="col-sm-4">
        							<h1 class="m-0 text-dark">' . $page_title . '</h1>
        						</div>';

        $html_title .=  '<div class="col-sm-8 add_link_more">';
        
        $html_title .=  '<ol class="breadcrumb float-sm-right">';
        
        if (gettype($add_link) == 'array') {
            foreach ($add_link as $link) {
                $html_title .=  '<li class="breadcrumb-item">
									' . $link . '
								</li>';
            }
        } else {
            $html_title .=  '<li class="breadcrumb-item">
								' . $add_link . '
							</li>';
        }

        $html_title   .=  '</ol>
						</div>';

        $html_title .=  '</div>
        				</div>';

        return $html_title;
    }
    
    /**
     * Layout::setNoteTitle()
     * Một số trang DS cần có thêm dòng ghi chú ở dưới title
     * @param mixed $note_title
     * @return void
     */
    function setNoteTitle($note_title)
    {
        $this->note_title   =   $note_title;
        return $this;
    }


    /**
     * Layout::loadFooter()
     * Load cac file JS footer
     * @param string $add_more string <script src="' . $this->path_theme . 'js/jquery.min.js"></script>
     * @return HTML of cac the script
     */
    function loadFooter($add_more = '')
    {
        
        $footer =  $add_more;
        
        //Load toastr JS
        $footer .=  toastr();
        
        return $footer;
    }
    
    /**
     * Layout::header()
     * Show ra content của phần header và sidebar
     * @return void
     */
    function header($page_title) {
        global  $Auth;
        $this->page_title   =   $page_title;
        
        echo    '<div class="wrapper">';
        if (!$this->popup) {
            include(PATH_ROOT . '/layout/inc_header.php');
        }
        
        echo    '<div class="content-wrapper">';
        if (!$this->popup) {
            echo    $this->titlePage($this->page_title, $this->title_button);
        }
        
        echo    '<div class="container-fluid main_content">';
        
        //Nếu có ghi chú của title
        if ($this->note_title != '') {
            echo    '<div class="row title_note">
						' . $this->note_title . '
                    </div>';
        }                
        
    }
    
    /**
     * Layout::footer()
     * Show ra content của footer
     * @param string $add_more: Chèn thêm đoạn HTML hoặc JS
     * @return void
     */
    function footer($add_more = '') {
        echo    '</div>
                </div>
                </div>';
        
        //Load các file JS
        echo    $this->loadFooter($add_more);
        
    }

    function loadCkeditor() {
        $js_paths = [
            "/theme/ckeditor/ckeditor.js",
            "/ckfinder/ckfinder.js",
            "/theme/ckeditor/init.js"
        ];
        global  $cfg_theme_version_js;
        
        foreach($js_paths as $file) {
            echo '<script src="' . $file . (isset($cfg_theme_version_js[$file]) ? '?v=' . $cfg_theme_version_js[$file] : '') . '"></script>';
        }
    }
}