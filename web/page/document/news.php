<?php
include('../../Core/Config/require_web.php');
include('data_news.php');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?= $Layout->loadHead() ?>
</head>
<body>
    <?php include($path_root . 'layout/inc_header.php'); ?>
    <?php include('view_news.php'); ?>
    <?php include($path_root . 'layout/inc_footer.php'); ?>
    <?= $Layout->loadFooter() ?>
</body>
</html>