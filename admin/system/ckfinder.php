<?php 
include('config_module.php');
$Auth->checkPermission('manage_files');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?=$Layout->loadHead("Cms.vietgoing.com | Thư viện ảnh");?>
    <style>
        iframe {
            min-width: 100%;
            min-height: 100vh;
            border: unset;
        }
    </style>
</head>
<body class="sidebar-mini">
	<? $Layout->header(''); ?>
    <iframe src="/ckfinder/ckfinder.php"></iframe>
	<? $Layout->footer(); ?>
</body>
</html>