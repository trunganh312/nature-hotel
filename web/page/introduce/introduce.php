<?php
include('../../Core/Config/require_web.php');
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <?= $Layout->loadHead() ?>
</head>

<body>
    <?php include($path_root . 'layout/inc_header.php'); ?>
    <?php include('view_introduce.php'); ?>
    <?php include($path_root . 'layout/inc_footer.php'); ?>
    <?= $Layout->loadFooter() ?>
</body>

</html>
