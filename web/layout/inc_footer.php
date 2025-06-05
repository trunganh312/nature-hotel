<footer id="main-footer" class="clearfix">
    <?
    if (!empty($link_show_footer)) {
        ?>
        <div id="link_footer_detail">
            <div class="container">
                <h3 class="title_box_relate"><?=$title_link_footer?>:</h3>
                <div class="col-12 box_links no_border_pc">
                    <div class="box_links__body row">
                        <?
                        foreach ($link_show_footer as $row) {
                            echo    '<div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
                                        <a href="' . $row['link'] . '" title="' . $row['text'] . '">' . $row['text'] . '</a>
                                    </div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?
    }
    ?>
    <div>
        <div class="vg_talk">
            <div class="container">
                <div class="box_why">
                    <h4>Đặt qua Vietgoing.com có lợi gì?</h4>
                    <p><i class="fas fa-check-circle"></i>Giá rẻ hơn nhiều so với đặt trực tiếp tại khách sạn</p>
                    <p><i class="fas fa-check-circle"></i>Được nhân viên tư vấn và hỗ trợ 24/7</p>
                    <p><i class="fas fa-check-circle"></i>Được đảm bảo chắc chắn có phòng khi đến nơi</p>
                    <p><i class="fas fa-check-circle"></i>Được cam kết hoàn tiền nếu dịch vụ không hài lòng</p>
                    <p><i class="fas fa-check-circle"></i>Có nhiều ưu đãi, tặng mã giảm giá và tích lũy điểm</p>
                </div>
                <div class="box_talk">
                    <h5>Cần tới 10 năm để xây dựng danh tiếng, nhưng chỉ cần 1 phút để hủy hoại nó. Thấu hiểu điều đó, chúng tôi luôn đặt địa vị của mình vào khách hàng để làm việc với một thái độ chân thành và trách nhiệm cao nhất, không ngừng nỗ lực xây dựng uy tín của mình để phát triển bền vững.</h5>
                </div>
            </div>
        </div>
    </div>
    <div id="sub_link_footer" data-vc-full-width="true" data-vc-full-width-init="false" data-vc-stretch-content="true" class="vc_row wpb_row st bg-holder vc_row-has-fill vc_row-no-padding line_break_footer">
        <div class="container">
            <div class="row">
            	<div class="wpb_column column_container col-md-12">
                    <div class="vc_column-inner wpb_wrapper">
                        <?
                        if ($cfg_website['con_header_html'] == 1) {
                            include('inc_footer_html.php');
                        } else {
                            include('inc_footer_query.php');
                        }
                        ?>
                    </div>
                </div> 
            </div>
        </div>
    </div>
    <div class="vc_row-full-width vc_clearfix"></div>
    <div class="vc_row wpb_row st bg-holder">
        <div class="container">
            <div class="row">
            	<div class="wpb_column column_container col-md-12">
                    <div class="vc_column-inner wpb_wrapper">
            			<div class="vc_empty_space" style="height: 30px"><span class="vc_empty_space_inner"></span></div>
                    </div>
            	</div> 
            </div>
        </div>
    </div>
    <div data-vc-full-width="true" data-vc-full-width-init="false" class="vc_row wpb_row st bg-holder footer_container vc_row-has-fill">
        <div class="container">
            <div class="row">
                <div class="wpb_column column_container col-md-3">
                    <div class="vc_column-inner wpb_wrapper">
                        <div class="wpb_text_column wpb_content_element footer_logo" >
                            <div class="wpb_wrapper">
                                <a href="<?=DOMAIN_WEB?>"><img src="<?=$cfg_path_image?>logo/logo-medium.webp" alt="<?=BRAND_DOMAIN?>"></a>
                            </div>
                        </div>
                    	<div class="wpb_text_column wpb_content_element footer_contact" >
                    		<div class="wpb_wrapper">
                    			<p><span>Hotline</span></p>
                                <h4><a class="link_black event_footer_hotline" href="tel:<?=$cfg_website['con_hotline']?>"><?=$cfg_website['con_hotline']?></a></h4>
                    		</div>
                    	</div>
                    	<div class="wpb_text_column wpb_content_element footer_contact" >
                    		<div class="wpb_wrapper">
                    			<p><span>Email</span></p>
                                <h4><a class="link_black" href="mailto:<?=$cfg_website['con_email_contact']?>"><?=$cfg_website['con_email_contact']?></a></h4>
                    		</div>
                    	</div>
                    	<div class="wpb_text_column wpb_content_element footer_contact" >
                    		<div class="wpb_wrapper">
                    			<p><span style="margin-bottom: 5px;">Cùng chúng tôi<br /></span></p>
                                <p class="social_icon">
                                    <?
                                    if ($cfg_website['con_social_fb'] != '') {
                                        ?>
                                        <a href="<?=$cfg_website['con_social_fb']?>" target="_blank">
                                            <i class="fab fa-facebook-square"></i>
                                        </a>
                                        <?
                                    }
                                    if ($cfg_website['con_social_instagram'] != '') {
                                        ?>
                                        <a href="<?=$cfg_website['con_social_instagram']?>" target="_blank">
                                            <i class="fab fa-instagram"></i>
                                        </a>
                                        <?
                                    }
                                    if ($cfg_website['con_social_twitter'] != '') {
                                        ?>
                                        <a href="<?=$cfg_website['con_social_twitter']?>" target="_blank">
                                            <i class="fab fa-twitter"></i>
                                        </a>
                                        <?
                                    }
                                    if ($cfg_website['con_social_youtube'] != '') {
                                        ?>
                                        <a href="<?=$cfg_website['con_social_youtube']?>" target="_blank">
                                            <i class="fab fa-youtube"></i>
                                        </a>
                                        <?
                                    }
                                    if ($cfg_website['con_social_tiktok'] != '') {
                                        ?>
                                        <a href="<?=$cfg_website['con_social_tiktok']?>" target="_blank">
                                            <i class="fab fa-tiktok"></i>
                                        </a>
                                        <?
                                    }
                                    ?>
                                </p>
                    		</div>
                    	</div>
                        <div class="assign_tmdt">
                            <a href="http://online.gov.vn/Home/WebDetails/99125" target="_blank" title="Xem thông tin đăng ký website TMĐT của Vietgoing trên website của Bộ Công Thương"><img src="<?=$cfg_path_image?>logoCCDV.png" alt="Vietgoing đăng ký website TMĐT" /></a>
                        </div>
                    </div>
                </div>
                <?
                /** Lấy ra các bài viết được tick Show footer của 3 danh mục con của danh mục Về chúng tôi **/
                $data   =   $DB->query("SELECT cat_id, cat_name
                                        FROM category
                                        WHERE cat_parent_id = 5 AND cat_active = 1
                                        ORDER BY cat_order
                                        LIMIT 3")
                                        ->toArray();
                foreach($data as $cate) {
                    //Lấy ra các bài viết
                    $articles   =   $DB->query("SELECT art_id, art_title
                                                FROM article
                                                WHERE art_show_footer = 1 AND art_category = " . $cate['cat_id'] . " AND art_active = 1
                                                ORDER BY art_order")
                                                ->toArray();
                    ?>
                    <div class="wpb_column column_container col-md-3 footer_column">
                        <div class="vc_column-inner wpb_wrapper">
                            <div class="wpb_text_column wpb_content_element" >
                          		<div class="wpb_wrapper">
                        			<h4><?=$cate['cat_name']?></h4>
                        		</div>
                            </div>
                            <div class="vc_separator wpb_content_element vc_separator_align_center vc_sep_width_40 vc_sep_pos_align_left vc_separator_no_text" >
                                <span class="vc_sep_holder vc_sep_holder_l"><span style="border-color:#eaeaea;" class="vc_sep_line"></span></span>
                                <span class="vc_sep_holder vc_sep_holder_r"><span style="border-color:#eaeaea;" class="vc_sep_line"></span></span>
                            </div>
                            <div  class="vc_wp_custommenu wpb_content_element">
                                <div class="widget widget_nav_menu">
                                    <div class="menu-footer-new-container">
                                        <ul id="menu-footer-new" class="menu">
                                            <?
                                            foreach ($articles as $row) {
                                                ?>
                                                <li class="menu-item"><a href="<?=$Router->detailArticle($row)?>"><?=$row['art_title']?></a></li>
                                                <?
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                	</div>
                    <?
                }
                ?>
                <div class="wpb_column column_container col-md-3 footer_column">
                    <div class="vc_column-inner wpb_wrapper">
                        <div class="wpb_text_column wpb_content_element" >
                      		<div class="wpb_wrapper">
                    			<h4>Bài viết</h4>
                    		</div>
                        </div>
                        <div class="vc_separator wpb_content_element vc_separator_align_center vc_sep_width_40 vc_sep_pos_align_left vc_separator_no_text" >
                            <span class="vc_sep_holder vc_sep_holder_l"><span style="border-color:#eaeaea;" class="vc_sep_line"></span></span>
                        </div>
                        <div  class="vc_wp_custommenu wpb_content_element">
                            <div class="widget widget_nav_menu">
                                <div class="menu-footer-new-container">
                                    <ul id="menu-footer-new" class="menu">
                                        <li class="menu-item">
                                            <a href="<?=DOMAIN_WEB?>/article/185-to-chuc-team-building-gala-su-kien-du-lich-tron-goi.html">Tổ chức Team building, Tour đoàn riêng</a>
                                        </li>
                                        <?
                                        $cates  =   $DB->query("SELECT cat_id, cat_name
                                                                FROM category
                                                                WHERE cat_parent_id = 2 AND cat_active = 1
                                                                ORDER BY cat_order")
                                                                ->toArray();
                                        foreach ($cates as $row) {
                                            ?>
                                            <li class="menu-item"><a href="<?=$Router->listArticleCate($row)?>"><?=$row['cat_name']?></a></li>
                                            <?
                                        }
                                        ?>
                                        <li class="menu-item">
                                            <a href="<?=DOMAIN_WEB?>/article/cate-1-ve-chung-toi.html">Về Vietgoing.com</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
            	</div>
            </div>
        </div>
    </div>
    <div class="vc_row-full-width vc_clearfix"></div>
</footer>
<div class="container main-footer-sub">
    <div class="st-flex space-between">
        <div class="left mt20">
            <div class="f14">
                &COPY; Công ty TNHH Vietgoing - GPDKKD: 0109824598 do Sở KH&amp;ĐT Hà Nội cấp ngày 19/11/2021.<br />
                GP lữ hành nội địa: 01-0261/2022/SDL-GP LHNĐ do Sở Du Lịch TP Hà Nội cấp ngày 26/07/2022.<br />
                Địa chỉ ĐKKD: Số 18-B4, Ngách 61/67 Phùng Khoang, Trung Văn, Nam Từ Liêm, Hà Nội.<br />
                Địa chỉ VP: Tòa Nhà Đồng Phát Park View Tower, KĐT Vĩnh Hoàng, Hoàng Mai, Hà Nội.
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="st-login-form" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="max-width: 450px;">
        <div class="modal-content relative">
            <div class="loader-wrapper">
                <div class="st-loader"></div>
            </div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                    <i class="input-icon field-icon fa fa-times"></i>
                </button>
                <h4 class="modal-title">Đăng nhập</h4>
            </div>
            <div class="modal-body relative">
                <form action="" class="form" method="post">
                    <div class="form-group">
                        <input type="text" class="form-control" id="email_login" name="email" autocomplete="off" placeholder="Email">
                        <i class="input-icon field-icon far fa-envelope"></i>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" id="password_login" name="password" autocomplete="off" placeholder="Mật khẩu">
                        <i class="input-icon field-icon fas fa-unlock"></i>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="submit" class="form-submit btn btn-search" value="Đăng nhập">
                    </div>
                    <div class="message-wrapper mt20"></div>
                    <div class="mt20 st-flex space-between st-icheck">
                        <div class="st-icheck-item">
                            <label for="remember-me" class="c-grey">
                                <input type="checkbox" name="remember" id="remember-me" value="1"> Ghi nhớ
                                <span class="checkmark fcheckbox"></span>
                            </label>
                        </div>
                        <a href="javascript:;" class="st-link open-loss-password" data-toggle="modal">Quên mật khẩu?</a>
                    </div>
                    <div class="mt20 c-grey font-medium f14 text-center">
                        Bạn chưa có tài khoản?
                        <a href="javascript:;" class="st-link open-signup" data-toggle="modal">Đăng ký ngay</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="st-register-form" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="max-width: 520px;">
        <div class="modal-content relative">
            <div class="loader-wrapper">
                <div class="st-loader"></div>
            </div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                    <i class="input-icon field-icon fa fa-times"></i>
                </button>
                <h4 class="modal-title">Đăng ký tài khoản</h4>
            </div>
            <div class="modal-body">
                <form action="" class="form" method="post">
                    <input type="hidden" name="st_theme_style" value="modern"/>
                    <input type="hidden" name="action" value="st_registration_popup">
                    <div class="form-group">
                        <input type="text" class="form-control" name="name" id="name_register" autocomplete="off" placeholder="Họ và tên *">
                        <i class="input-icon field-icon fa fa-info"></i>
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control" name="email" id="email_register" autocomplete="off" placeholder="Email *">
                        <i class="input-icon field-icon far fa-envelope"></i>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" name="password" id="pass_register" autocomplete="off" placeholder="Mật khẩu *">
                        <i class="input-icon field-icon fas fa-unlock"></i>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="submit" class="form-submit btn btn-search" value="Đăng ký">
                    </div>
                    <div class="message-wrapper mt20"></div>
                    <div class="mt20 c-grey f14 text-center">
                        Bạn đã có tài khoản?
                        <a href="javascript:;" class="st-link open-login" data-toggle="modal">Đăng nhập</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="st-forgot-form" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="max-width: 450px;">
        <div class="modal-content">
            <div class="loader-wrapper">
                <div class="st-loader"></div>
            </div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                    <i class="input-icon field-icon fa fa-times"></i>
                </button>
                <h4 class="modal-title">Lấy lại mật khẩu</h4>
            </div>
            <div class="modal-body">
                <form action="" class="form" method="post">
                    <p class="c-grey f14">
                        Vui lòng nhập email tài khoản.<br/>
                        Chúng tôi sẽ gửi một E-mail để tạo lại mật khẩu mới.
                    </p>
                    <div class="form-group">
                        <input type="email" id="email_forgot" class="form-control" name="email" placeholder="Email">
                        <i class="input-icon field-icon far fa-envelope"></i>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="submit" class="form-submit btn btn-search" value="Gửi link tạo lại mật khẩu mới">
                    </div>
                    <div class="message-wrapper mt20"></div>
                    <div class="text-center mt20">
                        <a href="javascript:;" class="st-link open-login" data-toggle="modal">Quay lại trang đăng nhập</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<? include($path_root . 'layout/inc_support.php'); ?>
<? include($path_root . 'layout/inc_destination.php'); ?>
<? include($path_root . 'layout/inc_mapview.php'); ?>
<div class="st-quickview-backdrop"></div>
<!--<div id="back_top" onclick="goto_box('#header')" title="Quay lên trên" class="event_scroll_top"><i class="fas fa-arrow-up"></i></div>-->
<div id="footer_contact">
    <a href="tel:<?=$cfg_website['con_hotline']?>" class="phone-call event_icon_call" title="Gọi điện tới Vietgoing">
        <img src="<?=$cfg_path_image?>icon/phone-call.png" />
    </a>
    <a href="https://zalo.me/<?=$cfg_website['con_zalo']?>" class="zalo-chat event_icon_zalo" title="Chat Zalo với Vietgoing" target="_blank">
        <img src="<?=$cfg_path_image?>icon/zalo-chat.png" />
    </a>
    <a href="https://m.me/Vietgoingcom" class="messenger-chat event_icon_messenger" title="Chat Facebook với Vietgoing" target="_blank">
        <img src="<?=$cfg_path_image?>icon/messenger-chat.png" />
    </a>
</div>