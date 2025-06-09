<?
include('page/home/data_home.php');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?=$Layout->loadHead()?>
    <style>
        .list-destination.layout4 .destination-item .image .content h4 {
            font-size: 30px!important;
            text-shadow: 1px 1px 1px #000;
        }
        .st-offer-new .item-title {
            text-shadow: 1px 1px 1px #000;
        }
        @media (max-width: 991px) {
            .home_deal_hot.mt10 {
                margin-top: 20px !important;
                margin-bottom: 0;
            }
            .list-destination.layout4.mt20 {
                margin-top: 0;
            }
        }
    </style>
</head>
<body class="page_home home page-template page st-header-2 header_white">
    <?
    include('layout/inc_header.php');
    ?>
    
    <?
    include('page/hotel/detail/view_detail.php');
    ?>
    <?
    include('layout/inc_footer.php');
    ?>
    <?=$Layout->loadFooter()?>

</body>
</html>
