<?php

use src\Libs\Utils;

/**
 * get_env_domain()
 * Lấy domain để check xem đang sử dụng Admin hay User
 */
function get_env_domain() {
    $hostname   =   isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ''; // Lấy hostname
    $hostname   =   (isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http') . '://' . $hostname;
    return $hostname;
}

/**
 * Môi trường Admin, là CRM nội bộ của Sennet
 */
function is_admin() {
    if (get_env_domain() == DOMAIN_CRM) return true;
    return false;
}

/**
 * Môi trường User, là các tính năng của User sử dụng: HMS, TA, Booking...
 */
function is_user() {
    if (get_env_domain() == DOMAIN_USER) return true;
    return false;
}

/**
 * is_dev()
 * Môi trường Development
 * @return boolean
 */
function is_dev() {
    return Utils::isDev();
}


/**
 * is_pro()
 * Môi trường Production
 * @return boolean
 */
function is_pro() {
    return Utils::isPro();
}

/**
 * Generate ra câu SQL để chỉ xử lý dữ liệu của COMPANY_ID, tránh bị lẫn dữ liệu các cty
 */
function sql_company($prefix) {
    return " AND {$prefix}company_id = " . Utils::companyID();
}

/**
 * Generate ra câu SQL để chỉ xử lý dữ liệu của HOTEL_ID, tránh bị lẫn dữ liệu các khách sạn
 */
function sql_hotel($prefix) {
    global  $Auth;
    if ($Auth->isHMS()) return " AND {$prefix}hotel_id = " . Utils::hotelID();
    return "";
}

/**
 * Check cảnh báo các câu SQL liên quan đến data của các công ty mà bị thiếu trường where company_id = COMPANY_ID, hotel_id = HOTEL_ID
 */
function warning_table_limit($sql, $object = 'company') {
    //Mảng chứa danh sách các bảng cần check theo từng đối tượng
    global $cfg_table_must_check;
    if (is_user() && is_dev() && !empty($cfg_table_must_check[$object])) {
        foreach ($cfg_table_must_check[$object] as $table) {
            //dump($sql . '. Table: ' . $table . '. Position: ' . strpos($sql, $table));
            if (strpos($sql, $table) > 0) {
                if (strpos($sql, $object . '_id') <= 0) {
                    dd("SQL thiếu WHERE " . $object . " ID: \n" . $sql);
                }
            }
        }
    }
}

/**
 * Lấy base URL của site
 */
function base_url()
{
    return sprintf(
        "%s://%s/",
        (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] != 'off')) || (isset($_SERVER["HTTP_X_FORWARDED_PROTO"]) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') ? 'https' : 'http',
        ($_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] != 80 ? ':' . $_SERVER['SERVER_PORT'] : ''))
    );
}

/**
 * getValue()
 * Lấy giá trị của 1 input
 *
 * @param mixed  $value_name:   Tên của input
 * @param string $data_type:    GET_INT, GET_STRING, GET_DOUBLE, GET_ARRAY
 * @param string $method:       GET_POST, GET_GET, GET_SESSION, GET_COOKIE
 * @param int    $default_value: Giá trị mặc định nếu ko có input $value_name
 * @param int    $remove_injection
 *
 * @return
 */
function getValue($value_name, $data_type = GET_INT, $method = GET_GET, $default_value = 0, $remove_injection = true)
{
    $value =   $default_value;
    
    switch ($method) {
        case GET_GET:
            if (isset($_GET[$value_name])) {
                $value = $_GET[$value_name];
            }
            break;

        case GET_POST:
            if (isset($_POST[$value_name])) {
                $value = $_POST[$value_name];
            }
            break;

        case GET_COOKIE:
            if (isset($_COOKIE[$value_name])) {
                $value = $_COOKIE[$value_name];
            }
            break;

        case GET_SESSION:
            if (isset($_SESSION[$value_name])) {
                $value = $_SESSION[$value_name];
            }
            break;
        
        case GET_JSON:
            $result = json_decode(file_get_contents('php://input'), true);
            if (isset($result[$value_name])) {
                $value = $result[$value_name];
            }
            break;

        default:
            if (isset($_GET[$value_name])) {
                $value = $_GET[$value_name];
            }
            break;
    }
    
    //Xử lý dữ liệu cho chuẩn
    switch ($data_type) {
        case GET_INT:
            $value  =   str_replace(',', '', $value);
            $value  =   (int)$value;
            if ("INF" == strval($value)) {  //Nếu số nằm ngoài phạm vi xử lý
                return 0;
            }
            break;

        case GET_STRING:
            $value  =   trim(strval($value));
            if ($remove_injection) $value = clear_injection($value);
            break;

        case GET_DOUBLE:
            $value  =   str_replace(',', '', (string) $value);
            $value  =   (double)$value;
            if ("INF" == strval($value)) {
                return 0;
            }
            break;

        case GET_ARRAY:
            $value  =   (array) $value;
            break;
    }

    return $value;
}


/**
 * get_url()
 * Get URL va loai tru cac param.
 *
 * @param mixed $remove_param
 * @param boo   $domain:      Co lay domain hay ko
 *
 * @return
 */
function get_url($remove_param = ['page'], $domain = false)
{
    //Lấy REQUEST_URI
    $url_full   =   $_SERVER['REQUEST_URI'];

    //Bẻ ký tự ? để lấy URL gốc
    $break      =   explode('?', $url_full);
    $url_return =   $break[0];
    $param      =   $_GET;

    //Loại bỏ param
    foreach ($remove_param as $p) {
        if (isset($param[$p])) {
            unset($param[$p]);
        }
    }

    if (!empty($param)) {
        $url_return .=  '?' . http_build_query($param);
        $url_return =   rawurldecode($url_return);
    }
    //Nếu có lấy domain
    if ($domain) {
        $url_return    =   substr(base_url(), 0, -1) . $url_return;
    }

    //return URL
    return $url_return;
}

/**
 * generate_param_text()
 * Replace dấu cách thành dấu + để truyền lên URL
 * @param mixed $string
 * @return
 */
function generate_param_text($string) {
    return str_replace(' ', '+', $string);
}

/**
 * generate_url_filter()
 * 
 * @param mixed $add_param
 * @param mixed $remove_param
 * @param string $url_full
 * @return
 */
function generate_url_filter ($add_param = [], $remove_param = [], $url_full = '') {
    
    //Mảng các param mặc định sẽ bị remove đi
    $remove_default =   [
                        'page',
                        'utm_web',
                        'utm_vg'
                        ];
    
    $is_uri =   false;
    $url    =   '';
    
    //Nếu ko truyền vào URL thì sẽ lấy theo URI và cần phải nối thêm domain vào đầu URL
    if ($url_full == '') {
        $url_full   =   $_SERVER['REQUEST_URI'];
        $is_uri     =   true;
    }
    
    //Bẻ ký tự ? để lấy URL gốc
    $break  =   explode('?', $url_full);
    $url    =   $break[0];
    $param  =   $_GET;
    
    //Loại bỏ param
    $remove =   array_merge($remove_default, $remove_param);
    foreach ($remove as $p) {
        if (isset($param[$p]))    unset($param[$p]);
    }
    
    //Nếu trường hợp $value là 1 array thì cần phải xử lý loại bỏ/thêm value của array vào param
    foreach ($add_param as $p => $value) {
        
        if (is_array($value)) {
            if (!empty($value)) {
                
                if (!isset($param[$p])) {
                    //Nếu chưa có thì khởi tạp array của param
                    $param[$p]  =   $value;
                } else {
                    
                    //Nếu đã có array của parram trên URL thì check tiếp: Đã có value của param rồi thì hủy đi, chưa có thì thêm vào
                    if (is_array($param[$p])) {
                        foreach ($value as $v) {
                            $key    =   array_search($v, $param[$p]);
                            if ($key !== false) {
                                unset($param[$p][$key]);
                            } else {
                                $param[$p][]    =   $v;
                            }
                            //unset($add_param[$p]);
                            if (!empty($param[$p])) {
                                $param[$p]  =   array_values($param[$p]);
                            }
                        }
                        
                    } else {
                        //Nếu chưa có (hoặc param ko phải là array) thì khởi tạo param
                        $param[$p]  =   $value;
                    }
                }
                //unset($add_param[$p]);
            }
            
        } else {
            //Unset param này đi để gán lại giá trị
            //if ($p == 'loai-tour')  exit($param[$p] . '-' . $value);
            if (isset($param[$p]) && $param[$p] == $value) {
                //unset($add_param[$p]);
                unset($param[$p]);
                //$param[$p]  =   $value;
            } else {
                $param[$p]  =   $value;
            }
        }
    }
    //dump($param);
    //dump($add_param);
    //Thêm các param mới
    //$param  =   array_merge($param, $add_param);
    if (!empty($param)) {
        $url    .=  '?' . http_build_query($param);
        $url    =   rawurldecode($url);
    }
    
    //Nếu chưa phải là Full URL (Bao gồm cả domain) thì nối thêm domain vào
    if($is_uri) $url =   DOMAIN_WEB . $url;
    
    //return URL
    return $url;
}

/**
 * get_url_symbol_query()
 * Lay ky tu de noi query cua URL.
 *
 * @param mixed $url
 *
 * @return ? OR & OR ''
 */
function get_url_symbol_query($url)
{
    $symbol =   '';

    if (false !== strpos($url, '?')) {
        if ('?' != substr($url, -1) && '&' != substr($url, -1)) {
            $symbol =   '&';
        }
    } else {
        $symbol =   '?';
    }

    return $symbol;
}

/**
 * str_totime()
 * Convert time tu String sang Integer
 * @param string $string dd/mm/YYYY [H:i:s]
 * @return integer
 */
function str_totime($string = ''){
    
    $time_return    =   0;
    
    $string  =  trim($string);
	if($string == '')  return $time_return;
    
    $string =   str_replace('-', '/', $string);
    // Làm sạch chuỗi thời gian " / " -> "/"
    $string =   preg_replace("/\s*\/\s*/", '/', $string);
	
    //Bẻ dấu cách trong trường hợp có thêm giờ (dd/mm/YYY H:i:s)
    $arr_string     =   explode(' ', $string);
    $string_date    =   $arr_string[0];
    $string_hour    =   isset($arr_string[1]) ? $arr_string[1] : '';
    
    //Bẻ chuỗi ngày
    $arr_date   =   explode('/', $string_date); 
    
	if(count($arr_date) == 3){
        $day    =   (int)$arr_date[0];
        $month  =   (int)$arr_date[1];
        $year   =   (int)$arr_date[2];
        
        //Kiểm tra ngày hợp lệ thì convert
        if (checkdate($month, $day, $year)) {
			$time_return =   strtotime($month . '/' . $day . '/' . $year . ' ' . $string_hour);
		}
	}
	
	return intval($time_return);
}


/**
 * generate_time_from_date_range()
 * Generate ra integer time tu daterangepicker.
 * @param mixed $date_range
 * @return ['from' => from, 'to' => to]
 */
function generate_time_from_date_range($date_range, $end_day = true)
{
    $time_from  =   0;
    $time_to    =   0;

    $exp    =   explode('-', $date_range);
    if (isset($exp[0]) && isset($exp[1])) {
        $time_from  =   str_totime($exp[0]);
        $time_to    =   str_totime($exp[1]);
    }

    //Nếu lấy đến cuối ngày thì phải cộng với 86399
    if ($end_day) {
        $time_to    =   strtotime(date('m/d/Y', $time_to)) + 86399;
    }

    return  [
        'from'  => (int) $time_from,
        'to'    => (int) $time_to,
    ];
}


/**
 * clear_injection()
 * 
 * @param mixed $text
 * @return
 */
function clear_injection($text){
	
    $text =   str_replace("\\", "", $text);
    $text =   str_replace("\'", "'", $text);
    $text =   str_replace("'", "''", $text);
    
    //Remove ký tự gạch ngang về ký tự chuẩn vì có nhiều cái copy từ Word ra sẽ bị thành ký tự dài hơn
    $text =   str_replace('–', '-', $text);
    
	return $text;
}
/**
 * removeInjection()
 * Remove cac ki tu injection
 * @param mixed $text
 * @return string
 */
function removeInjection($text){
	$text	= str_replace("\'", "'", $text);
	$text	= str_replace("'", " ", $text);
    $text	= str_replace(";", " ", $text);
    $text	= str_replace("=", " ", $text);
    
	return trim($text);
}

/** --- Close or Reload when close thickbox --- **/
function close_tb_window($remove_el = '')
{
    $str    =   '<script type="text/javascript">';

    //Nếu có xóa element của parent
    if ('' != $remove_el) {
        $str .= 'var el_remove = parent.document.getElementById("' . $remove_el . '");
                    el_remove.parentNode.removeChild(el_remove);';
    }

    $str .= 'window.parent.tb_remove();
                </script>';
    echo    $str;
    exit();
}

/**
 * reload_parent_window()
 * Reload parent
 * @param string $el
 * @return void
 */
function reload_parent_window($el = '')
{
    echo '<script type="text/javascript">
            parent.location.href = parent.location.href' . ($el != '' ? ' + "?' . $el . '"' : '') . ';
         </script>';
    exit();
}

/**
 * removeAccent()
 * Replace cac ky tu Tieng Viet thanh ko dau
 * @param mixed $str
 * @return string Chuoi ko dau
 */
function removeAccent($str){
    
    //Một số âm lặp 2 lần ("ê", "ô", "ị"...) vì là 2 bộ gõ khác nhau
    
	$marSearch	=	["à","á","ạ","ả","ã","â","ầ","ấ","ậ","ẩ","ẫ","ă","ằ","ắ","ặ","ẳ","ẵ","â",
					"è","é","ẹ","ẻ","ẽ","ê","ề","ế","ệ","ể","ễ","ê",
					"ì","í","ị","ỉ","ĩ","ị",
					"ò","ó","ọ","ỏ","õ","ô","ồ","ố","ộ","ổ","ỗ","ơ","ờ","ớ","ợ","ở","ỡ","ồ","ò",
					"ù","ú","ụ","ủ","ũ","ư","ừ","ứ","ự","ử","ữ",
					"ỳ","ý","ỵ","ỷ","ỹ",
					"đ",
					"À","Á","Ạ","Ả","Ã","Â","Ầ","Ấ","Ậ","Ẩ","Ẫ","Ă","Ằ","Ắ","Ặ","Ẳ","Ẵ",
					"È","É","Ẹ","Ẻ","Ẽ","Ê","Ề","Ế","Ệ","Ể","Ễ",
					"Ì","Í","Ị","Ỉ","Ĩ",
					"Ò","Ó","Ọ","Ỏ","Õ","Ô","Ồ","Ố","Ộ","Ổ","Ỗ","Ơ","Ờ","Ớ","Ợ","Ở","Ỡ",
					"Ù","Ú","Ụ","Ủ","Ũ","Ư","Ừ","Ứ","Ự","Ử","Ữ",
					"Ỳ","Ý","Ỵ","Ỷ","Ỹ",
					"Đ",
					"'"];
	$marReplace	=	["a","a","a","a","a","a","a","a","a","a","a","a","a","a","a","a","a","a",
					"e","e","e","e","e","e","e","e","e","e","e","e",
					"i","i","i","i","i","i",
					"o","o","o","o","o","o","o","o","o","o","o","o","o","o","o","o","o","o","o",
					"u","u","u","u","u","u","u","u","u","u","u",
					"y","y","y","y","y",
					"d",

					"A","A","A","A","A","A","A","A","A","A","A","A","A","A","A","A","A",
					"E","E","E","E","E","E","E","E","E","E","E",
					"I","I","I","I","I",
					"O","O","O","O","O","O","O","O","O","O","O","O","O","O","O","O","O",
					"U","U","U","U","U","U","U","U","U","U","U",
					"Y","Y","Y","Y","Y",
					"D",
					""];
                    
	return str_replace($marSearch, $marReplace, $str);
}


/**
 * to_slug()
 * Tạo ra một bí danh(alias) cho tên
 * @param mixed $str
 * @return alias
 */
function to_slug($str) {
    
    $str = trim(mb_strtolower($str));
    $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
    $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
    $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
    $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
    $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
    $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
    $str = preg_replace('/(đ)/', 'd', $str);
    
    //Riêng ký tự ' thì ko replace thành rỗng
    $str    =   str_replace("'", "-", $str);
    
    $str = preg_replace('/[^a-z0-9-\s]/', '', $str);
    $str = preg_replace('/([\s]+)/', '-', $str);
    
    for ($i = 1; $i <= 5; $i++) {
        $str    =   str_replace('--', '-', $str);
    }
    
    return $str;
}

/**
 * remove_special_char()
 * Remvoe các ký tự đặc biệt
 * @param mixed $string
 * @return
 */
function remove_special_char($string) {
    return  preg_replace('/[^a-zA-Z0-9\s]/', '', $string);
}

/**
 * generate_data_search()
 * Remove cụm text thành ko dấu để cho vào các trường search data
 * @param mixed $string
 * @return
 */
function generate_data_search($string) {
    $string =   mb_strtolower($string . ' ' . remove_special_char(removeAccent($string)));
    for($i = 0; $i <= 5; $i++) $string  =   str_replace ('  ', ' ', $string);
    return $string;
}

/**
 * removeHTML()
 * 
 * @param mixed $string
 * @param bool $keytab
 * @return
 */
function removeHTML($string, $keytab = true){
	$string = preg_replace ('/<script.*?\>.*?<\/script>/si', ' ', $string); 
	$string = preg_replace ('/<style.*?\>.*?<\/style>/si', ' ', $string); 
	$string = preg_replace ('/<.*?\>/si', ' ', $string); 
	$string = str_replace ('&nbsp;', ' ', $string);
	$string = mb_convert_encoding($string, "UTF-8", "UTF-8");
	if($keytab) $string = str_replace (array(chr(9),chr(10),chr(13)), ' ', $string);
    $string =   trim($string);
	for($i = 0; $i <= 5; $i++) $string = str_replace ('  ', ' ', $string);
    
	return $string;
}

/**
 * removeJS()
 * 
 * @param mixed $string
 * @return
 */
function removeJS($string) {
    $string =   preg_replace ('/<script.*?\>.*?<\/script>/si', ' ', $string);
    return $string;
}

/**
 * removeLink()
 * 
 * @param mixed $string
 * @return
 */
function removeLink($string, $keep_owner = true){
	$string =   preg_replace('#<a.*?>(.*?)</a>#is', '\1', $string);
    
	return $string;
}

/**
 * removeOutsideLink()
 * Remove link/ảnh dẫn đến các page khác
 * @param mixed $string
 * @return string
 */
function removeOutsideLink($string) {
    /**
     * $string =   preg_replace('#<a [^>]*\bhref=([\'"])http.?://((?<!vietgoing)[^\'"])+\1 *.*?>(.*?)</a>#i', '\3', $string);
    */
    $string =   preg_replace('#<img [^>]*\bsrc=([\'"])http.?://((?<!vietgoing)[^\'"])+\1 *.*?>#i', '', $string);
    
    return $string;
}

/**
 * Replace dau nhay trong textbox
 */
function replaceQuot($string){
    $string = (string) $string;
	$string = str_replace('\"', '"', $string);
	$string = str_replace("\'", "'", $string);
	$string = str_replace("\&quot;", "&quot;", $string);
	$string = str_replace("\\\\", "\\", $string);
   
	$arrSearch	= array('<', '>', '\"', '"');
	$arrReplace	= array('&lt;', '&gt;', '&quot;', '&quot;');
	$string = str_replace($arrSearch, $arrReplace, $string);
   
	return $string;
}

/**
 * replace_single_quotes()
 * Replace dấu nháy kép thành nháy đơn để dùng khi chèn tên các item vào schema...
 * @param mixed $str
 * @return string
 */
function replace_single_quotes($str) {
    return str_replace('"', '\"', $str);
}

/**
 * Ham remove cac ky hieu <,> cua tag HTML
 */
function htmlspecialTag($str){
	$arrSearch	= array('<', '>', '"');
	$arrReplace	= array('&lt;', '&gt;', '&quot;');
	$str = str_replace($arrSearch, $arrReplace, $str);
	return $str;
}

/**
 * Ham remove ky tu dac biet
 */
function replaceFCK($string, $type=0){
	$array_fck	= array ("&Agrave;", "&Aacute;", "&Acirc;", "&Atilde;", "&Egrave;", "&Eacute;", "&Ecirc;", "&Igrave;", "&Iacute;", "&Icirc;",
								"&Iuml;", "&ETH;", "&Ograve;", "&Oacute;", "&Ocirc;", "&Otilde;", "&Ugrave;", "&Uacute;", "&Yacute;", "&agrave;",
								"&aacute;", "&acirc;", "&atilde;", "&egrave;", "&eacute;", "&ecirc;", "&igrave;", "&iacute;", "&ograve;", "&oacute;",
								"&ocirc;", "&otilde;", "&ugrave;", "&uacute;", "&ucirc;", "&yacute;",
								);
	$array_text	= array ("À", "Á", "Â", "Ã", "È", "É", "Ê", "Ì", "Í", "Î",
								"Ï", "Ð", "Ò", "Ó", "Ô", "Õ", "Ù", "Ú", "Ý", "à",
								"á", "â", "ã", "è", "é", "ê", "ì", "í", "ò", "ó",
								"ô", "õ", "ù", "ú", "û", "ý",
								);
	if($type == 1) $string = str_replace($array_fck, $array_text, $string);
	else $string = str_replace($array_text, $array_fck, $string);
	return $string;
}

/**
 * round_number()
 * 
 * @param mixed $number
 * @return
 */
function round_number($number) {
   $value   =  round($number / 1000) * 1000;
   
   return $value;
}

/**
 * format_number()
 * 
 * @param mixed $number
 * @param integer $num_decimal
 * @param string $split
 * @return
 */
function format_number($number, $num_decimal = 2, $split = ",", $remove_decimal = true){

    $number = empty($number) ? 0 : $number;
    
    //Remove dấu , của số đi
    if ($remove_decimal) {
        $number =   floatval(str_replace(',', '', $number));
    }
    
	$break_thousands	=	$split;
	$break_retail		=	($split == "." ? "," : ".");
	$return    =   number_format($number, $num_decimal, $break_retail, $break_thousands);
	$stt	= -1;
	for($i = $num_decimal; $i > 0; $i--){
		$stt++;
		if(intval(substr($return, -$i, $i)) == 0){
			$return = number_format($number, $stt, $break_retail, $break_thousands);
			break;
		}
	}
	return $return;
}


/**
 * replace_keyword()
 * Xoa cac ky tu nguy hiem cua tu khoa search
 * @param mixed $keyword
 * @param integer $lower
 * @return string $keyword
 */
function replace_keyword($keyword, $lower = true){
	
    if ($lower) $keyword   =   mb_strtolower($keyword, "UTF-8");
    
    //Remove các ký tự phá ngoặc SQL    
	$keyword   =   clear_injection($keyword);
    
    //Các ký tự sẽ bị xóa khỏi keyword
    $arrRep     =   array("'", '"', "-", "+", "=", "*", "?", "/", "!", "~", "#", "@", "%", "$", "^", "&", "(", ")", ";", ":", "\\", ".", ",", "[", "]", "{", "}", "‘", "’", '“', '”');
    $keyword    =   str_replace($arrRep, " ", $keyword);
    
    //Xóa các dấu cách đôi thành dấu cách đơn
    for ($i = 1; $i < 5; $i++) {
        $keyword    =   str_replace("  ", " ", $keyword);
    }
    
	return trim($keyword);
}

/**
 * generate_array_keyword()
 * Tach keyword ra thanh mang chua cac tu khoa rieng le
 * @param string $keyword
 * @return $array_keyword = ['tu', 'khoa', 'tim', 'kiem']
 */
function generate_array_keyword($keyword = "", $max_word = 10){
	
    $array_keyword  =   [];
    
    /** --- Xóa các ký tự ko cho phép --- **/
    //$keyword    =   replace_keyword($keyword);
    
    //Tìm kiếm cả ko dấu
    $keyword_kodau  =   removeAccent($keyword);
	
    //Nếu chuỗi ko dấu khác với chuỗi gốc thì mới nối vào
    if ($keyword_kodau != $keyword) $keyword    =   $keyword . " " . $keyword_kodau;
	
    //Bẻ chuỗi
    $break  =   explode(' ', $keyword);
    $i  =   0;
    foreach ($break as $word) {
        $word   =   trim($word);
        //Chỉ tìm kiếm với các từ có từ 2 chữ cái trở lên
        if (mb_strlen($word, 'UTF-8') > 1) {
            $array_keyword[]    =   $word;
            $i++;
            //Từ khóa tìm kiếm tối đa là 10 thôi
            if ($i == 10)   break;
        }
    }
	
    return $array_keyword;
}

/**
 * cutstring()
 * Cat mot chuoi theo so luong ky tu
 * @param mixed $str
 * @param mixed $length
 * @param string $char: Ky tu noi them vao phan bi cat di
 * @return string
 */
function cutstring($str, $length, $char = "..."){
    $strlen =   mb_strlen($str, "UTF-8");
	if($strlen <= $length) return $str;
    
    $substr =   mb_substr($str, 0, $length, "UTF-8");
	$substr    =   trim($substr) . $char;
    
    return $substr;
}

/**
 * alert()
 * 
 * @param string $str
 * @return void
 */
function alert($str = ""){
   header('Content-Type: text/html; charset=utf-8');
   echo  '<script> alert("' . $str . '"); </script>';
}

/**
 * redirect_url()
 * Redirect URL binh thuong
 * @param mixed $url
 * @return void
 */
function redirect_url($url){
	header( "Location: " . $url);
    exit();
}

/**
 * redirect_parent()
 * Redirect URL khi đang mở thickbox
 * @param mixed $url
 * @return void
 */
function redirect_parent($url){
	echo    '<script>window.top.location.href = "' . $url . '";</script>';
    exit();
}


/**
 * redirect301()
 * Redirect URL 301
 * @param mixed $url
 * @return void
 */
function redirect301($url) {
    header( "Location: " . $url, 301);
    exit();
}

/**
 * redirect_correct_url()
 * Redirect các URL sai về URL đúng trong các trường hợp đối tượng bị thay đổi URL do đổi tên hoặc fake URL
 * @param mixed $url
 * @return void
 */
function redirect_correct_url($url) {
    if (!empty($_SERVER['REQUEST_URI'])) {
        
        //DOMAIN_WEB bắt buộc phải ko có '/' ở cuối
        $full_url   =   DOMAIN_WEB . $_SERVER['REQUEST_URI'];
        $exp    =   explode('?', $full_url, 2);
        if (trim($exp[0]) != trim($url)) {
            save_log('redirect_301.cfn', $full_url);
            redirect301($url . (isset($exp[1]) ? '?' . $exp[1] : ''));
        }
    }
}


/**
 * dump()
 * Dump data de test loi o local.
 *
 * @param mixed $data
 *
 * @return
 */
if (!function_exists('dump')) {
    function dump($data)
    {

        if (!is_dev()) {
            return false;
        }

        $name       = "";
        $back_track = debug_backtrace();
        $caller     = array_shift($back_track);
        foreach ($GLOBALS as $var_name => $value) {
            if ($value === $data) {
                $name = $var_name;
                break;
            }
        }

        echo '<pre style="position: relative;float: left; z-index: 99999; background: black; color: #FFF; width: 100%; max-height: 600px; overflow: auto; padding: 5px; border-top: 3px solid #d31a1a;">';
        echo '<span style="display:block; text-align: center; background: #D6D61F; font-weight: 600; color: #111;">DUMP IN (' . $caller['file'] . ' -- line: ' . $caller['line'] . ')</span>';
        //echo "<span style='display:block; text-align: center;font-weight: 600;padding: 4px 0px;color: #00B8FF;'>$" . $name . "</span>";

        switch (gettype($data)) {
            case "boolean":
            case "object":
                var_dump($data);
                break;

            case "array":
                print_r($data);
                break;

            default:
                echo $data;
                break;
        }

        echo '</pre>';
    }

}

/**
 * dd()
 * Dump and exit
 * @param mixed $data
 * @return void
 */
// if (!function_exists('dd')) {
//     function dd($data) {
//         dump($data);
//         exit;
//     }
// }

function dd2($data) {
    dump2($data);
    exit;
}

function dump2($data)
{

    if (!is_dev()) {
        return false;
    }

    $name       = "";
    $back_track = debug_backtrace();
    $caller     = array_shift($back_track);
    foreach ($GLOBALS as $var_name => $value) {
        if ($value === $data) {
            $name = $var_name;
            break;
        }
    }

    echo '<pre style="position: relative;float: left; z-index: 99999; background: black; color: #FFF; width: 100%; max-height: 600px; overflow: auto; padding: 5px; border-top: 3px solid #d31a1a;">';
    echo '<span style="display:block; text-align: center; background: #D6D61F; font-weight: 600; color: #111;">DUMP IN (' . $caller['file'] . ' -- line: ' . $caller['line'] . ')</span>';
    echo "<span style='display:block; text-align: center;font-weight: 600;padding: 4px 0px;color: #00B8FF;'>$" . $name . "</span>";

    switch (gettype($data)) {
        case "boolean":
        case "object":
            var_dump($data);
            break;

        case "array":
            print_r($data);
            break;

        default:
            echo $data;
            break;
    }

    echo '</pre>';
}



/**
 * get_current_page()
 * Lay trang hien tai
 * @param string $param
 * @return integer $current_page
 */
function get_current_page($param = 'page') {
    $current_page   =   getValue($param);
    if ($current_page < 1)  $current_page   =   1;
    if ($current_page > 9999)   $current_page   =   9999;
    
    return $current_page;
}

/**
 * generate_pagebar()
 * Generate HTML cua pagebar
 * @param integer $total_record
 * @param integer $page_size
 * @return
 */
function generate_pagebar($total_record, $page_size = 12, $param_remove = [], $show_total = true) {
    
    $html_page  =   '';
    
    if ($total_record > 0) {
        
        $total_page     =   ceil($total_record / $page_size);
        $page_current   =   get_current_page();
        
        $html_page  .=  '<div class="pagination moderm-pagination" id="moderm-pagination" data-layout="normal">';
        
        if ($total_page > 1) {
    
            $page_start =   $page_current - 2;
            if ($page_start < 1)    $page_start =   1;
    
            //Lấy URL đươc loại bỏ param page
            $url    =   get_url(array_merge($param_remove, ['page']));
            $symbol =   get_url_symbol_query($url);
            
            $html_page  .=   '<ul class="page-numbers">';
    
            //Nếu trang hiện tại > 1 thì mới hiện nút "Previous"
            if ($page_current > 1) {
                $html_page  .=  '<li class="page_first">
                                    <a href="' . $url . ($page_current > 2 ? $symbol . 'page=' . ($page_current - 1) : '') . '" class="page-numbers prev"><i class="fas fa-angle-left"></i></a>
                                </li>';
            }
            
            //2 Trang liền trước của trang hiện tại
            for ($i = $page_start; $i < $page_current; $i++) {
                $html_page  .=  '<li>
                                    <a href="' . $url . ($i >= 2 ? $symbol . 'page=' . $i : '') . '" class="page-numbers">' . $i . '</a>
                                </li>';
            }
            
            //Trang hiện tại
            $html_page  .=  '<li>
                                <a href="javascript:;" class="page-numbers current">' . $page_current . '</a>
                            </li>';
            
            $url    .=  $symbol;
            //2 Trang liền sau của trang hiện tại
            $next_2_page    =   $page_current + 2;
            if ($next_2_page > $total_page) $next_2_page  =   $total_page;
            for ($i = $page_current + 1; $i <= $next_2_page; $i++) {
                $html_page  .=  '<li>
                                    <a href="' . $url . 'page=' . $i . '" class="page-numbers">' . $i . '</a>
                                </li>';
            }
    
            //Nếu trang hiện tại nhỏ hơn tổng số trang thì mới hiện nút Next
            if ($page_current < $total_page) {
                $html_page  .=  '<li>
                    				<a href="' . $url . 'page=' . ($page_current + 1) . '" class="page-numbers next"><i class="fas fa-angle-right"></i></a>
                    			</li>';
            }
    
            $html_page  .=  '</ul>';
            
        }
        
        if ($show_total)    $html_page  .=  '<span class="count-string">Có ' . format_number($total_record) . ' kết quả được tìm thấy</span>';
        $html_page  .=  '</div>';
    }
    
    //Return HTML
    return $html_page;
}


/**
 * encode_base_json()
 * Encode mot array thanh chuoi Json
 * @param mixed $array
 * @return string json
 */
function encode_base_json($array = []) {
    
    if (!empty($array)) {
        $string  =  base64_encode(json_encode($array));
        return $string;
    }
  
    return '';
}


/**
 * decodeBaseJson()
 * Decode 1 chuoi json thanh array
 * @param mixed $string
 * @return array
 */
function decode_base_json($string) {
      
    if ($string != '') {
    
        $string  =  base64_decode($string);
        $return  =  json_decode($string, true);
        
        if (is_array($return)) {
            return $return;
        }
    }
    
    return [];
}


/**
 * validate_name()
 * Validate Ten
 * @param mixed $name
 * @return boolean
 */
function validate_name($name)
{

    $name   =   removeAccent($name);

    //Tối đa 32 ký tự
    if (trim($name) == '' || mb_strlen($name, 'UTF-8') > 32) {
        return false;
    }

    if (!preg_match("/^[\p{L}\s]*$/u", $name)) {
        return false;
    }

    return true;
}

/**
 * validate_email()
 * Validate email
 * @param mixed $email
 * @return boolean
 */
function validate_email($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    
    return true;
}

/**
 * validate_phone()
 * Validate so DT
 * @param mixed $phone
 * @return boolean
 */
function validate_phone($phone) {
    
    if (!preg_match("/^(\+)?([0-9 \.\-]{10,15})$/", $phone)) {
		return false;
	}
    
    return true;
}

/**
 * validate_password()
 * Validate mat khau
 * @param mixed $password
 * @param integer $min_char
 * @return Boolean
 */
function validate_password($password, $min_char = 6) {
    if (mb_strlen($password, 'UTF-8') < $min_char) {
        return false;
    }
    
    return true;
}


/**
 * convert_phone_number()
 * Convert số ĐT từ đầu 0 => 84 và ngược lại
 * @param mixed $phone
 * @param string $type: 0 OR 84
 * @return string $phone
 */
function convert_phone_number($phone, $type = '0') {
    
    $phone  =   clear_phone_number($phone);
    
    $phone  =   preg_replace('/^84/', '0', $phone);
    $phone  =   preg_replace('/^00/', '0', $phone);
    
    if ((string)$type == '84') {
        $phone  =   '84' . (int)$phone;
    }
    
    return $phone;
}

/**
 * clear_phone_number()
 * Clear các ký tự đặt biệt của số ĐT
 * @param mixed $phone
 * @return
 */
function clear_phone_number($phone)
{
    $char_remove    =   ['.', '-', ' ', '+'];
    foreach ($char_remove as $char) {
        $phone  =   str_replace($char, '', $phone);
    }
    $phone  =   clear_injection($phone);

    return $phone;
}

/**
 * fix_email()
 * Fix các email lỗi
 * @param mixed $email
 * @return array|string|string[]
 */
function fix_email($email) {
    
    $email  =   strtolower($email);
    $email  =   str_replace(' ', '', $email);
    $email  =   str_replace('@gmail.con', '@gmail.com', $email);
    $email  =   str_replace('@gmail.com.vn', '@gmail.com', $email);
    $email  =   str_replace('@gmial.com', '@gmail.com', $email);
    $email  =   str_replace('@gmali.com', '@gmail.com', $email);
    $email  =   str_replace('@gamil.com', '@gmail.com', $email);
    $email  =   str_replace('@gmaik.com', '@gmail.com', $email);
    
    return $email;
}

/**
 * get_microtime()
 * Lay thoi gian hien tai de tinh toan thoi gian load trang
 * @return
 */
function get_microtime() {
    list($usec, $sec) =  explode(" ", microtime());
    
    return ((float)$usec + (float)$sec);
}


/**
 * show_time_load()
 * Hiển thị thời gian load trang
 * @param mixed $time
 * @return
 */
function show_time_load($time) {
    return '<p class="time_load">Time: ' . $time . '</p>';
}

/**
 * get_extension()
 * Lay duoi file
 * @param mixed $filename
 * @return string
 */
function get_extension($filename){
    $ext =   substr($filename, strrpos($filename, ".") + 1);
    return	strtolower($ext);
}

/**
 * get_folder_of_image()
 * Lấy tên thư mục lưu ảnh của các đối tượng
 * @param mixed $item_id
 * @return 01/01/
 */
function get_folder_of_image($item_id) {
    
    /**
     * Chia thư mục ảnh theo ID của SP, có 2 cấp thư mục: Cấp 1 lấy phần nguyên ID chia cho 1000 rồi cộng thêm 1
     * Bên trong sẽ chia thành 100 thư mục con, tên thư mục lưu ảnh là số dư của ID chia cho 100
     */
    
    $folder_parent  =   ceil($item_id / FOLDER_PARENT);
    $folder_child   =   $item_id % FOLDER_CHILD;
    
    if ($folder_parent < 10)    $folder_parent  =   '0' . $folder_parent;
    if ($folder_child < 10)    $folder_child  =   '0' . $folder_child;
    
    $path   =   $folder_parent . '/' . $folder_child . '/';
    
    return $path;
}

/**
 * check_ratio_image()
 * 
 * @param mixed $width
 * @param mixed $height
 * @param string $ratio
 * @return
 */
function check_ratio_image($width, $height, $ratio = '3:2') {
    if (!empty($ratio)) {
        $exp    =   explode(':', $ratio);
        $w  =   (int)$exp[0];
        $h  =   (int)$exp[1];
        if ($h > 0) {
            $ratio_require  =   round($w / $h, 2);  //Tỷ lệ được tính ra số thập phân
            
            //Tính tỷ lệ thực tế của ảnh
            $ratio_actual   =   round($width / $height, 2);
            if (abs($ratio_actual - $ratio_require) > 0.005) {
                return false;
            }
        }
    }
    
    return true;
}

/**
 * @param int $bytes Number of bytes (eg. 25907)
 * @param int $precision [optional] Number of digits after the decimal point (eg. 1)
 * @return string Value converted with unit (eg. 25.3KB)
 */
function format_bytes($bytes, $precision = 2) {
    $unit = ["B", "KB", "MB", "GB"];
    $exp = floor(log($bytes, 1024)) | 0;
    return round($bytes / (pow(1024, $exp)), $precision).$unit[$exp];
}

/**
 * set_session_toastr()
 * Tạo ra biến session dùng cho toastr
 * @param string $name
 * @param string $value
 * @return alias
 */
function set_session_toastr($name = 'result', $value = 'success') {
    $_SESSION[$name]    =   $value;
}

/**
 * toastr()
 * Show toastr của JS
 * @param string $msg_success
 * @param string $msg_fail
 * @param string $session_name
 * @param string $session_value
 * @return
 */
function toastr($msg_success = 'Cập nhật thành công', $msg_fail = 'Cập nhật không thành công', $session_name = 'result', $session_value = 'success') {
    $toast  =   '';
    
    if (isset($_SESSION[$session_name])) {
        $toast  .=  '<script>';
        $toast  .=  'toastr.options = {"positionClass":"toast-bottom-right"};';
        if ($_SESSION[$session_name] == $session_value) {
            $toast  .=  'toastr.success("' . $msg_success . '");';
        } else {
            $toast  .=  'toastr.error("' . $msg_fail . '");';
        }
        $toast  .=  '</script>';
        unset($_SESSION[$session_name]);
    }
    
    return $toast;
}

/**
 * hotel_group_sort()
 * Sắp xếp record theo thứ tự vào gắn thêm tiền tố 
 * @param array $data
 * @param int $parent_id
 * @param string $str
 * @return
 */
function hotel_group_sort($data, $parent_id=0, $str=''){
    $result = array() ;
    foreach($data as &$item){
        if($item['hotg_parent_id'] == $parent_id){
                $item["hotg_name"] = $str .' '. $item["hotg_name"];
                $result[] = $item ;
                $result = array_merge($result, hotel_group_sort($data, $item['hotg_id'], ($str.'-')));
        }
        unset($item);
    }
    return $result ;
}

/**
 * generate_checkbox_icon()
 * Generate ra icon cua truong checkbox.
 *
 * @param mixed $value
 * @return str icon
 */
function generate_checkbox_icon($value)
{
    $icon   =   '<i class="' . (1 == $value ? 'fas fa-check-square' : 'far fa-square') . '"></i>';

    return $icon;
}

/**
 * generate_checkbox_icon()
 * Generate ra option chọn tháng của số năm truyền vào
 *
 * @param mixed $y_next
 * @param mixed $time
 * @return 
 */
function generate_optgroup_month($y_next = 4, $time = CURRENT_TIME) {
    $value_select = date('Y-n');
	//Tạm bỏ đi để cho đội content copy giá từ 2021
    //$y_now = date('Y', $time);
    $y_now  =   2022;
    $y_next =   $y_now + $y_next;
    
	//$m_now = date('m', $time);

	$html_char = [];
	for ($i = $y_now; $i <= $y_next; $i++) { 
		$html_char[] = "<optgroup label=\"Năm $i\">";
		for ($i2 = 1; $i2 <= 12; $i2++) {
            //if ($i == 2021 && $i2 < 12) continue;
			$html_char[] = "<option value=\"$i-$i2\"". ($value_select == "$i-$i2" ? 'selected' : '') ." >Tháng $i2/$i</option>";
		}
		//if($m_now != 1) $m_now = 1;
		$html_char[] = "</optgroup>";
	}
	return implode('', $html_char);
}


/**
 * generate_month_select()
 * 
 * @param string $current_month
 * @param integer $year_next
 * @return
 */
function generate_month_select($current_month = '', $year_next = 2) {
    if (empty($current_month))  $current_month  =   date('m/Y');
    $y_now      =   2023;
    $y_end      =   $y_now + $year_next;
    $html_char  =   [];
	for ($year = $y_now; $year <= $y_end; $year++) { 
		$html_char[] = "<optgroup label=\"Năm $year\">";
		for ($month = 1; $month <= 12; $month++) {
            if ($month < 10)    $month  =   '0' . $month;
			$html_char[] = "<option value=\"$month/$year\"". ($current_month == "$month/$year" ? 'selected' : '') ." >Tháng $month/$year</option>";
		}
		//if($m_now != 1) $m_now = 1;
		$html_char[] = "</optgroup>";
	}
	return implode('', $html_char);
}


/**
 * array_get()
 * Lấy value trong array theo key tryền vào
 * @param array $input Mảng chứa dữ liệu
 * @param string $key Tên index cần lấy trong mảng, lấy value trong mảng đa triều index cách nhau bằng dấu . (key.key2)
 * @param mixed $default Giá trị mặc định khi không tìm thấy index chỉ định
 * @return mixed Giá trị của index
 */
function array_get($input, $key = null, $default = null) {

    if (is_null($key)) {
        return $input;
    }

    $arr = explode('.', $key);
    foreach ($arr as $k) {
        $input = isset($input[$k]) ? $input[$k] : null;
    }

    if (is_null($input)) {
        return $default;
    }

    return $input;
}

/**
 * show_money()
 * 
 * @param mixed $money
 * @param string $symbol
 * @return
 */
function show_money($money, $symbol = '₫') {
    return format_number($money) . '' . $symbol;
}

/**
 * gen_star()
 * Generate ra html của star
 * @param mixed $star
 * @param bool $show_empty
 * @param string $class_css
 * @return
 */
function gen_star($star, $show_empty = '', $class_css = '') {
    
    $html   =   '<span class="line-stars st-stars ' . $class_css . '">';
    
    if ($star > 0) {
        $floor  =   floor($star);
        for ($i = 1; $i <= $floor; $i++) {
            $html   .=  '<i class="fa fa-star"></i>';
        }
        
        //Nếu số điểm là lẻ thì gán thêm star 1/2
        if ($floor < $star) {
            $html   .=  '<i class="fa fa-star-half-alt"></i>';
            $i++;
        }
    } else {
        $html   .=  $show_empty;
    }
    
    //Nếu vẫn chưa đủ star thì show thêm các star trắng
    /*
    if ($show_empty) {
        for ($j = $i; $j <= 5; $j++) {
            $html   .=  '<i class="fa fa-star grey"></i>';
        }
    }
    */
    
    $html   .=  '</span>';
    
    return $html;
}


/**
 * gen_star_2()
 * Generate ra star fake của từng KS
 * @param mixed $score
 * @param string $class_css
 * @return
 */
function gen_star_2($score, $class_css = '') {
    
    $html   =   '<span class="line-stars st-stars ' . $class_css . '">';
    
    $floor  =   floor($score);
    for ($i = 1; $i <= $floor; $i++) {
        $html   .=  '<i class="fas fa-star-of-life"></i>';
    }
    
    $html   .=  '</span>';
    
    return $html;
}

/**
 * gen_star_3()
 * 
 * @param mixed $score
 * @return
 */
function gen_star_3($score) {
    
    $html   =   "<ul class=\"icon-list icon-group booking-item-rating-stars\">";
    
    $floor  =   floor($score);
    for ($i = 1; $i <= $floor; $i++) {
        $html   .=  "<li><i class=\"fa  fa-star\"></i></li>";
    }
    
    $html   .=  "</ul>";
    
    return $html;
}

/**
 * gen_review_label()
 * 
 * @param mixed $score
 * @return
 */
function gen_review_label($score) {
    $score  =   ceil($score);
    global  $cfg_review_label;
    if (isset($cfg_review_label[$score])) {
        return $cfg_review_label[$score];
    }
    
    return '';
}

/**
 * get_client_ip()
 * Get Client IP address
 * @return string IP
 */
function get_client_ip()
{
    $ipaddress = 'N/A';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    
    $exp    =   explode(',', $ipaddress);
    
    return trim($exp[0]);
}

/**
 * is_google_ip()
 * Check xem IP có phải của Google bot ko
 * @return bool
 */
function is_google_ip() {
    
    $ip =   get_client_ip();
    $list   =   ['66.249'];
    $exp    =   explode('.', $ip);
    
    if (isset($exp[1])) {
        return in_array($exp[0] . '.' . $exp[1], $list);
    }
    
    return false;
}

/**
 * is_bot()
 * 
 * @return
 */
function is_bot() {
    if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|curl|dataprovider|search|get|spider|find|java|majesticsEO|google|yahoo|teoma|contaxe|yandex|libwww-perl|facebookexternalhit/i', $_SERVER['HTTP_USER_AGENT'])) {
        return true;
    }
    return false;
}

/**
 * generate_address()
 * Generate ra địa chỉ full
 * @param mixed $arr_address['address', 'ward', 'district', 'city']
 * @return string full address OR ['city', 'district', 'ward', 'address']
 */
function generate_address($arr_address, $return_string = false) {
    global  $DB;
    
    $input_address  =   array_merge(['address' => '', 'city' => 0, 'district' => 0, 'ward' => 0], $arr_address);
    
    $data_return    =   $return_string ? '' : ['city' => '', 'district' => '', 'ward' => '', 'address' => $arr_address['address']];
    
    /** Do địa chỉ của các bookign ko bắt buộc luôn phải có ward, district, city nên ko thể join mà phải check từng query **/
    
    if ($input_address['city'] > 0) {
        $row    =   $DB->query("SELECT cit_name
                                FROM city
                                WHERE cit_id = " . (int)$input_address['city'])
                                ->getOne();
        if (!empty($row)) {
            if ($return_string) {
                $data_return    =   $row['cit_name'];
            } else {
                $data_return['city']    =   $row['cit_name'];
            }
        }
    }
    
    if ($input_address['district'] > 0) {
        $row    =   $DB->query("SELECT dis_name
                                FROM district
                                WHERE dis_id = " . (int)$input_address['district'])
                                ->getOne();
        if (!empty($row)) {
            if ($return_string) {
                $data_return    =   $row['dis_name'] . ', ' . $data_return;
            } else {
                $data_return['district']    =   $row['dis_name'];
            }
        }
    }
    
    if ($input_address['ward'] > 0) {
        $row    =   $DB->query("SELECT war_name
                                FROM ward
                                WHERE war_id = " . (int)$input_address['ward'])
                                ->getOne();
        if (!empty($row)) {
            if ($return_string) {
                $data_return    =   $row['war_name'] . ', ' . $data_return;
            } else {
                $data_return['ward']    =   $row['war_name'];
            }
        }
    }
    
    //Nối thêm địa chỉ ở đầu
    if (!empty($arr_address['address']) && $return_string) {
        $data_return    =   $arr_address['address'] . ', ' . $data_return;
    }
    
    //Return
    return $data_return;
}

/**
 * generate_navbar()
 * Generate html cua breadcrum
 * @param mixed $arr_link
 * @return html
 */
function generate_navbar($arr_link, $hide_last = false) {
    
    $html   =   '<ul>';
    $html   .=  '<li>
                    <a href="' . DOMAIN_WEB . '" title="Trang chủ Vietgoing.com">Trang chủ</a>
                    <i class="fas fa-chevron-right"></i>
                </li>';
    if ($hide_last) array_pop($arr_link);
    
    if (!empty($arr_link)) {
        foreach ($arr_link as $item) {
            $html   .=  '<li>';
            if (!empty($item['link'])) {
                $html   .=  '<a href="' . $item['link'] . param_box_web(35, true) . '" title="' . $item['text'] . '">' . $item['text'] . '</a>';
            } else {
                $html   .=  '<span>' . $item['text'] . '</span>';
            }
            $html   .=  '<i class="fas fa-chevron-right"></i>';
            $html   .=  '</li>';
        }
        
    }
    $html   .=  '</ul>';
    return $html;
}


/**
 * response()
 * Mẫu response chung của API
 *
 * @param int $code Mã code thông báo
 * @param void $data Dữ liệu trả về cho client
 * @param bool $error Có phải chế độ in lỗi k
 * @param bool $exit Dừng process hay return
 *
 * @return void
 */
function response($data = [], $code = REQUEST_SUCCESS, $exit = true)
{
    $output = [
        "ResponseCode"    => $code,
        "RequestTime"     => CURRENT_TIME
    ];

    if ($code === REQUEST_ERROR) {
        $output["Errors"]   = $data;
    } else {
        $output["Data"]     = $data;
    }

    if ($exit) {
        // http_response_code($code);
        echo json_encode($output);
        exit;
    } else {
        return $output;
    }
}

/**
 * calculate_percent()
 * Tinh phan tram cua 2 so
 * @param mixed $number
 * @param mixed $total
 * @param integer $length
 * @return
 */
function calculate_percent($number, $total, $length = 2) {
    if ($total > 0) {
        return round($number * 100 / $total, $length);
    }

    return 0;
}

/**
 * Tính giá trị trung bình, VD như ADR
 * @param double $total: Số bị chia (VD Tổng số tiền)
 * @param int $number: Số chia (VD Số đêm) 
 */
function calculate_average($total, $number) {
    if ($number > 0)    return round($total/$number);
    return 0;
}

/**
 * getIntegerArrayID()
 * Generate ra mảng chứa các ID từ mảng ID ban đầu để tránh bị fake lỗi injection
 * @param mixed $array_id
 * @return [id]
 */
function getIntegerArrayID($array_id) {
    
    $ids    =   [];
    
    if (!empty($array_id)) {
        foreach ($array_id as $id) {
            $id =   (int)$id;
            if ($id > 0)    $ids[]  =   $id;    //Tránh lỗi dữ liệu do fake injection
        }
    }
    
    //Return
    return $ids;
}

/**
 * get_number()
 * 
 * @param mixed $value
 * @return
 */
function get_number($value)
{
    return str_replace(',', '', $value);
}

function get_latlng_mapbox($address) {
    
    global  $cfg_website;
    
    $ch  = curl_init();
    $url = "https://maps.googleapis.com/maps/api/geocode/json?address=". urlencode($address) ."&key=". $cfg_website['cowe_key_map'];
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $response = json_decode($response, true);
    curl_close($ch); // Close the connection
    return ["lat"=> array_get($response, 'results.0.geometry.location.lat'),
            "lng"=> array_get($response, 'results.0.geometry.location.lng'),
            "address"=> array_get($response, 'results.0.formatted_address')
            ];
}


/**
 * distance()
 * Tính khoảng cách giữa 2 điểm theo lat,lon
 * @param mixed $lat1
 * @param mixed $lon1
 * @param mixed $lat2
 * @param mixed $lon2
 * @param string $unit
 * @return
 */
function distance($lat1, $lon1, $lat2, $lon2, $unit = 'K') {

    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);

    if ($unit == "K") {
        return ($miles * 1.609344);
    } else if ($unit == "N") {
        return ($miles * 0.8684);
    } else {
        return $miles;
    }
}

/**
 * showDistanceText()
 * Show khoảng cách theo text
 * @param mixed $km
 * @return
 */
function showDistanceText($km) {
    if ($km < 1) {
        return round($km * 100, 0) * 10 .'m';
    }
    return round($km, 1) .'km';
}

/**
 * pushNotifyTelegram()
 * Push thong bao co don/request moi vao group Tele
 * @param mixed $msg
 * @return
 */
function pushNotifyTelegram($msg, $icon="%F0%9F%94%94", $chat_id='-610722536') {
    
    if (is_dev()) return;
    
    $msg    =   'icon ' . $msg;
    
    $curl   =   curl_init();
    $params =   [
        'text'      =>  $msg,
        'chat_id'   =>  $chat_id
    ];
    $params =   str_replace('icon', $icon, http_build_query($params));

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.telegram.org/bot5898523939:AAGIsFvRr5FEaPD7aJg-n0W-EbLbesCUJ_A/sendMessage?' . $params,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
    ));
    
    $response   =   curl_exec($curl);
    //save_log('curl_notify.cfn', $response);
    curl_close($curl);
}

/**
 * is_combo()
 * 
 * @param mixed $tour_group
 * @return
 */
function is_combo($tour_group) {
    if ($tour_group == CATE_TOUR_COMBO)  return true;
    return false;
}

/**
 * param_box_web()
 * Generate ra array chua param box website de gan vao URL
 * @param mixed $box_id
 * @return [PARAM_WEB]
 */
function param_box_web($box_id, $return_string = false, $symbol = '?') {
    
    if (is_bot()) {
        return;
    }
    
    if ($return_string) {
        return $symbol . PARAM_WEB . '=' . $box_id;
    }
    
    return [PARAM_WEB => $box_id];
}

/**
 * is_vg()
 * Check xem co phai visit cua nv VG ko
 * @return
 */
function is_vg() {
    global  $User, $cfg_website;
    if (
        $User->isVGStaff()
        || (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] == $cfg_website['cowe_my_ip'])
        || (isset($_SESSION['source_reffer']) && $_SESSION['source_reffer'] == 'cms')
        ) {
        return true;
    }
    return false;
}

/**
 * set_click_box_website()
 * Set click cho box cua website
 * @return void
 */
function set_click_box_website() {
    //Check nhiều if để lọc dần, giảm việc phải check đồng thời nhiều condition ko cần thiết
    if (isset($_GET[PARAM_WEB])) {
        $ads_id =   (int)$_GET[PARAM_WEB];
        if ($ads_id > 0 && !is_bot() && !is_vg()) {
            if (!isset($_SESSION[PARAM_WEB]) || $ads_id != $_SESSION[PARAM_WEB]) {
                global  $DB;
                $DB->execute("UPDATE box_website SET box_count_click = box_count_click + 1 WHERE box_id = $ads_id LIMIT 1");
                //Set session
                $_SESSION[PARAM_WEB]    =   $ads_id;
            }
        }
    }
}

/**
 * set_visit_page()
 * Count visit of website
 * @param mixed $page_id
 * @return void
 */
function set_visit_page($page_id) {
    if (!is_bot() && !is_vg()) {
        /** Lưu thẳng vào MySQL **/
        
        //Có một số case mà ở 1 page sẽ lưu visit cho 2 page (VD như trang list hotel, lưu cả Hotel List Visit và Hotel Near By) nên cần check page_id truyền vào là 1 ID hay array
        if (gettype($page_id) == 'array') {
            $sql_where  =   " IN(" . implode(',', $page_id) . ") LIMIT " . count($page_id);
        } else {
            $sql_where  =   " = " . $page_id . " LIMIT 1";
        }
        
        //Check theo Base URL, nếu khác Base URL thì mới tính visit để tránh bị tính cả những visit mà sử dụng bộ lọc, sort...
        $uri    =   '';
        if (!empty($_SERVER['REQUEST_URI'])) {
            $uri    =   explode('?', $_SERVER['REQUEST_URI'])[0];
        }
        if (empty($_SESSION['page_visit']) || $uri != $_SESSION['page_visit']) {
            global  $DB;
            $DB->execute("UPDATE box_website SET box_count_click = box_count_click + 1 WHERE box_id $sql_where");
            $_SESSION['page_visit'] =   $uri;
        }
    }
}

/**
 * get_key_time_view()
 * Generate ra key cua array dung de render ra chart theo time va type truyen vao
 * @param mixed $time: integer
 * @param integer $type: 1: Theo ngay, 2: Theo tuan, 3: Theo thang
 * @return void integer $key
 */
function get_key_time_view($time, $type = 1) {
    
    $key    =   0;
    
    switch ($type) {
        //Xem theo ngày
        case 1:
            $key    =   strtotime('today', $time);
            break;
        
        //Xem theo tuần
        case 2:
            $key    =   strtotime('monday this week', $time);
            break;
            
        //Xem theo tháng
        case 3:
            $key    =   strtotime('first day of this month 00:00:00', $time);
            break;
        
        default:
            $key    =   strtotime('today', $time);
            break;
    }
    
    return $key;
}

/**
 * get_label_item_chart()
 * Lấy label của chart khi xem theo ngày tuần tháng
 * @param mixed $time
 * @param integer $type
 * @return
 */
function get_label_item_chart($time, $type = 1) {
    
    $label  =   'N/A';
    
    switch ($type) {
        //Xem theo ngày
        case 1:
            $label  =   date('d/m', $time);
            break;
        
        //Xem theo tuần
        case 2:
            $date   =   strtotime('monday this week', $time);
            $label  =   date('d', $date) . '-' . date('d/m', $date + 86400 * 7 - 1);
            break;
            
        //Xem theo tháng
        case 3:
            $label  =   date('m/Y', $time);
            break;
        
        default:
            $label  =   date('d/m', $time);
            break;
    }
    
    return $label;
}


/**
 * restruct_json_encoded()
 * Remove chuoi json bi loi thanh chuoi dung
 * @param mixed $string_json
 * @return string json
 */
function restruct_json_encoded($string_json)
{
    $string_json    =   str_replace('"[{"id"', '[{"id"', $string_json);
    $string_json    =   str_replace('"}]"', '"}]', $string_json);

    return $string_json;
}

// Lấy tẩt cả ngày trong khoảng thời gian
function create_date_range_array($iDateFrom,$iDateTo)
{
    $aryRange = [];

    array_push($aryRange, $iDateFrom);
    if ($iDateTo > $iDateFrom) {
        do{
            $iDateFrom += 86400; // add 24 hours
            array_push($aryRange, $iDateFrom);
        } while ($iDateFrom<$iDateTo);
    }
    return $aryRange;
}

/**
 * show_label_color()
 * 
 * @param mixed $value: Mã màu (0, 1, 2, 3, 4)
 * @param mixed $text: Text sẽ hiển thị
 * @return
 */
function show_label($value, $text) {
    global  $cfg_label_color;
    return  '<span class="badge ' . $cfg_label_color[$value] . '">' . $text . '</span>';
}

/**
 * show_label_request()
 * 
 * @param mixed $value: Trạng thái rq
 * @param mixed $text: Text sẽ hiển thị
 * @return
 */
function show_label_request($value, $text) {
    global  $cfg_request_color;
    return  '<span class="badge ' . $cfg_request_color[$value] . '">' . $text . '</span>';
}

/**
 * Đánh dấu là khách cũ để chú ý trong xử lý
 */
function show_label_return($value) {

    if ($value == SOURCE_RETURN) {
        return '<span class="badge bg-lime-gray lable_return">Khách cũ</span>';
    }
    
    return '';
}

/**
 * Echo thông báo lỗi ra màn hình
 * @param string $msg: Ko bao gồm dấu chấm than ở cuối
 */
function exitError($msg) {
    echo    '<p style="text-align:center;margin-top:30px;">' . $msg . '!</p>';
    exit();
}

/**
 * gen_hour_minute_second()
 * Convert số giây ra thành Giờ:Phút:Giây
 * @param mixed $second
 * @return hh:mm:ss
 */
function gen_hour_minute_second($second) {
    $hour   =   floor($second/3600);
    $minute =   floor(($second - $hour*3600)/60);
    $second =   $second % 60;
    $str    =   ($hour < 10 ? '0' : '') . $hour . ':' . ($minute < 10 ? '0' : '') . $minute . ':' . ($second < 10 ? '0' : '') . $second;
    return $str;
}

if (!function_exists('str_contains')) {
    function str_contains ($haystack, $needle) {
        return strstr($haystack, $needle) !== false;
    }
}

/**
 * Generate ra param của thickbox
 */
function param_thickbox($width = 1000, $height = 600) {
    return  'TB_iframe=true&width=' . $width . '&height=' . $height;
}

function gen_bed_info($width, $length) {
    return  ($width/100) . 'm x ' . ($length/100) . 'm';
}

function gen_sort_icon($field, $title_field) {
    
    //Lấy URL hiện tại
    $url    =   get_url(['page', 'fieldsort', 'sort']);
    $field_sort =   strtolower(getValue('fieldsort', GET_STRING, GET_GET, ''));
    $type_sort  =   strtolower(getValue('sort', GET_STRING, GET_GET, ''));
    if ($type_sort != 'asc' && $type_sort != 'desc')    $type_sort  =   'asc';

    //Ký tự nối param page (? hoặc &)
    $symbol =   '?';
    if (strpos($url, '?') > 0)  $symbol =   '&';
    $url    .=  $symbol;

    $href       =   $url .'fieldsort=' . $field . '&sort=' . ($type_sort == 'desc' && $field_sort == $field ? 'asc' : 'desc');
    $icon_sort  =   '<i class="fas fa-sort"></i>';
    $title_sort =   ' giảm dần';

    //Nếu đang sắp xếp theo field này thi mới hiển thị mũi tên lên hoặc xuống
    if ($field_sort == $field) {
        if ($type_sort == 'asc') {
            $icon_sort  =   '<i class="fas fa-sort-amount-up"></i>';
            $title_sort =   ' tăng dần';
        } else {
            $icon_sort  =   '<i class="fas fa-sort-amount-down"></i>';
        }
    }

    $html   =  '<a class="has_sort" href="' . $href . '" title="Sắp xếp ' . $title_field . $title_sort . '">' . $icon_sort . '</a>';
    return $html;
}

/**
 * replaceMQ()
 * 
 * @param mixed $text
 * @return
 */
function replaceMQ($text){
	
    $text =   str_replace("\\", "", $text);
    $text =   str_replace("\'", "'", $text);
    $text =   str_replace("'", "''", $text);
    
    //Remove ký tự gạch ngang về ký tự chuẩn vì có nhiều cái copy từ Word ra sẽ bị thành ký tự dài hơn
    $text =   str_replace('–', '-', $text);
    
	return $text;
}


// Check block ip
function isAllowedIP($ipClient, $allowedIPs, $register = false)
{
    // Nếu không phải môi trường production (dev hoặc local), luôn cho phép truy cập
    if (Utils::isDev() || Utils::isLocal()) {
        return true;
    }

    // Nếu là bản demo thì chặn hết
    // Bỏ chặn ở demo, cho dùng thoải mái
    // if (Utils::isDemo()) {
    //     // Chuyển danh sách IP được phép thành mảng và kiểm tra
    //     $allowedIPsArray = explode(';', $allowedIPs);
    //     return in_array($ipClient, $allowedIPsArray, true);
    // }

    // Nếu check ở register thì cả bản live và demo cũng check còn bỏ qua các trang khác cho vào tự do
    if((Utils::isPro() || Utils::isDemo()) && $register) {
        $allowedIPsArray = explode(';', $allowedIPs);
        return in_array($ipClient, $allowedIPsArray, true);
    }
    
    return true;
}