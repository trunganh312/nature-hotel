<?
include('data_list.php');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <?=$Layout->loadHead()?>
</head>
<body class="page_hotel_list header_white page-template page st-header-2">
    <?
    include($path_root . 'layout/inc_header.php');
    ?>
    
    <?
    include('view_list.php');
    ?>
    
    <?
    include($path_root . 'layout/inc_footer.php');
    ?>
    <?=$schema_html?>
    <?=$Layout->loadFooter()?>
    
    <script>
        $('.slider_dest_schedule').owlCarousel({
            loop:true,
            autoplay: true,
            margin:10,
            responsiveClass:true,
            responsive:{
                0:{
                    items:1,
                    loop:true
                },
                380:{
                    items:2,
                    loop:true
                },
                600:{
                    items:3,
                    loop:true
                },
                992:{
                    items:4,
                    loop:true
                }
            }
        });
        
        $(".vietgoing-mobile-check-in-out").daterangepicker({
            singleDatePicker: false,
            dateFormat: 'DD/MM/YYYY - DD/MM/YYYY',
            autoUpdateInput: true,
            autoApply: true,
            disabledPast: true,
            customClass: '',
            widthSingle: 500,
            onlyShowCurrentMonth: true,
            locale: locale_daterangepicker
        }).on("apply.daterangepicker", function (ev, picker) {
            let start   = picker.startDate.format("DD/MM/YYYY");
            let end     = picker.endDate.format("DD/MM/YYYY");
            $('.form-date-search').find('.check-in-wrapper>span').show();
            $('.form-date-search').find('.check-in-render').text(start).show();
            $('.form-date-search').find('.check-out-render').text(end).show();
            $('.change_date_mb').find('.check-in-render').text(start);
            $('.change_date_mb').find('.check-out-render').text(end);
            $('.form-date-search').find('input[name=checkin]').val(start);
            $('.form-date-search').find('input[name=checkout]').val(end);
            
            const url = new URL(window.location);
            url.searchParams.set('checkin', start);
            url.searchParams.set('checkout', end);
            window.location.href = url;
        });
        
    </script>
    
    <?
    /** Update visit để tính CTR **/
    if ($page_near_by) {
        set_visit_page([136, 171]);
        ?>
        <script>
            console.log('<?=$current_location?>');
        </script>
        <?
    } else {
        set_visit_page(136);
    }
    ?>
</body>
</html>
