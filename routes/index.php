<?php

use src\Libs\Router;

Router::add('/', 'index.php');
Router::add('/home', 'client/home.php');
Router::add('/login', 'client/login.php');
Router::add('/register', 'client/register.php');
Router::add('/upload_image_ckeditor', 'client/upload_image_ckeditor.php');
Router::add('/profile', 'client/profile.php');
Router::add('/forgot_password', 'client/forgot_password.php');
Router::add('/reset_password', 'client/reset_password.php');
Router::add('/logout', 'client/logout.php');
Router::add('/changeto', 'client/changeto.php');
Router::add('/get_select_child', 'client/get_select_child.php');
Router::add('/change_password', 'client/change_password.php');
// Router::add('/common/sidebar', 'common/get_sidebar.php'); // đã bỏ đi nhưng cmt lại trước
Router::add('/common/header', 'common/get_header.php');

Router::add('/api/login', 'api/login.php');
Router::add('/api/home', 'api/home.php');
Router::add('/api/check-auth', 'api/check-auth.php');
Router::add('/api/changeto', 'api/change_to.php');
Router::add('/api/house_skipping', 'api/house_skipping.php');
Router::add('/api/room_status', 'api/room_status.php');
Router::add('/api/service', 'api/service.php');
Router::add('/api/statistic/data', 'api/statistic/data.php');
Router::add('/api/statistic/effective', 'api/statistic/effective.php');

// Channex
Router::add('/api/channex_sync', 'api/channex_sync.php');
Router::add('/api/channex_webhook', 'api/channex_webhook.php');
Router::add('/api/channex_test', 'api/channex_test.php');
Router::add('/api/test_connection', 'api/test_connection.php');
Router::add('/api/mapping_details', 'api/mapping_details.php');
Router::add('/api/changes', 'api/changes.php');
Router::add('/api/channex_sync_bookings', 'api/channex_sync_bookings.php');
Router::add('/api/channex_register_webhook', 'api/channex_register_webhook.php');

