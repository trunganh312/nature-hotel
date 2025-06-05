<?php
use src\Services\CommonService;
use src\Facades\DB;
include('config_module.php');

// Kiểm tra quyền chỉnh sửa
$Auth->checkPermission('general_city_edit');

// Lấy dữ liệu từ request
$cit_id = isset($_POST['cit_id']) ? (int)$_POST['cit_id'] : null; // Ép kiểu về integer để tránh SQL injection
$cit_name = isset($_POST['cit_name']) ? addslashes($_POST['cit_name']) : ''; // Xử lý chuỗi để tránh SQL injection
$cit_name_other = isset($_POST['cit_name_other']) ? addslashes($_POST['cit_name_other']) : ''; // Xử lý chuỗi
$cit_image = isset($_POST['cit_image']) ? addslashes($_POST['cit_image']) : ''; // Xử lý chuỗi

// Kiểm tra dữ liệu đầu vào
if (!$cit_id || !$cit_name) {
    CommonService::resJson([
        'success' => 0,
        'message' => 'Thiếu thông tin bắt buộc (cit_id hoặc cit_name)'
    ]);
}

// Đường dẫn lưu ảnh
if (!file_exists($path_image_city)) {
    mkdir($path_image_city, 0777, true); // Tạo thư mục nếu chưa tồn tại
}

// Lấy thông tin thành phố hiện tại để xóa ảnh cũ nếu cần
$old_record = DB::pass()->query("SELECT * FROM cities WHERE cit_id = $cit_id")->getOne();

if (!$old_record) {
    CommonService::resJson([
        'success' => 0,
        'message' => 'Không tìm thấy thành phố với cit_id: ' . $cit_id
    ]);
}

// Xử lý upload ảnh nếu có
$imagePath = $cit_image; // Mặc định giữ URL cũ hoặc rỗng
if (isset($_FILES['cit_image']) && $_FILES['cit_image']['error'] === UPLOAD_ERR_OK) {
    $Upload = new Upload('cit_image', $path_image_city, 450, 450); // Giới hạn kích thước 450x450
    if ($Upload->error) {
        CommonService::resJson([
            'success' => 0,
            'message' => $Upload->error
        ]);
    }
    $imagePath = $Upload->new_name; // Lưu đường dẫn ảnh mới
    $imagePath = addslashes($imagePath); // Xử lý chuỗi để tránh SQL injection
}

// Cập nhật dữ liệu trong bảng cities
// Bỏ placeholder, chèn giá trị trực tiếp (đã được xử lý an toàn)
$result = DB::pass(false, true)->execute(
    "UPDATE cities SET cit_name = '$cit_name', cit_name_other = '$cit_name_other', cit_image = '$imagePath' WHERE cit_id = $cit_id"
);

if ($result >= 0) {
    // Xóa ảnh cũ nếu có ảnh mới
    if ($imagePath !== $old_record['cit_image'] && $old_record['cit_image'] && file_exists($path_image_city . $old_record['cit_image'])) {
        unlink($path_image_city . $old_record['cit_image']);
    }

    CommonService::resJson([
        'success' => 1,
        'message' => 'Cập nhật thành phố thành công'
    ]);
} else {
    CommonService::resJson([
        'success' => 0,
        'message' => 'Không thể cập nhật thành phố'
    ]);
}