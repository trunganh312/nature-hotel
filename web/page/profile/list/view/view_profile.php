<!-- sidebar-wrapper  -->
<main class="page-content">
    <div class="st_content">
        <div class="row div-partner-page-title">
            <div class="col-md-12 title_page_profile">
                <div class="st-create">
                    <h3 class="partner-page-title"><?=$page_title?></h3>
                    <?
                    if (!empty($add_button_top)) {
                        echo    $add_button_top;
                    }
                    ?>
                </div>
            </div>
            <?
            include($file_view);
            ?>
        </div>
    </div>
</main>
<div class="sidenav-overlay"></div>