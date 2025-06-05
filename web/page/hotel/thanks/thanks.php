<?
include('data_thanks.php');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?=$Layout->loadHead()?>
</head>
<body class="page_thanks page_thanks_hotel page-template-default header_white page st-header-2 woocommerce-page">
    <?
    include($path_root . 'layout/inc_header.php');
    ?>
    
    <?
    include('view_thanks.php');
    ?>
    
    <?
    include($path_root . 'layout/inc_footer.php');
    ?>
    
    <?=$Layout->loadFooter()?><?
    if (!empty($booking_info)) {
        ?>
        <script>
            // Nếu có error thì thông báo lên tạm
            var error = "<?=$error?>";
            if (error) {
                alert('<?=$message?>');
                window.location.href='<?=$url_return?>';
            }

            // Copy text to clipboard
            function copyToClipboard(text) {
                navigator.clipboard.writeText(text).then(() => {
                    // Copy xong thì đổi text trong tooltip
                    $(".tooltip-inner").html("Copied");
                }).catch(err => {
                    console.error('Lỗi khi sao chép:', err);
                });
            }
            $(function () {
                $.post(
                    '/ajax/ajax_complete.php',
                    {code: '<?=$booking_info['bkho_code']?>'}
                );
                
            });

            $('[data-toggle="tooltip"]').tooltip();
            
            //purchase Hotel
            dataLayer.push({ ecommerce: null });  // Clear the previous ecommerce object.
            dataLayer.push({
              event: "purchase",
              ecommerce: {
                transaction_id: "<?=$booking_info['bkho_code']?>",
                currency: "VND",
                value: <?=$booking_info['bkho_money_pay']?>,
                items: [
                    {item_id: "HI_<?=$booking_info['hot_id']?>", item_name: "<?=$booking_info['hot_name']?>"}
                ]
              }
            });
            
        </script>
        <?
    }
    ?>
</body>
</html>
