<?php

use src\Libs\Router;

Router::add('/hms/search', '/hms/search/index.php');


/** ----------------------- HMS - Booking ---------------------------------- */

Router::add('/hms/booking/list', '/hms/booking/list.php');

Router::add('/hms/booking/money', '/hms/booking/money.php');
Router::add('/hms/booking/money_spend', '/hms/booking/money_spend.php');
Router::add('/hms/booking/booking', '/hms/booking/booking.php');
Router::add('/hms/booking/checkin', '/hms/booking/checkin.php');
Router::add('/hms/booking/invoice', '/hms/booking/invoice.php');
Router::add('/hms/booking/invoice_split', '/hms/booking/invoice_split.php');
Router::add('/hms/booking/note', '/hms/booking/note.php');
Router::add('/hms/booking/send_email', '/hms/booking/send_email.php');
Router::add('/hms/booking/review', '/hms/booking/review.php');
Router::add('/hms/booking/room_status', '/hms/booking/room_status.php');
Router::add('/hms/booking/service', '/hms/booking/service.php');
Router::add('/hms/booking/surcharge', '/hms/booking/surcharge.php');
Router::add('/hms/booking/status', '/hms/booking/status.php');
Router::add('/hms/booking/user', '/hms/booking/user.php');
Router::add('/hms/booking/room', '/hms/booking/room.php');
Router::add('/hms/booking/detail', '/hms/booking/detail.php');
// AJAX 
Router::add('/hms/booking/ajax/update_payment', '/hms/booking/ajax/update_payment.php');
Router::add('/hms/booking/ajax/assign', '/hms/booking/ajax/assign.php');
Router::add('/hms/booking/ajax/assign_room_quickly', '/hms/booking/ajax/assign_room_quickly.php');
Router::add('/hms/booking/ajax/booking_status_flow', '/hms/booking/ajax/booking_status_flow.php');
Router::add('/hms/booking/ajax/booking', '/hms/booking/ajax/booking.php');
Router::add('/hms/booking/ajax/change_room', '/hms/booking/ajax/change_room.php');
Router::add('/hms/booking/ajax/change_time_invoice', '/hms/booking/ajax/change_time_invoice.php');
Router::add('/hms/booking/ajax/room_price', '/hms/booking/ajax/room_price.php');
Router::add('/hms/booking/ajax/search_agency', '/hms/booking/ajax/search_agency.php');
Router::add('/hms/booking/ajax/search_corporate', '/hms/booking/ajax/search_corporate.php');
Router::add('/hms/booking/ajax/hold_room', '/hms/booking/ajax/hold_room.php');
Router::add('/hms/booking/ajax/card_checkin', '/hms/booking/ajax/card_checkin.php');
Router::add('/hms/booking/ajax/card_confirmation', '/hms/booking/ajax/card_confirmation.php');
Router::add('/hms/booking/ajax/tick_off_room', '/hms/booking/ajax/tick_off_room.php');
Router::add('/hms/booking/ajax/check_booking', '/hms/booking/ajax/check_booking.php');
Router::add('/hms/booking/ajax/check_breakfast', '/hms/booking/ajax/check_breakfast.php');
Router::add('/hms/booking/ajax/change_price', '/hms/booking/ajax/change_price.php');
Router::add('/hms/booking/ajax/check_price', '/hms/booking/ajax/check_price.php');
Router::add('/hms/booking/ajax/confirm_vat', '/hms/booking/ajax/confirm_vat.php');
Router::add('/hms/booking/ajax/get_price_hours', '/hms/booking/ajax/get_price_hours.php');
Router::add('/hms/booking/ajax/create_to_header', '/hms/booking/ajax/create_to_header.php');
Router::add('/hms/booking/ajax/get_room_price_days', '/hms/booking/ajax/get_room_price_days.php');
Router::add('/hms/booking/ajax/get_assigned_rooms', '/hms/booking/ajax/get_assigned_rooms.php');
Router::add('/hms/booking/ajax/delete_room_items', '/hms/booking/ajax/delete_room_items.php');


/** ----------------------- HMS - Booking Other ---------------------------------- */
Router::add('/hms/bookingOther/list', '/hms/booking_other/list.php');
Router::add('/hms/bookingOther/convert_status', '/hms/booking_other/convert_status.php');

Router::add('/hms/bookingOther/booking', '/hms/booking_other/booking.php');
Router::add('/hms/bookingOther/change_status', '/hms/booking_other/change_status.php');
Router::add('/hms/bookingOther/money', '/hms/booking_other/money.php');
Router::add('/hms/bookingOther/ajax/note', '/hms/booking_other/ajax/note.php');
Router::add('/hms/bookingOther/ajax/invoice', '/hms/booking_other/ajax/invoice.php');
Router::add('/hms/bookingOther/ajax/confirm_vat', '/hms/booking_other/ajax/confirm_vat.php');
Router::add('/hms/bookingOther/ajax/change_staff', '/hms/booking_other/ajax/change_staff.php');

Router::add('/hms/bookingOther/ajax/create_to_header', '/hms/booking_other/ajax/create_to_header.php');


/** ----------------------- HMS - Config ---------------------------------- */
Router::add('/hms/config/hotel', '/hms/config/hotel.php');
Router::add('/hms/config/bank', '/hms/config/bank.php');
Router::add('/hms/config/bank_account_save', '/hms/config/bank_account_save.php');


/** ----------------------- HMS - Common ---------------------------------- */
Router::add('/hms/common/delete_image_temp', '/hms/common/delete_image_temp.php');
Router::add('/hms/common/uploadify', '/hms/common/uploadify.php');
Router::add('/hms/common/warning_room', '/hms/common/warning_room.php');

/** ----------------------- HMS - Hotel ---------------------------------- */
Router::add('/hms/hotel/detail', '/hms/hotel/detail.php');
Router::add('/hms/hotel/breakfast', '/hms/hotel/breakfast.php');
Router::add('/hms/hotel/inventory', '/hms/hotel/inventory.php');
Router::add('/hms/hotel/ajax/review', '/hms/hotel/ajax/review.php');
Router::add('/hms/hotel/shift_handover/search_booking', '/hms/hotel/shift_handover/search_booking.php');
Router::add('/hms/hotel/shift_handover/get_bookings', '/hms/hotel/shift_handover/get_bookings.php');
// HOTEL
Router::add('/hms/hotel/create', '/hms/hotel/create.php');
Router::add('/hms/hotel/delete_image_room', '/hms/hotel/delete_image_room.php');
Router::add('/hms/hotel/delete_image', '/hms/hotel/delete_image.php');
Router::add('/hms/hotel/edit_image_hotel', '/hms/hotel/edit_image_hotel.php');
Router::add('/hms/hotel/inventory', '/hms/hotel/inventory.php');
Router::add('/hms/hotel/room_create', '/hms/hotel/room_create.php');
Router::add('/hms/hotel/room_edit', '/hms/hotel/room_edit.php');
Router::add('/hms/hotel/room_image', '/hms/hotel/room_image.php');
Router::add('/hms/hotel/room_price', '/hms/hotel/room_price.php');
Router::add('/hms/hotel/room_price', '/hms/hotel/room_price.php');
Router::add('/hms/hotel/timeline', '/hms/hotel/timeline.php');
Router::add('/hms/hotel/ajax/change_room', '/hms/hotel/ajax/change_room.php');
Router::add('/hms/hotel/ajax/check_room_availability', '/hms/hotel/ajax/check_room_availability.php');
Router::add('/hms/hotel/ajax/unassign_room', '/hms/hotel/ajax/unassign_room.php');

// HOTEL/AJAX
Router::add('/hms/hotel/ajax/edit', '/hms/hotel/ajax/edit.php');
Router::add('/hms/hotel/ajax/edit_map', '/hms/hotel/ajax/edit_map.php');
Router::add('/hms/hotel/ajax/assign_room', '/hms/hotel/ajax/assign_room.php');
Router::add('/hms/hotel/ajax/get_room', '/hms/hotel/ajax/get_room.php');
Router::add('/hms/hotel/ajax/edit_policy_cancel', '/hms/hotel/ajax/edit_policy_cancel.php');
Router::add('/hms/hotel/ajax/edit_policy_general', '/hms/hotel/ajax/edit_policy_general.php');
Router::add('/hms/hotel/ajax/edit_surcharge', '/hms/hotel/ajax/edit_surcharge.php');
Router::add('/hms/hotel/ajax/inventory_config_price_save', '/hms/hotel/ajax/inventory_config_price_save.php');
Router::add('/hms/hotel/ajax/inventory_config_price', '/hms/hotel/ajax/inventory_config_price.php');
Router::add('/hms/hotel/ajax/inventory_price', '/hms/hotel/ajax/inventory_price.php');
Router::add('/hms/hotel/ajax/inventory_room', '/hms/hotel/ajax/inventory_room.php');
Router::add('/hms/hotel/ajax/locking_room_range', '/hms/hotel/ajax/locking_room_range.php');
Router::add('/hms/hotel/ajax/locking_room', '/hms/hotel/ajax/locking_room.php');
Router::add('/hms/hotel/ajax/price_range_save', '/hms/hotel/ajax/price_range_save.php');
Router::add('/hms/hotel/ajax/price_save', '/hms/hotel/ajax/price_save.php');
Router::add('/hms/hotel/ajax/room_detail', '/hms/hotel/ajax/room_detail.php');
Router::add('/hms/hotel/ajax/state_lock_room', '/hms/hotel/ajax/state_lock_room.php');
Router::add('/hms/hotel/ajax/timeline', '/hms/hotel/ajax/timeline.php');
Router::add('/hms/hotel/ajax/list_booking', '/hms/hotel/ajax/list_booking.php');

// TODAY
Router::add('/hms/hotel/today/report', '/hms/hotel/today/report.php');
Router::add('/hms/hotel/today/remove_inventory', '/hms/hotel/today/remove_inventory.php');
Router::add('/hms/hotel/today/import', '/hms/hotel/today/import.php');
Router::add('/hms/hotel/today/daily', '/hms/hotel/today/daily.php');
Router::add('/hms/hotel/today/dailyList', '/hms/hotel/today/all_booking.php');
Router::add('/hms/hotel/today/checkout', '/hms/hotel/today/checkout.php');
Router::add('/hms/hotel/today/checkin', '/hms/hotel/today/checkin.php');

// TODAY/AJAX
Router::add('/hms/hotel/today/ajax/change_room', '/hms/hotel/today/ajax/change_room.php');
Router::add('/hms/hotel/today/ajax/list_booking', '/hms/hotel/today/ajax/list_booking.php');
Router::add('/hms/hotel/today/ajax/all_booking', '/hms/hotel/today/ajax/all_booking.php');
Router::add('/hms/hotel/today/ajax/daily', '/hms/hotel/today/ajax/daily.php');
Router::add('/hms/hotel/today/ajax/report_occ', '/hms/hotel/today/ajax/report_occ.php');
Router::add('/hms/hotel/today/ajax/report', '/hms/hotel/today/ajax/report.php');

// CONFIG
Router::add('/hms/hotel/config/bed', '/hms/hotel/config/bed.php');
Router::add('/hms/hotel/config/price', '/hms/hotel/config/price.php');
Router::add('/hms/hotel/config/service', '/hms/hotel/config/service.php');
Router::add('/hms/hotel/config/roomDiagram', '/hms/hotel/config/room_diagram.php');
Router::add('/hms/hotel/config/area', '/hms/hotel/config/area.php');
Router::add('/hms/hotel/config/block', '/hms/hotel/config/block.php');
Router::add('/hms/hotel/config/remove', '/hms/hotel/config/remove.php');
Router::add('/hms/hotel/config/room_multiple', '/hms/hotel/config/room_multiple.php');
Router::add('/hms/hotel/config/room', '/hms/hotel/config/room.php');
Router::add('/hms/hotel/config/search_service', '/hms/hotel/config/search_service.php');
Router::add('/hms/hotel/config/configCICO', '/hms/hotel/config/config_ci_co.php');
Router::add('/hms/hotel/config/configPriceHour', '/hms/hotel/config/config_price_hour.php');


// SHIFT_HANDOVER
Router::add('/hms/hotel/shift_handover/accept', '/hms/hotel/shift_handover/accept.php');
Router::add('/hms/hotel/shift_handover/detail', '/hms/hotel/shift_handover/detail.php');
Router::add('/hms/hotel/shiftHandover', '/hms/hotel/shift_handover/index.php');
Router::add('/hms/hotel/shift_handover/note', '/hms/hotel/shift_handover/note.php');
Router::add('/hms/hotel/shift_handover/save', '/hms/hotel/shift_handover/save.php');

// SMS_MONEY
Router::add('/hms/hotel/sms_money/accept', '/hms/hotel/sms_money/accept.php');
Router::add('/hms/hotel/smsMoney', '/hms/hotel/sms_money/index.php');
Router::add('/hms/hotel/sms_money/save', '/hms/hotel/sms_money/save.php');

// PROMOTION
Router::add('/hms/hotel/promotion/list', '/hms/hotel/promotion/list.php');
Router::add('/hms/hotel/promotion/ajax/save', '/hms/hotel/promotion/ajax/save.php');

// HOUSE_skipping
Router::add('/hms/hotel/houseSkipping', '/hms/hotel/house_skipping.php');

/** ----------------------- HMS - Statistic ---------------------------------- */
Router::add('/hms/statistic/booking', '/hms/statistic/booking.php');
Router::add('/hms/statistic/city', '/hms/statistic/city.php');
Router::add('/hms/statistic/cskh', '/hms/statistic/cskh.php');
Router::add('/hms/statistic/data', '/hms/statistic/data.php');
Router::add('/hms/statistic/debt', '/hms/statistic/debt.php');
Router::add('/hms/statistic/department', '/hms/statistic/department.php');
Router::add('/hms/statistic/effective', '/hms/statistic/effective.php');
Router::add('/hms/statistic/list_booking_city', '/hms/statistic/list_booking_city.php');
Router::add('/hms/statistic/list_booking_country', '/hms/statistic/list_booking_country.php');
Router::add('/hms/statistic/country', '/hms/statistic/country.php');
Router::add('/hms/statistic/list_booking', '/hms/statistic/list_booking.php');
Router::add('/hms/statistic/list_commission', '/hms/statistic/list_commission.php');
Router::add('/hms/statistic/list_debt_partner', '/hms/statistic/list_debt_partner.php');
Router::add('/hms/statistic/list_exclude', '/hms/statistic/list_exclude.php');
Router::add('/hms/statistic/list_request', '/hms/statistic/list_request.php');
Router::add('/hms/statistic/partner', '/hms/statistic/partner.php');
Router::add('/hms/statistic/corporate', '/hms/statistic/corporate.php');
Router::add('/hms/statistic/room', '/hms/statistic/room.php');
Router::add('/hms/statistic/sale', '/hms/statistic/sale.php');
Router::add('/hms/statistic/hotelChain', '/hms/statistic/hotel_chain.php');
Router::add('/hms/statistic/forecastOcc', '/hms/statistic/forecast_occ.php');
Router::add('/hms/statistic/money', '/hms/statistic/money.php');
Router::add('/hms/statistic/ajax/sale_price_detail', '/hms/statistic/ajax/sale_price_detail.php');

// AJAX
Router::add('/hms/statistic/ajax/excel_room', '/hms/statistic/ajax/excel_room.php');

/** ----------------------- HMS - Warehouse ---------------------------------- */
Router::add('/hms/warehouse/list', '/hms/warehouse/list.php');

Router::add('/hms/warehouse/usageService', '/hms/warehouse/usage_service.php');
Router::add('/hms/warehouse/transactionList', '/hms/warehouse/transaction_list.php');

// AJAX 
Router::add('/hms/warehouse/ajax/warehouse', '/hms/warehouse/ajax/warehouse.php');
Router::add('/hms/warehouse/ajax/warehouse_history', '/hms/warehouse/ajax/warehouse_history.php');
Router::add('/hms/warehouse/ajax/transaction', '/hms/warehouse/ajax/transaction.php');


// CONVERT
Router::add('/hms/convert/convert_price_phase1', '/hms/convert/convert_price_phase1.php');
Router::add('/hms/convert/conver_group_vg_to_sn_b1', '/hms/convert/conver_group_vg_to_sn_b1.php');
Router::add('/hms/convert/conver_group_vg_to_sn_b2', '/hms/convert/conver_group_vg_to_sn_b2.php');
Router::add('/hms/convert/conver_group_service', '/hms/convert/conver_group_service.php');
Router::add('/hms/convert/convert_surcharges', '/hms/convert/convert_surcharges.php');
