<?
include('data/data_profile.php');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?=$Layout->loadHead()?>
    <style>
        .form_search .form-control.date_range {
            width: 165px;
        }
    </style>
</head>
<body class="<?=($popup == 'true' ? 'page-modal ' : '')?>page-template page logged-in">
    <div class="page-wrapper chiller-theme toggled page_profile">
        <?
        include($path_root . 'layout/profile_sidebar.php');
        ?>
        
        <?
        include('view/view_profile.php');
        ?>
    </div>
        
    <?=$Layout->loadFooter()?>
</body>
</html>