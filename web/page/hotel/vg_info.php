<?php
include('../../Core/Config/require_web.php');
$hotel_id   =   getValue('id');

$hotel_info =   $DB->query("SELECT hot_name, hot_phone, hot_phone_sale, hot_email, hot_note_contacts FROM hotel WHERE hot_id = $hotel_id")->getOne();
$Layout->setIndex(false)
->setJS(['page.basic']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?=$Layout->loadHead()?>
</head>
<body>
    <div class="iframe_modal" style="margin-top: 20px;">
        <h4 class="text-center"><?=$hotel_info['hot_name']?></h4>
        <p>ĐT Sale: <?=$hotel_info['hot_phone_sale']?></p>
        <p>ĐT Lễ tân: <?=$hotel_info['hot_phone']?></p>
        <p>Email: <?=$hotel_info['hot_email']?></p>
        <p>Ghi chú: <?=$hotel_info['hot_note_contacts']?></p>
    </div>
    <?=$Layout->loadFooter()?>
    <script type="text/javascript">
        $(document).ready(function() {
            // Hoạt động khi bị nhúng làm iframe, sẽ gửi height hiện tại sang cho page parent
            setTimeout(function() {
                window.parent.postMessage($(document).outerHeight() + 5, '*');
            }, 300);
        });
    </script>
</body>
</html>
