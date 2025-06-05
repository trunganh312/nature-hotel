<a id="show-sidebar" class="btn btn-sm btn-dark" href="javascript:;">
    <i class="fas fa-bars"></i>
</a>
<nav id="sidebar" class="sidebar-wrapper">
    <div class="sidebar-content">
        <div class="sidebar-header">
            <a href="<?=DOMAIN_WEB?>">
                <img src="<?=$cfg_path_image?>logo/logo.png" class="st-logo-site" alt="<?=BRAND_NAME?>"/>
            </a>
            <div class="sidebar-brand icon-ccv hidden-md">
                <a href="#"></a>
                <div id="close-sidebar">
                    <i class="fas fa-chevron-left"></i>
                </div>
            </div>
        </div>
        <div class="sidebar-header">
            <div class="user-pic">
                <img alt="<?=BRAND_NAME?> Profile" src="<?=$Router->srcUserAvatar($User->info['use_avatar'], SIZE_MEDIUM)?>" class="avatar avatar-50 photo" height='50' width='50' loading='lazy'/>
            </div>
            <div class="user-info">
                <span class="user-name"><?=$User->info['use_name']?></span>
                <!--<span class="user-role">Since: Mar 2021</span>-->
            </div>
        </div>
        <div class="sidebar-menu">
            <ul>
                <?
                foreach ($array_menu_sidebar as $key => $menu) {
                    if ($menu['icon'] != '') {
                        ?>
                        <li class="<?=($key == $module ? 'active' : '')?>">
                            <a href="<?=($cfg_path_profile . '?mod=' . $key)?>">
                                <i class="<?=$menu['icon']?> st-icon-menu"></i>
                                <span><?=$menu['label']?></span>
                            </a>
                        </li>
                        <?
                    }
                }
                ?>
            </ul>
        </div>
        <div class="sidebar-footer">
            <ul>
                <li>
                    <a href="javascript:logout_ajax();">
                        <i class="fas fa-sign-out-alt st-icon-menu"></i>
                        <span>Đăng xuất</span>
                    </a>
                </li>
                <li>
                    <a href="<?=DOMAIN_WEB?>" target="_blank">
                        <span class="st-green-homepage">Trang chủ</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- sidebar-menu  -->
    </div>
    <!-- sidebar-content  -->
</nav>
