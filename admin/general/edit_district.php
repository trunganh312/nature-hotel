<?php
use src\Services\CommonService;
use src\Facades\DB;
use src\Models\City;

include('config_module.php');

// Kiểm tra quyền chỉnh sửa
$Auth->checkPermission('general_district_edit');

// Lấy dữ liệu từ request
$dis_id = isset($_POST['dis_id']) ? (int)$_POST['dis_id'] : null;
$dis_name = isset($_POST['dis_name']) ? addslashes($_POST['dis_name']) : '';
$dis_name_other = isset($_POST['dis_name_other']) ? addslashes($_POST['dis_name_other']) : '';
$dis_city = isset($_POST['dis_city']) ? (int)$_POST['dis_city'] : null;
$dis_image = isset($_POST['dis_image']) ? addslashes($_POST['dis_image']) : '';

// Kiểm tra dữ liệu đầu vào
if ($dis_id === null || $dis_id <= 0 || !$dis_name) {
    CommonService::resJson([
        'success' => 0,
        'message' => 'Thiếu thông tin bắt buộc (dis_id phải là số nguyên dương và dis_name không được rỗng)'
    ]);
    exit;
}

// Đường dẫn lưu ảnh
if (!file_exists($path_image_district)) {
    mkdir($path_image_district, 0777, true); 
}

// Lấy thông tin quận/huyện hiện tại để xóa ảnh cũ nếu cần
$old_record = DB::pass()->query("SELECT * FROM districts WHERE dis_id = $dis_id")->getOne();

if (!$old_record) {
    CommonService::resJson([
        'success' => 0,
        'message' => 'Không tìm thấy quận/huyện với dis_id: ' . $dis_id
    ]);
    exit;
}

// Xử lý upload ảnh nếu có
$imagePath = $dis_image; // Mặc định giữ URL cũ hoặc rỗng
if (isset($_FILES['dis_image']) && $_FILES['dis_image']['error'] === UPLOAD_ERR_OK) {
    $Upload = new Upload('dis_image', $path_image_district, 450, 450);
    if ($Upload->error) {
        CommonService::resJson([
            'success' => 0,
            'message' => $Upload->error
        ]);
        exit;
    }
    $imagePath = $Upload->new_name;
    $imagePath = addslashes($imagePath);
}

// Cập nhật dữ liệu trong bảng districts
$result = DB::pass(false, true)->execute(
    "UPDATE districts SET dis_name = '$dis_name', dis_name_other = '$dis_name_other', dis_city = $dis_city, dis_image = '$imagePath' WHERE dis_id = $dis_id"
);

// Lấy danh sách thành phố sử dụng model City
$list_city = City::orderBy('cit_name', 'ASC')->pluck('cit_id', 'cit_name');
$cities = [];
foreach ($list_city as $k => $v) {
    $cities[] = [
        "value" => $k,
        "label" => $v
    ];
}

if ($result >= 0) {
    // Xóa ảnh cũ nếu có ảnh mới
    if ($imagePath !== $old_record['dis_image'] && $old_record['dis_image'] && file_exists($path_image_district . $old_record['dis_image'])) {
        unlink($path_image_district . $old_record['dis_image']);
    }

    // Lấy thông tin quận/huyện sau khi cập nhật
    $updated_record = DB::pass()->query("SELECT * FROM districts WHERE dis_id = $dis_id")->getOne();
    $updated_record['dis_image_url'] = !empty($updated_record['dis_image']) ? $Router->srcDistrict($updated_record['dis_image']) : '';

    CommonService::resJson([
        'success' => 1,
        'message' => 'Cập nhật quận/huyện thành công',
        'data' => [
            'district' => $updated_record,
            'cities' => $cities 
        ]
    ]);
} else {
    CommonService::resJson([
        'success' => 0,
        'message' => 'Không thể cập nhật quận/huyện',
        'data' => [
            'cities' => $cities 
        ]
    ]);
}