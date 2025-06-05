<?php

use src\Libs\Router;

Router::add('/ta/search', '/ta/search/index.php');


/** ----------------------- TA - BOOKING_HOTEL ---------------------------------- */
Router::add('/ta/bookingHotel/list', '/ta/booking_hotel/list.php');
Router::add('/ta/bookingHotel/convert_status', '/ta/booking_hotel/convert_status.php');
Router::add('/ta/bookingHotel/booking_money', '/ta/booking_hotel/booking_money.php');
Router::add('/ta/bookingHotel/ajax/update_payment', '/ta/booking_hotel/ajax/update_payment.php');
Router::add('/ta/bookingHotel/change_cskh', '/ta/booking_hotel/change_cskh.php');
Router::add('/ta/bookingHotel/change_status_partner', '/ta/booking_hotel/change_status_partner.php');
Router::add('/ta/bookingHotel/checkin', '/ta/booking_hotel/checkin.php');
Router::add('/ta/booking_hotel/create', '/ta/booking_hotel/create.php');
Router::add('/ta/bookingHotel/send_email', '/ta/booking_hotel/send_email.php');

/** ----------------------- TA - BOOKING ---------------------------------- */
Router::add('/ta/booking/assign', '/ta/booking/assign.php');
Router::add('/ta/booking/send_booking_hms', '/ta/booking/send_booking_hms.php');
Router::add('/ta/booking/booking_spend', '/ta/booking/booking_spend.php');
Router::add('/ta/booking/change_status', '/ta/booking/change_status.php');
Router::add('/ta/booking/new_request', '/ta/booking/new_request.php');
Router::add('/ta/booking/note_process', '/ta/booking/note_process.php');
Router::add('/ta/booking/review', '/ta/booking/review.php');


/** ----------------------- TA - BOOKING ---------------------------------- */
Router::add('/ta/book/ajax/create', '/ta/book/ajax/create.php');
Router::add('/ta/book/ajax/create_to_header', '/ta/book/ajax/create_to_header.php');

/** ----------------------- TA - COMMON ---------------------------------- */
Router::add('/ta/common/active', '/ta/common/active.php');
Router::add('/ta/common/search_auto', '/ta/common/search_auto.php');

/** ----------------------- TA - CONFIG ---------------------------------- */
Router::add('/ta/config/emailTemplate', '/ta/config/email_template.php');
Router::add('/ta/config/bank', '/ta/config/bank_account_index.php');
Router::add('/ta/config/bank_account_save', '/ta/config/bank_account_save.php');

/** ----------------------- TA - FINANCE ---------------------------------- */
Router::add('/ta/finance/createSpend', '/ta/finance/create_spend.php');
Router::add('/ta/finance/edit_spend_booking', '/ta/finance/edit_spend_booking.php');
Router::add('/ta/finance/edit_spend', '/ta/finance/edit_spend.php');
Router::add('/ta/finance/listReceived', '/ta/finance/list_received.php');
Router::add('/ta/finance/listSpendConfirm', '/ta/finance/list_spend_confirm.php');
Router::add('/ta/finance/listSpend', '/ta/finance/list_spend.php');
Router::add('/ta/finance/view_spend', '/ta/finance/view_spend.php');

/** ----------------------- TA - GENERAL ---------------------------------- */
Router::add('/ta/general/partner_create', '/ta/general/partner_create.php');
Router::add('/ta/general/partner_edit', '/ta/general/partner_edit.php');
Router::add('/ta/general/partner', '/ta/general/partner.php');
Router::add('/ta/general/ajax/partner_save', '/ta/general//ajax/partner_save.php');

/** ----------------------- TA - HOTEL ---------------------------------- */
Router::add('/ta/hotel/list', '/ta/hotel/list.php');
Router::add('/ta/hotel/location', '/ta/hotel/location.php');
Router::add('/ta/hotel/ajax/get_gallery', '/ta/hotel/ajax/get_gallery.php');
Router::add('/ta/hotel/ajax/get_room', '/ta/hotel/ajax/get_room.php');


/** ----------------------- TA - STATISTIC ---------------------------------- */
Router::add('/ta/statistic/booking', '/ta/statistic/booking.php');
Router::add('/ta/statistic/city', '/ta/statistic/city.php');
Router::add('/ta/statistic/cskh', '/ta/statistic/cskh.php');
Router::add('/ta/statistic/data', '/ta/statistic/data.php');
Router::add('/ta/statistic/department', '/ta/statistic/department.php');
Router::add('/ta/statistic/list_booking_city', '/ta/statistic/list_booking_city.php');
Router::add('/ta/statistic/list_booking', '/ta/statistic/list_booking.php');
Router::add('/ta/statistic/list_commission', '/ta/statistic/list_commission.php');
Router::add('/ta/statistic/list_debt_customer', '/ta/statistic/list_debt_customer.php');
Router::add('/ta/statistic/list_debt_partner', '/ta/statistic/list_debt_partner.php');
Router::add('/ta/statistic/list_exclude', '/ta/statistic/list_exclude.php');
Router::add('/ta/statistic/sale', '/ta/statistic/sale.php');
Router::add('/ta/statistic/list_request', '/ta/statistic/list_request.php');

/** ----------------------- TA - PROMOTION ---------------------------------- */
Router::add('/ta/promotion/list', '/ta/promotion/list.php');
Router::add('/ta/promotion/detail', '/ta/promotion/detail.php');

