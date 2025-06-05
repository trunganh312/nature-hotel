<?

use src\Facades\DB;
use src\Services\CommonService;

include('config_module.php');
if (!$Auth->cto) {
    CommonService::resJson(['Bạn không có quyền sử dụng tính năng này!'], STATUS_INACTIVE, 'Error');
}

/** --- Khai báo một số biến cơ bản --- **/
$record_id      =   getValue('id');
$record_info    =   DB::pass()->query("SELECT * FROM users WHERE use_id = " . $record_id)->getOne();
if (empty($record_info)) {
    CommonService::resJson(['Dữ liệu này không tồn tại!'], STATUS_INACTIVE, 'Error');
}

if (!isset($_GET['date_range'])) {
    $_GET['date_range'] =   date('d/m/Y') . ' - ' . date('d/m/Y');
}
/** --- End of Khai báo một số biến cơ bản --- **/

/** --- DataTable --- **/
$Table  =   new DataTable('table', 'id');
$Table->column('date', 'Thời gian')
        ->column('url', 'URL')
        ->column('ip', 'IP');


$Table->setFieldHidden(['id']);

$Table->addSearchData([
                        'date_range'    =>  ['label' => 'Thời gian', 'type' => TAB_DATE, 'query' => false]
                        ]);

$date_range =   getValue('date_range', GET_STRING, GET_GET, '');

//Lấy log
$data   =   $Log->getLogAccess($record_id, $date_range);
$Table->getSQLSearch();

// Chỉnh sửa dữ liệu trước khi trả về
$data = array_map(function($item) {
    $item['date']   =   date('d/m/Y H:i:s', $item['loga_time']);
    return $item;
}, $data);


$res = [
    'rows' => $data,
    ''
];


CommonService::resJson($res, STATUS_ACTIVE, 'Success');