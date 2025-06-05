<?

use src\Libs\Utils;
use src\Libs\Vue;

include '../Core/Config/require_crm.php';
$page_title =   Utils::env('BRAND_NAME');

//Quyền xem dashboard
$has_dashboard  =   $Auth->hasPermission('statistic_dashboard');
if ($has_dashboard) {
    $page_title =   'Dashboard ' . date('m/Y');
}

Vue::setTitle($page_title);
Vue::render('', 'admin');
