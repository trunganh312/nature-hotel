<?

use src\Models\Hotel;
use src\Models\HotelPicture;

include('../../../Core/Config/require_web.php');

$id = getValue('id');

// lấy thông tin khách sạn
$hotel = Hotel::where('hot_id', $id)->getOne();

// Lấy ảnh KS
$hotel_picture = HotelPicture::where('hopi_hotel_id', $id)->toArray();
$images = [];

foreach ($hotel_picture as $picture) {
    // Lấy 5 ảnh đầu k theo id và các ảnh sau
    $images[] = $Router->srcHotel($id, $picture['hopi_picture']);
}
$image_1 = $images[0] ?? $cfg_default_image;
$image_2 = $images[1] ?? $cfg_default_image;
$image_3 = $images[2] ?? $cfg_default_image;
$image_4 = $images[3] ?? $cfg_default_image;
$image_5 = $images[4] ?? $cfg_default_image;

?>