<?php

use src\Libs\Router;

Router::add('/hrm/newRequest', '/hrm/request/new_request.php');
Router::add('/hrm/request/ajax/assign', '/hrm/request/ajax/assign.php');

/** ----------------------- HRM - Customer ---------------------------------- */
Router::add('/hrm/customer/list', '/hrm/customer/list.php');
Router::add('/hrm/customer/create_level', '/hrm/customer/create_level.php');
Router::add('/hrm/customer/cskh', '/hrm/customer/cskh.php');
Router::add('/hrm/customer/edit_customer', '/hrm/customer/edit_customer.php');
Router::add('/hrm/customer/edit_level', '/hrm/customer/edit_level.php');
Router::add('/hrm/customer/edit', '/hrm/customer/edit.php');
Router::add('/hrm/customer/history', '/hrm/customer/history.php');
Router::add('/hrm/customer/list_cskh', '/hrm/customer/list_cskh.php');
Router::add('/hrm/customer/listLevel', '/hrm/customer/list_level.php');
Router::add('/hrm/customer/listCorporate', '/hrm/customer/list_customer_corporate.php');
Router::add('/hrm/customer/ajax/customer_corporate_save', '/hrm/customer/ajax/customer_corporate_save.php');


/** ----------------------- HRM - Account ---------------------------------- */
Router::add('/hrm/account/active_permission', '/hrm/account/active_permission.php');
Router::add('/hrm/account/company', '/hrm/account/company.php');
Router::add('/hrm/account/configMail', '/hrm/account/config_mail.php');
Router::add('/hrm/account/configTelegram', '/hrm/account/config_telegram.php');
Router::add('/hrm/account/configGeneral', '/hrm/account/config_general.php');
Router::add('/hrm/account/emailTemplate', '/hrm/account/email_template.php');
Router::add('/hrm/account/telegramTemplate', '/hrm/account/telegram_template.php');
Router::add('/hrm/account/configPermission', '/hrm/account/config_permission.php');
Router::add('/hrm/account/department_create', '/hrm/account/department_create.php');
Router::add('/hrm/account/department_edit', '/hrm/account/department_edit.php');
Router::add('/hrm/account/department_member_add', '/hrm/account/department_member_add.php');
Router::add('/hrm/account/department', '/hrm/account/department.php');
Router::add('/hrm/account/group_create', '/hrm/account/group_create.php');
Router::add('/hrm/account/group_edit', '/hrm/account/group_edit.php');
Router::add('/hrm/account/group', '/hrm/account/group.php');
Router::add('/hrm/account/profile', '/hrm/account/profile.php');
Router::add('/hrm/account/bank', '/hrm/account/bank_account_index.php');
Router::add('/hrm/account/bank_save', '/hrm/account/bank_account_save.php');
// Để đây để fake login chưa đổi link vội 
Router::add('/hrm/account/login_by_cto', '/hrm/account/login_by_cto.php');
Router::add('/hrm/account/change_password', '/hrm/account/change_password.php');
Router::add('/hrm/account/member_active', '/hrm/account/member_active.php');
Router::add('/hrm/account/member_add', '/hrm/account/member_add.php');
Router::add('/hrm/account/member_edit', '/hrm/account/member_edit.php');
Router::add('/hrm/account/member_remove', '/hrm/account/member_remove.php');
Router::add('/hrm/account/member', '/hrm/account/member.php');
Router::add('/hrm/account/permission', '/hrm/account/permission.php');
Router::add('/hrm/account/search_user', '/hrm/account/search_user.php');


Router::add('/hrm/account/ajax/change_password', '/hrm/account/ajax/change_password.php');
Router::add('/hrm/account/ajax/hotel_bank', '/hrm/account/ajax/hotel_bank.php');
Router::add('/hrm/account/ajax/bank_hotel_save', '/hrm/account/ajax/bank_hotel_save.php');


/** ----------------------- HRM - General ---------------------------------- */
// GENERAL/PARTNERSHIP
Router::add('/hrm/general/partnership/list', '/hrm/general/partnership/index.php');
Router::add('/hrm/general/partnership/list_backup', '/hrm/general/partnership/index_backup.php');
Router::add('/hrm/general/partnership/change_status', '/hrm/general/partnership/change_status.php');

// GENERAL/PARTNERSHIP/AJAX
Router::add('/hrm/general/partnership/ajax/create', '/hrm/general/partnership/ajax/create.php');
Router::add('/hrm/general/partnership/ajax/add', '/hrm/general/partnership/ajax/add.php');

Router::add('/hrm/general/partner/list', '/hrm/general/partner/partner.php');
Router::add('/hrm/general/partner/ajax/partner_save', '/hrm/general/partner/ajax/partner_save.php');
Router::add('/hrm/general/partner/ajax/change_status', '/hrm/general/partner/ajax/change_status.php');


/** ----------------------- HRM - PROFILE ---------------------------------- */
Router::add('/hrm/profile/company', '/hrm/profile/company.php');
Router::add('/hrm/profile/hotel', '/hrm/profile/hotel.php');
Router::add('/hrm/profile/review', '/hrm/profile/review.php');
Router::add('/hrm/profile/ajax/room_detail', '/hrm/profile/ajax/room_detail.php');

/** ----------------------- HRM - REQUEST ---------------------------------- */
Router::add('/hrm/request/assign', '/hrm/request/assign.php');
Router::add('/hrm/request/create', '/hrm/request/create.php');
Router::add('/hrm/request/detail', '/hrm/request/detail.php');
Router::add('/hrm/request/edit', '/hrm/request/edit.php');
Router::add('/hrm/request/list', '/hrm/request/list.php');

/** ----------------------- HRM - FINANCE ---------------------------------- */
Router::add('/hrm/finance/createSpend', '/hrm/finance/create_spend.php');
Router::add('/hrm/finance/edit_spend_booking', '/hrm/finance/edit_spend_booking.php');
Router::add('/hrm/finance/edit_spend', '/hrm/finance/edit_spend.php');
Router::add('/hrm/finance/listReceived', '/hrm/finance/list_received.php');
Router::add('/hrm/finance/listWait', '/hrm/finance/list_wait.php');
Router::add('/hrm/finance/listSpend', '/hrm/finance/list_spend.php');
Router::add('/hrm/finance/view_spend', '/hrm/finance/view_spend.php');
Router::add('/hrm/finance/bankTransfer', '/hrm/finance/index.php');
Router::add('/hrm/finance/createReceipt', '/hrm/finance/create_ticket_wait.php');
Router::add('/hrm/finance/create', '/hrm/finance/create.php');
Router::add('/hrm/finance/change_status', '/hrm/finance/change_status.php');
Router::add('/hrm/finance/ajax/confirm', '/hrm/finance/ajax/confirm.php');
Router::add('/hrm/finance/ajax/confirmed', '/hrm/finance/ajax/confirmed.php');
Router::add('/hrm/finance/ajax/create', '/hrm/finance/ajax/create.php');
Router::add('/hrm/finance/ajax/search_booking', '/hrm/finance/ajax/search_booking.php');
Router::add('/hrm/finance/ajax/search_booking_ticket', '/hrm/finance/ajax/search_booking_ticket.php');
Router::add('/hrm/finance/ajax/list_booking_ticket', '/hrm/finance/ajax/list_booking_ticket.php');
Router::add('/hrm/finance/ajax/confirm_ticket', '/hrm/finance/ajax/confirm_ticket.php');
/** ----------------------- HRM - COMMON ---------------------------------- */
Router::add('/hrm/common/active', '/hrm/common/active.php');

/** ----------------------- HRM - PLAN - KPI ---------------------------------- */
Router::add('/hrm/kpi/list', '/hrm/kpi/kpi_list.php');
Router::add('/hrm/kpi/ajax/kpiSave', '/hrm/kpi/ajax/kpi_save.php');

