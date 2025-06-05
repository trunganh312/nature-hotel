<?
$Layout->setTitle('Tìm các khách sạn ở gần vị trí của bạn - Vietgoing')
->setDescription('Tìm kiếm các khách sạn ở xung quanh vị trí hiện tại của bạn với mức giá tốt nhất, còn phòng trống, đặt ngay được ưu đãi và xác nhận phòng ngay.')
->setKeywords('đặt phòng, khách sạn, xung quanh, ở gần đây, giá rẻ, giá mới nhất ' . date('Y') . ', còn phòng trống, xác nhận ngay, nhiều ưu đãi, cam kết hoàn tiền')
->setCanonical(DOMAIN_WEB . '/hotel/')
->setJS(['page.basic']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?=$Layout->loadHead()?>
</head>
<body class="page-template-default page st-header-2 woocommerce-page header_white">
    <?
    include('layout/inc_header.php');
    ?>
    <div id="st-content-wrapper">
        <div class="container">
            <div class="box_notfound">
                <p>Vui lòng bật tính năng "Chia sẻ tọa độ" của trình duyệt (click "Đồng ý", "Cho phép", hoặc "Allow" như trong ảnh bên dưới) để cho phép trình duyệt lấy tọa độ vị trí hiện tại của bạn. Chúng tôi sẽ tìm kiếm các khách sạn tốt nhất ở xung quanh đây để hiển thị cho bạn lựa chọn.</p>
                <p class="text-center" style="margin: 20px 0 50px;"><img alt="Tìm các khách sạn ở gần đây" src="<?=($cfg_path_image)?>hotel_near_by_me.jpg" /></p>
                <p>Nếu trình duyệt của bạn không hỗ trợ tính năng "Chia sẻ tọa độ", vui lòng quay về <a href="<?=DOMAIN_WEB?>">Trang chủ</a> để tìm kiếm khách sạn theo các địa danh trên khắp Việt Nam.</p>                
            </div>
        </div>
    </div>
    <?
    include('layout/inc_footer.php');
    ?>
    <?=$Layout->loadFooter()?>
    <script>
        if (navigator.geolocation) {
            
            navigator.geolocation.getCurrentPosition(function(position) {
                
                console.log('Lat: ' + position.coords.latitude);
                console.log('Lng: ' + position.coords.longitude);
                
                var lat = position.coords.latitude;
                var lang = position.coords.longitude;
                
                var url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" + lat + "," + lang + "&sensor=true&key=<?=CON_KEY_MAP?>";

                $.getJSON(url, function (data) {
                    var address = data.results[0].formatted_address;
                    
                    $.post(
                        '/ajax/set_geocode.php',
                        {lat: lat, lng: lang, address: address},
                        function (data) {
                            window.top.location.href = '<?=$cfg_path_hotel_near?>';
                        }
                    );
                    
                });
            });
        } else {
            $('.box_notfound').text('<p>Trình duyệt bạn đang sử dụng không hỗ trợ tính năng lấy tọa độ vị trí hiện tại của bạn! Vui lòng quay về <a href="<?=DOMAIN_WEB?>">Trang chủ</a> để tìm kiếm khách sạn theo các địa danh trên khắp Việt Nam.</p>');
        }
    </script>
</body>
</html>