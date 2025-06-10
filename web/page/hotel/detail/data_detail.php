<?

use src\Models\Hotel;
use src\Models\HotelPicture;
use src\Models\Room;
use src\Models\RoomPicture;

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

// Lấy danh sách hạng phòng
$rooms = Room::where('roo_hotel_id', $id)->toArray();
$rooms = array_map(function($room) use($Router, $cfg_default_image, $AttributeModel, $HotelModel) {
    $room_picture = RoomPicture::where('rop_room_id', $room['roo_id'])->toArray();
    $room_images = [];
    foreach ($room_picture as $picture) {
        $room_images[] = isset($picture['rop_picture']) ? $Router->srcRoom($room['roo_id'], $picture['rop_picture']) : $cfg_default_image;
    }
    $room['images'] = $room_images;
    $room['attrs'] = $AttributeModel->getAttributeOfId($room['roo_id'], GROUP_ROOM);
    $room['utilities'] = [];
    foreach ($room['attrs'] as $attr) {
        $room['utilities'] = array_merge($room['utilities'], array_values($attr['data']));
    }
    $room['attr_view']        = $HotelModel->showRoomView($room, true);
    $room['attr_bed']         = $HotelModel->showRoomBed($room, false);
    return $room;
}, $rooms);

$hotel_attribute    =   $AttributeModel->getAttributeOfId($hotel['hot_id'], GROUP_HOTEL);

?>