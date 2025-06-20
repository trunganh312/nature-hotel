<?

use src\Models\NotifyTemplate;

include('../../Core/Config/require_crm.php');
require_once(PATH_CORE . '/Model/DataLevelModel.php');

//Đường dẫn lưu ảnh
$path_image_company     =   PATH_ROOT . '/static/image/company/';
$path_image_department  =   PATH_ROOT . '/static/image/department/';
$path_image_admin       =   PATH_ROOT . '/static/image/admin/';
$path_image_user        =   PATH_ROOT . '/static/image/user/';
$path_image_cccd        =   PATH_ROOT . '/static/image/cccd/';

//Resize avatar của member và department
$array_resize   =   [
    SIZE_MEDIUM =>  ['maxwidth' => 450, 'maxheight' => 450],
    SIZE_SMALL  =>  ['maxwidth' => 150, 'maxheight' => 150]
];

?>