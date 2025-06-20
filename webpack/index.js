import Cookies from 'js-cookie';
import _ from 'lodash';
import { Base64 } from 'js-base64';
import { createApp, ref, reactive, toRaw } from 'vue';
import {
    ConfigProvider,
    Switch,
    Button,
    Modal,
    Radio,
    Spin,
    TimePicker,
    Form,
    Select,
    Space,
    Typography,
    message,
    Input,
    DatePicker,
    Dropdown,
    Menu,
    Drawer,
    Descriptions,
    Row,
    Col,
    Carousel,
    Card,
    Table,
    Textarea,
    Flex,
    InputNumber
} from 'ant-design-vue';
import dayjs from 'dayjs';
import utils from './utils';
import viVN from 'ant-design-vue/es/locale/vi_VN';
import 'dayjs/locale/vi';
dayjs.locale('vi');
import CKEditor from '@ckeditor/ckeditor5-vue';
import PusherPlugin from './plugins/pusher';

/**
 * Nhập các component ở đây
 */
import LayoutAdmin from '@admin/components/admin_layout.vue';

// CRM
import CrmHotelList from '@admin/hotel/list.vue';
import CrmGeneraAttributelList from '@admin/general/attribute_list.vue';
import CrmGeneraApplication from '@admin/general/application.vue';
import CrmGeneraCity from '@admin/general/city.vue';
import CrmGeneraDistrict from '@admin/general/district.vue';
import CrmGeneraBanklList from '@admin/general/bank_list.vue';
import CrmGeneraDocumentList from '@admin/general/document_list.vue';
import CrmGeneraDocumentCreate from '@admin/general/document_create.vue';
import CrmGeneraDocumentEdit from '@admin/general/document_edit.vue';
import CrmGeneraConfig from '@admin/general/crm_config.vue';
import CrmAccountAdminlList from '@admin/account/admin_list.vue';
import CrmAccountPermissionlList from '@admin/account/permission_list.vue';
import CrmAccountGroupList from '@admin/account/group_list.vue';
import CrmAccountDepartmentList from '@admin/account/department_list.vue';
import CrmAccountProfile from '@admin/account/profile.vue';
import CrmSystemModuleList from '@admin/system/module_list.vue';
import CrmSystemTableList from '@admin/system/table_log_list.vue';
import CrmSystemFieldList from '@admin/system/field_log_list.vue';
import CrmLogin from '@admin/login.vue';

const CustomViVN = {
    ...viVN,
    DatePicker: {
        lang: {
            ...viVN.DatePicker.lang,
            ok: 'Xác nhận'
        }
    },
    Modal: {
        ...viVN.Modal,
        okText: 'Xác nhận'
    },
    Table: {
        ...viVN.Table,
        triggerAsc: 'Nhấn để sắp xếp tăng dần',
        triggerDesc: 'Nhấn để sắp xếp giảm dần',
        cancelSort: 'Nhấn để hủy sắp xếp'
    }
};

window.app = function (options) {
    $.ajaxSetup({
        dataType: 'json'
    });
    options.setup = function setup() {
        const appData = ref(window.appData);
        return { appData };
    };
    const app = createApp(options);

    app.use(Space);
    app.use(TimePicker);
    app.use(Button);
    app.use(Modal);
    app.use(Radio);
    app.use(Spin);
    app.use(Form);
    app.use(Select);
    app.use(Typography);
    app.use(Input);
    app.use(DatePicker);
    app.use(Dropdown);
    app.use(Menu);
    app.use(Drawer);
    app.use(Descriptions);
    app.use(Row);
    app.use(Col);
    app.use(Carousel);
    app.use(Card);
    app.use(Table);
    app.use(ConfigProvider);
    app.use(CKEditor);
    app.use(Switch);
    app.use(Textarea);
    app.use(Flex);
    app.use(InputNumber);

    app.component('layout-admin', LayoutAdmin);

    // CRM
    app.component('crm-hotel-list', CrmHotelList);
    app.component('crm-general-attribute-list', CrmGeneraAttributelList);
    app.component('crm-general-application', CrmGeneraApplication);
    app.component('crm-general-city', CrmGeneraCity);
    app.component('crm-general-district', CrmGeneraDistrict);

    app.component('crm-general-bank-list', CrmGeneraBanklList);
    app.component('crm-general-document-list', CrmGeneraDocumentList);
    app.component('crm-general-document-create', CrmGeneraDocumentCreate);
    app.component('crm-general-document-edit', CrmGeneraDocumentEdit);
    app.component('crm-general-config', CrmGeneraConfig);
    app.component('crm-account-admin-list', CrmAccountAdminlList);
    app.component('crm-account-permission-list', CrmAccountPermissionlList);
    app.component('crm-account-group-list', CrmAccountGroupList);
    app.component('crm-account-department-list', CrmAccountDepartmentList);
    app.component('crm-account-profile', CrmAccountProfile);
    app.component('crm-system-module-list', CrmSystemModuleList);
    app.component('crm-system-table-list', CrmSystemTableList);
    app.component('crm-system-field-list', CrmSystemFieldList);
    app.component('a-crm-login', CrmLogin);

    app.config.globalProperties = {
        lodash: _,
        ref,
        moment,
        reactive,
        toRaw,
        dayjs,
        message,
        utils,
        locale: CustomViVN
    };

    app.use(PusherPlugin, {
        apiKey: 'beee041b5f61a92d3546',
        cluster: 'ap1'
    });

    app.mount('#app');
    return app;
};

// function issetPage(name) {
//     return $(`#${name}`).length > 0;
// }

// function getPrice(value) {
//     value = _.toNumber(`${value}`.split(',').join(''));
//     return isNaN(value) ? 0 : value;
// }

// Hàm tính toán khoảng thời gian giữa hai ngày
// function calculateDateDifference(startDate, endDate) {
//     // Tạo đối tượng Date từ chuỗi ngày bắt đầu và kết thúc
//     var start = new Date(startDate);
//     var end = new Date(endDate);

//     // Tính toán số mili giây giữa hai ngày
//     var difference = end.getTime() - start.getTime();

//     // Chuyển đổi sang ngày, giờ, phút, giây
//     var millisecondsInADay = 1000 * 60 * 60 * 24;
//     var daysDifference = Math.floor(difference / millisecondsInADay);

//     // Trả về số ngày chênh lệch
//     return daysDifference;
// }

window.$(document).ready(function () {
    // if (issetPage('page_inventory') || issetPage('page_booking_inventory')) {
    //     $('#daterange').on('apply.daterangepicker', function (ev, picker) {
    //         var startDate = picker.startDate.format('YYYY-MM-DD');
    //         var endDate = picker.endDate.format('YYYY-MM-DD');
    //         var endDate = new Date(endDate);
    //         let old_value = $('#daterange').val().split('-');
    //         var difference = calculateDateDifference(startDate, endDate);
    //         if (difference > 20) {
    //             setTimeout(() => {
    //                 $('#daterange').data('daterangepicker').setStartDate(old_value[0]);
    //                 $('#daterange').data('daterangepicker').setEndDate(old_value[1]);
    //                 $('#daterange').val(old_value.join(' - '));
    //             }, 10);
    //             alert('Koảng thời gian lựa chọn không được lớn hơn 20 đêm!');
    //         }
    //     });
    // }
    // if (issetPage('page_inventory')) {
    //     // Biến lưu trữ trạng thái khi vẽ hình chữ nhật
    //     var isDrawing = false;
    //     var priceType = 0;
    //     var startX, endX;
    //     // Lắng nghe sự kiện mousedown để bắt đầu vẽ hình chữ nhật
    //     document.getElementById('main_table').addEventListener('mousedown', function (event) {
    //         if ($(event.target).hasClass('item')) {
    //             priceType = Number($(event.target).data('type'));
    //             isDrawing = true;
    //             startX = event.target.cellIndex;
    //         }
    //     });
    //     // Lắng nghe sự kiện mousemove để vẽ hình chữ nhật
    //     document.getElementById('main_table').addEventListener('mousemove', function (event) {
    //         if (isDrawing) {
    //             endX = event.target.cellIndex;
    //             highlightSelectedColumns();
    //         }
    //     });
    //     // Lắng nghe sự kiện mouseup để kết thúc vẽ hình chữ nhật
    //     document.getElementById('main_table').addEventListener('mouseup', function (event) {
    //         if (isDrawing) {
    //             isDrawing = false;
    //             endX = event.target.cellIndex;
    //             highlightSelectedColumns();
    //         }
    //     });
    //     var daterange = '';
    //     let rooms_qty = {};
    //     var room_id = _.toInteger($('select[name=room_id]').val());
    //     rooms_qty[room_id] = 1;
    //     // Hàm đánh dấu các cột nằm trong hình chữ nhật
    //     function highlightSelectedColumns() {
    //         if (priceType < 1) return;
    //         var minCol = Math.min(startX, endX);
    //         var maxCol = Math.max(startX, endX);
    //         daterange = `${$($(`#main_table tr.day td`)[minCol - 1]).data('day')} - ${$(
    //             $(`#main_table tr.day td`)[maxCol - 1]
    //         ).data('day')}`;
    //         $(`#main_table td`).removeClass('highlight');
    //         // Đánh dấu các cột nằm trong hình chữ nhật
    //         for (var i = minCol; i <= maxCol; i++) {
    //             if (i < 1) continue;
    //             let item = $($(`#main_table tr._${priceType} td`)[i]);
    //             item.addClass('highlight');
    //         }
    //         let booking_btn = $('a.booking_create_btn');
    //         let href = booking_btn.data('href');
    //         href = href.replace('[roomraw]', Base64.encode(JSON.stringify(rooms_qty), true));
    //         href = href.replace('[price_type]', priceType);
    //         href = href.replace('[daterange]', daterange);
    //         booking_btn.attr('href', href);
    //         booking_btn.show();
    //     }
    // }
    // if (issetPage('page_booking_inventory')) {
    //     let rooms_qty = {};
    //     $('input[name=room_qty]').change(function () {
    //         let qty = _.toInteger($(this).val());
    //         let room_id = _.toInteger($(this).data('roomid'));
    //         rooms_qty[room_id] = qty;
    //         let total_qty = _.sum(_.values(rooms_qty));
    //         let booking_btn = $('a.booking_create_btn');
    //         if (total_qty > 0) {
    //             let href = booking_btn.data('href');
    //             href = href.replace('[roomraw]', Base64.encode(JSON.stringify(rooms_qty), true));
    //             booking_btn.attr('href', href);
    //             booking_btn.show();
    //         } else {
    //             booking_btn.hide();
    //         }
    //     });
    // }
    // if (issetPage('page_booking')) {
    //     function calculateMoney() {
    //         let money_total = 0;
    //         // Tính giá phòng + sl
    //         $(`.booking-info .rooms tbody input[name="rooms_id[]"]`).each(async function (i, elm) {
    //             let ID = _.toInteger($(elm).val());
    //             let price = getPrice($(`.booking-info .rooms tbody input[name="rooms_price[${ID}]"]`).val());
    //             let qty = _.toInteger($(`.booking-info .rooms tbody input[name="rooms_qty[${ID}]"]`).val());
    //             money_total += price * qty;
    //         });
    //         // Tính phụ thu + sl
    //         $(`table.surcharges tr.selected input[name="surcharges_id[]"]`).each(async function (i, elm) {
    //             let ID = _.toInteger($(elm).val());
    //             let price = getPrice($(`table.surcharges tbody input[name="surcharges_price[${ID}]"]`).val());
    //             let qty = _.toInteger($(`table.surcharges tbody input[name="surcharges_qty[${ID}]"]`).val());
    //             money_total += price * qty;
    //         });
    //         let money_discount = getPrice($('#money_discount').val());
    //         let money_pay = money_total - money_discount;
    //         $('.money_total .value').text(format_number(money_total));
    //         $('.money_discount .value').text(format_number(money_discount));
    //         $('.money_pay .value').text(format_number(money_pay));
    //         if (money_pay < 1) {
    //             $('.money_pay .value').addClass('text-danger');
    //         } else {
    //             $('.money_pay .value').removeClass('text-danger');
    //         }
    //     }
    //     function reloadRoom(startDate = null, endDate = null) {
    //         $(`.booking-info .rooms tbody input[name="rooms_id[]"]`).each(async function (i, elm) {
    //             let ID = _.toInteger($(elm).val());
    //             let queryParams = {
    //                 room_id: ID,
    //                 price_type: _.toInteger($('#price_type').val()),
    //                 daterange: startDate && endDate ? `${startDate} - ${endDate}` : $('#daterange').val()
    //             };
    //             let res = await $.get({
    //                 url: `/hms/booking/ajax/room_price.php?${$.param(queryParams)}`,
    //                 dataType: 'json'
    //             });
    //             $(`.booking-info .rooms tbody input[name="rooms_price[${ID}]"]`).val(res.data.price_format);
    //             calculateMoney();
    //         });
    //     }
    //     $('#surcharge_tmp').change(function () {
    //         let ID = _.toInteger($(this).val());
    //         let elm = $(`.surcharges tr[_id=${ID}]`);
    //         if (elm.hasClass('selected')) {
    //             elm.removeClass('selected');
    //             elm.find('input[name="surcharges_id[]"]').attr('disabled', '');
    //             calculateMoney();
    //             return;
    //         }
    //         elm.find('input[name="surcharges_id[]"]').removeAttr('disabled');
    //         elm.addClass('selected');
    //         calculateMoney();
    //     });
    //     $('.surcharges').on('click', 'td .remove-icon', function () {
    //         let elm = $(this).parents('tr');
    //         elm.removeClass('selected');
    //         elm.find('input[name="surcharges_id[]"]').attr('disabled', '');
    //         calculateMoney();
    //     });
    //     $('#choose_room').change(async function () {
    //         let ID = _.toInteger($(this).val());
    //         if ($(`.booking-info .rooms tbody tr[_id=${ID}]`).length > 0) return;
    //         let queryParams = {
    //             room_id: ID,
    //             price_type: _.toInteger($('#price_type').val()),
    //             daterange: $('#daterange').val()
    //         };
    //         let res = await $.get({
    //             url: `/hms/booking/ajax/room_price.php?${$.param(queryParams)}`,
    //             dataType: 'json'
    //         });
    //         if (res.data.max_qty < 1) {
    //             alert('Hạng phòng vừa chọn chưa được cập nhật số lượng phòng!');
    //             return;
    //         }
    //         let html = `<tr _id="${res.data.id}">
    //             <td class="room_title">${res.data.name}</td>
    //             <td>
    //                 <input type="hidden" name="rooms_id[]" value="${res.data.id}" />
    //                 <input type="text" class="form-control room_price number" name="rooms_price[${res.data.id}]" value="${res.data.price_format}" />
    //             </td>
    //             <td>
    //                 <input type="number" class="form-control room_qty" name="rooms_qty[${res.data.id}]" value="1" min="1" max="${res.data.max_qty}" />/${res.data.max_qty}
    //             </td>
    //             <td class="text-center"><i class="fal fa-times-circle text-danger remove-icon"></i></td>
    //         </tr>`;
    //         $('.booking-info .rooms tbody').append(html);
    //         calculateMoney();
    //     });
    //     $('#price_type').change(function () {
    //         reloadRoom();
    //     });
    //     $('#daterange').on('apply.daterangepicker', function (ev, picker) {
    //         var startDate = picker.startDate.format('YYYY-MM-DD');
    //         var endDate = picker.endDate.format('YYYY-MM-DD');
    //         var endDate = new Date(endDate);
    //         let old_value = $('#daterange').val().split('-');
    //         var difference = calculateDateDifference(startDate, endDate);
    //         if (difference > 20) {
    //             alert('Koảng thời gian lựa chọn không được lớn hơn 20 đêm!');
    //             setTimeout(() => {
    //                 $('#daterange').data('daterangepicker').setStartDate(old_value[0]);
    //                 $('#daterange').data('daterangepicker').setEndDate(old_value[1]);
    //                 $('#daterange').val(old_value.join(' - '));
    //             }, 10);
    //             return;
    //         }
    //         $('.count_night').text(`Số đêm: ${difference}`);
    //         reloadRoom(picker.startDate.format('DD/MM/YYYY'), picker.endDate.format('DD/MM/YYYY'));
    //     });
    //     $('.booking-info .rooms').on('click', 'td .remove-icon', function () {
    //         let elm = $(this).parents('tr');
    //         elm.remove();
    //         calculateMoney();
    //     });
    //     $('#page_booking').on(
    //         'change',
    //         ['input.room_qty', 'input.room_price', '#money_discount', '.surcharges input.form-control'],
    //         function () {
    //             calculateMoney();
    //         }
    //     );
    //     $(document).ready(function () {
    //         calculateMoney();
    //     });
    //     $('#bkho_get_vat').change(function () {
    //         if (this.checked) {
    //             $('.control_bkho_vat_info').show();
    //         } else {
    //             $('.control_bkho_vat_info').hide();
    //         }
    //     });
    // }
    // if (issetPage('page_hotel_diagram')) {
    //     $('.remove_room').click(async function (e) {
    //         e.preventDefault();
    //         if (!confirm('Xác nhận xoá phòng?')) return;
    //         let diagram_id = _.toNumber($(this).data('diagram_id'));
    //         let block_index = _.toNumber($(this).data('block_index'));
    //         let room_index = _.toNumber($(this).data('room_index'));
    //         let form_data = new FormData();
    //         form_data.append('diagram_id', diagram_id);
    //         form_data.append('block_index', block_index);
    //         form_data.append('room_index', room_index);
    //         form_data.append('obj', 'room');
    //         let res = await $.ajax({
    //             type: 'POST',
    //             url: `/hms/hotel/diagram/remove.php`,
    //             data: form_data,
    //             dataType: 'json',
    //             contentType: false,
    //             processData: false
    //         });
    //         if (_.toNumber(res.success) === 1) {
    //             $(this).parents('td').remove();
    //         } else {
    //             alert(res.msg);
    //         }
    //     });
    //     $('.remove_block').click(async function (e) {
    //         e.preventDefault();
    //         if (!confirm('Xác nhận xoá block?')) return;
    //         let diagram_id = _.toNumber($(this).data('diagram_id'));
    //         let block_index = _.toNumber($(this).data('block_index'));
    //         let form_data = new FormData();
    //         form_data.append('diagram_id', diagram_id);
    //         form_data.append('block_index', block_index);
    //         form_data.append('obj', 'block');
    //         let res = await $.ajax({
    //             type: 'POST',
    //             url: `/hms/hotel/diagram/remove.php`,
    //             data: form_data,
    //             dataType: 'json',
    //             contentType: false,
    //             processData: false
    //         });
    //         if (_.toNumber(res.success) === 1) {
    //             $(`.block_${block_index}`).remove();
    //         } else {
    //             alert(res.msg);
    //         }
    //     });
    //     $('.remove_area').click(async function (e) {
    //         e.preventDefault();
    //         if (!confirm('Xác nhận xoá khu?')) return;
    //         let diagram_id = _.toNumber($(this).data('diagram_id'));
    //         let hotel_id = _.toNumber($(this).data('hotel_id'));
    //         let form_data = new FormData();
    //         form_data.append('diagram_id', diagram_id);
    //         form_data.append('obj', 'area');
    //         let res = await $.ajax({
    //             type: 'POST',
    //             url: `/hms/hotel/config/remove.php`,
    //             data: form_data,
    //             dataType: 'json',
    //             contentType: false,
    //             processData: false
    //         });
    //         if (_.toNumber(res.success) === 1) {
    //             location.href = `/hms/hotel/diagram/index.php?id=${hotel_id}`;
    //         } else {
    //             alert(res.msg);
    //         }
    //     });
    // }
});
