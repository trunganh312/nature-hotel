<?
include('../Core/Config/require_web.php');
//echo phpinfo();
//exit;

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?=$Layout->loadHead()?>
</head>
<body>
    <?
    
    save_log('test.cfn', 'Ghi log thành công');
    ?>
</body>
</html>