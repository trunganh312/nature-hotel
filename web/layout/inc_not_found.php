<?
if (empty($_SESSION['vg_sent_request'])) {
    //Đảo câu để tránh bị report là duplicate content
    if (rand(1, 2) > 1) {
        ?>
        <p>Quý khách vui lòng liên hệ Hotline <a href="tel:<?=$cfg_website['con_hotline']?>"><?=$cfg_website['con_hotline']?></a> hoặc <a href="javascript:;" onclick="$('#st-support-form').modal('show');">Gửi yêu cầu riêng</a> để chúng tôi có cơ hội được phục vụ Quý khách.</p>
        <?
    } else {
        ?>
        <p>Quý khách vui lòng <a href="javascript:;" onclick="$('#st-support-form').modal('show');">Gửi yêu cầu riêng</a> hoặc liên hệ Hotline <a href="tel:<?=$cfg_website['con_hotline']?>"><?=$cfg_website['con_hotline']?></a> để chúng tôi có cơ hội được phục vụ Quý khách.</p>
        <?
    }
} else {
    ?>
    <p>Quý khách vui lòng liên hệ Hotline <a href="tel:<?=$cfg_website['con_hotline']?>"><?=$cfg_website['con_hotline']?></a> để chúng tôi có cơ hội được phục vụ Quý khách.</p>
    <?
}
?>
<p>Rất xin lỗi Quý khách vì sự bất tiện này!</p>