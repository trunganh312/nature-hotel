<?
include('data_list.php');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?=$Layout->loadHead()?>
</head>
<body class="page_collection header_white page-template st-header-2">
    <?
    include($path_root . 'layout/inc_header.php');
    ?>
    
    <?
    include('view_list.php');
    ?>
    
    <?
    include($path_root . 'layout/inc_footer.php');
    ?>
    
    <?=$Layout->loadFooter()?>
    <?
    /** Update visit để tính CTR **/
    set_visit_page(169);
    ?>
</body>
</html>
