<?
include('../Core/Config/require_web.php');
exit('Vietgoing.com');
$list   =   'room_price_01231
room_price_01232
room_price_02231
room_price_02232
room_price_03231
room_price_03232
room_price_04221
room_price_04222
room_price_04231
room_price_04232
room_price_05221
room_price_05222
room_price_05231
room_price_05232
room_price_06221
room_price_06222
room_price_06231
room_price_06232
room_price_07221
room_price_07222
room_price_07231
room_price_07232
room_price_08221
room_price_08222
room_price_08231
room_price_08232
room_price_09221
room_price_09222
room_price_09231
room_price_09232
room_price_10221
room_price_10222
room_price_10231
room_price_10232
room_price_11221
room_price_11222
room_price_11231
room_price_11232
room_price_12221
room_price_12222
room_price_12231';

$exp    =   explode(chr(13), $list);
$arr    =   [];
foreach ($exp as $e) {
    $arr[]  =   trim($e);
}

//print_r($arr);

echo    '<table>';
echo    '<tr>
        <td>Tên KS</td>
        <td>Link KS</td>
        <td>Link file giá</td>
        <td>Link file ảnh</td>
        </tr>';

foreach ($arr as $t) {
    $list   =   $DB->query("SELECT hot_id, hot_name, hot_url_price, hot_url_image
                            FROM hotel
                            INNER JOIN $t ON hot_id = rop_hotel_id
                            GROUP BY hot_id")
                            ->toArray();
    foreach ($list as $row) {
    
        echo    '<tr>';
        echo    '<td>' . $row['hot_name'] . '</td>';
        echo    '<td>https://vietgoing.com/hotel/' . $row['hot_id'] . '-' . to_slug($row['hot_name']) . '.html</td>';
        echo    '<td>' . $row['hot_url_price'] . '</td>';
        echo    '<td>' . $row['hot_url_image'] . '</td>';
        echo    '</tr>';
    }
}
echo    '</table>';
?>