<template>
    <!-- Form search -->
    <a-form :model="formSearch" name="horizontal_login" class="mt-3" layout="inline" @finish="onSearch">
        <a-form-item class="mb-3" name="hot_name">
            <a-input v-model:value="formSearch.hot_name" placeholder="Khách sạn"></a-input>
        </a-form-item>
        <a-form-item class="mb-3" name="hot_type">
            <select-custom
                style="width: 150px"
                placeholder="Loại hình"
                :options="others.hot_type"
                show-search
                allow-clear
                v-model:value="formSearch.hot_type"
                :filter-option="utils.filterOption"
            />
        </a-form-item>
        <a-form-item class="mb-3" name="hot_star">
            <select-custom
                style="width: 150px"
                placeholder="Hạng sao"
                :options="others.hot_star"
                show-search
                allow-clear
                v-model:value="formSearch.hot_star"
                :filter-option="utils.filterOption"
            />
        </a-form-item>
        <a-form-item class="mb-3" name="hot_city">
            <select-custom
                style="width: 150px"
                placeholder="Tỉnh/Thành phố"
                :options="others.hot_city"
                show-search
                allow-clear
                v-model:value="formSearch.hot_city"
                :filter-option="utils.filterOption"
            />
        </a-form-item>
        <a-form-item class="mb-3">
            <a-button :disabled="formSearch.disabled" type="primary" html-type="submit">Tìm kiếm</a-button>
        </a-form-item>
    </a-form>

    <!-- Bảng khách sạn -->
    <a-table
        :columns="columns"
        :data-source="data_source"
        :pagination="pagination"
        :loading="loading"
        @change="handleTableChange"
        bordered
        :scroll="{ x: 'max-content' }"
    >
        <template #bodyCell="{ column, record, index }">
            <template v-if="column.dataIndex === 'stt'">{{ index + 1 }}</template>
            <template v-if="column.dataIndex === 'picture'">
                <a-image width="100px" :src="record.hot_picture" :fallback="defaultImage"></a-image>
            </template>
            <template v-if="column.dataIndex === 'info'">
                <a-tag :color="'green'" v-if="record.hot_type > 0">{{ record.hot_type_text }}</a-tag>
                <a-rate v-if="record.hot_star > 0" :value="record.hot_star" :count="record.hot_star" disabled />
                <p class="mb-0 bold">{{ record.hot_name }}</p>
                <i class="fal fa-map-marker-alt mr-2"></i>{{ record.hot_city }}
            </template>
            <template v-if="column.dataIndex === 'hot_top'">
                <a-checkbox
                    v-model:checked="record.hot_top"
                    :checked-value="1"
                    :unchecked-value="0"
                    @change="changeStatus(record, 'hot_top')"
                />
            </template>
            <template v-if="column.dataIndex === 'hot_active'">
                <a-checkbox
                    v-model:checked="record.hot_active"
                    :checked-value="1"
                    :unchecked-value="0"
                    @change="changeStatus(record, 'hot_active')"
                />
            </template>
        </template>
        <template #footer>
            <a-row :gutter="15">
                <a-col
                    >Tổng số bản ghi: <strong>{{ pagination.total }}</strong></a-col
                >
            </a-row>
        </template>
    </a-table>

</template>


<script>
import { Image, Tag, notification, Rate, Checkbox, AutoComplete } from '@lib/ant-design-vue';
import utils from '@root/utils';
import SelectCustom from '@admin/components/select-custom.vue';

export default {
    components: {
        ATag: Tag,
        AImage: Image,
        ARate: Rate,
        ACheckbox: Checkbox,
        AAutoComplete: AutoComplete,
        SelectCustom,
    },
    data: () => ({
        defaultImage:
            'data:image/png;base64,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',
        baseColumns: [
            {
                title: '#',
                dataIndex: 'stt',
                align: 'center',
                width: '35px',
                order: 1
            },
            {
                title: 'Ảnh đại diện',
                dataIndex: 'picture',
                width: '100px',
                order: 2
            },
            {
                title: 'Thông tin',
                dataIndex: 'info',
                width: '400px',
                order: 3
            },
            {
                title: 'Ngày tạo',
                dataIndex: 'hot_time_create',
                align: 'center',
                width: '100px',
                order: 6
            },
            {
                title: 'Top',
                dataIndex: 'hot_top',
                align: 'center',
                width: '30px',
                sorter: true,
                sortDirections: ['descend', 'ascend'],
                order: 8
            },
            {
                title: 'Act',
                dataIndex: 'hot_active',
                align: 'center',
                width: '30px',
                sorter: true,
                sortDirections: ['descend', 'ascend'],
                order: 9
            }
        ],
        bookingColumns: [
            {
                title: '#',
                dataIndex: 'stt',
                width: '35px',
                align: 'center'
            },
            {
                title: 'Thông tin đơn',
                dataIndex: 'info_booking',
                width: '170px'
            },
            {
                title: 'Thông tin phòng',
                dataIndex: 'info_room',
                width: '350px'
            },
            {
                title: 'Thông tin khách',
                dataIndex: 'info_customer',
                width: '250px'
            },
            {
                title: 'Nhận/Trả phòng',
                dataIndex: 'checkin_checkout',
                align: 'center',
                width: '120px'
            },
            {
                title: 'Số tiền (VNĐ)',
                dataIndex: 'money',
                width: '190px'
            },
            {
                title: 'Quản lý',
                dataIndex: 'manager',
                align: 'center',
                width: '170px'
            }
        ],
        loading: false,
        data_source: [],
        pagination: {
            total: 0,
            current: 1,
            pageSize: 10,
            pageSizeOptions: ['10', '20', '30', '50']
        },
        formSearch: {
            hot_name: '',
            hot_star: null,
            hot_type: null,
            hot_city: null,
            disabled: false
        },
        size: 'large',
        others: {},
        owner: {
            open: false,
            info: {},
            loading: false,
            modalTitle: '',
            formState: {
                id: 0,
                owner_name: ''
            }
        },
        users: [],
        permissions: {},
        booking: {
            open: false,
            hotelInfo: {},
            modalTitle: '',
            data_source: [],
            loading: false,
            pagination: {
                total: 0,
                current: 1,
                pageSize: 10,
                pageSizeOptions: ['10', '20', '30', '50']
            },
            formSearch: {
                bkho_code: '',
                bkho_name: '',
                bkho_source: null,
                daterangeCreate: [],
                daterangeCheckin: [],
                daterangeConfirm: [],
                daterangeComplete: [],
                disabled: false
            },
            others: {
                arr_source: [],
                total_money: 0
            }
        },
        status: {}
    }),
    computed: {
        columns() {
            let baseColumns = [...this.baseColumns];
            if (this.permissions.hasViewCount) {
                baseColumns.push(
                    {
                        title: 'View',
                        dataIndex: 'hot_count_view',
                        align: 'right',
                        width: '70px',
                        sorter: true,
                        sortDirections: ['descend', 'ascend'],
                        order: 6
                    },
                    {
                        title: 'Book',
                        dataIndex: 'hot_count_booking',
                        align: 'right',
                        width: '70px',
                        sorter: true,
                        sortDirections: ['descend', 'ascend'],
                        order: 7
                    }
                );
            }
            return baseColumns.sort((a, b) => a.order - b.order);
        },
        optionsUser() {
            return this.users.map((user) => ({
                key: user.id,
                value: user.value,
                payload: user
            }));
        }
    },
    created() {
        this.data_source = window.appData.rows;
        this.pagination = this.lodash.assign(this.pagination, window.appData.pagination);
        this.others = this.lodash.assign(this.others, window.appData.others);
        this.permissions = this.lodash.assign(this.permissions, window.appData.permissions);
        this.formSearch = this.lodash.assign(this.formSearch, window.appData.params);
        if (window.innerWidth <= 768) {
            this.size = 'small';
        }
    },
    methods: {
        async handleTableChange(pag, filters, sorter) {
            const searchParams = {
                ...this.formSearch,
                hot_star: this.formSearch.hot_star ?? -9999,
                hot_type: this.formSearch.hot_type ?? -9999,
                hot_city: this.formSearch.hot_city ?? -9999
            };
            const params = {
                ...searchParams,
                sort: sorter.order === 'ascend' ? 'asc' : 'desc',
                pageSize: pag.pageSize
            };
            if (sorter.field === 'hot_count_view') params.fieldsort = 'hot_count_view';
            else if (sorter.field === 'hot_count_booking') params.fieldsort = 'hot_count_booking';
            else if (sorter.field === 'hot_top') params.fieldsort = 'hot_top';
            else if (sorter.field === 'hot_active') params.fieldsort = 'hot_active';
            await this.getData(pag.current, params);
        },
        async getData(page = 1, params = {}) {
            this.loading = true;
            params.page = page;
            params = {
                ...this.formSearch,
                ...params,
                hot_star: this.formSearch.hot_star ?? -9999,
                hot_type: this.formSearch.hot_type ?? -9999,
                hot_city: this.formSearch.hot_city ?? -9999,
                page: params.page ?? this.pagination.current ?? 1,
                json: 1
            };
            let res = await $.ajax({
                url: `list.php?${new URLSearchParams(params).toString()}`,
                dataType: 'json'
            });
            if (this.lodash.toNumber(res.success) === 1) {
                this.data_source = res.data.rows;
                this.pagination = this.lodash.assign(this.pagination, res.data.pagination);
            }
            this.loading = false;
        },
        async onSearch(values) {
            let params = {
                ...this.formSearch,
                hot_star: this.formSearch.hot_star ?? -9999,
                hot_type: this.formSearch.hot_type ?? -9999,
                hot_city: this.formSearch.hot_city ?? -9999
            };
            if (this.formSearch.disabled) return;
            this.formSearch.disabled = true;
            await this.getData(1, params);
            this.formSearch.disabled = false;
        },
        async changeStatus(record, field = 'hot_active') {
            const params = { field, id: record.hot_id };
            let res = await $.ajax({
                url: `/common/active.php?${new URLSearchParams(params).toString()}`,
                dataType: 'json'
            });
            if (this.lodash.toNumber(res.success) === 1) {
                notification.success({ placement: 'bottomRight', message: 'Cập nhật thành công!' });
                await this.getData(null, {});
            } else {
                notification.error({ message: 'Cập nhật thất bại!' });
            }
        },
        async openFormEditOwner(record) {
            this.owner.open = true;
            this.owner.modalTitle = 'Cập nhật thông tin chủ sở hữu cho KS: ' + record.hot_name;
            this.owner.info = record;
            this.owner.formState = {
                id: record.hot_company_id,
                owner_name: record.hot_company_id <= 0 ? '' : `${record.com_name} - ${record.com_license_business}`
            };
            this.users = [];
        },
        onSearchUser(searchValue) {
            if (!searchValue) {
                this.users = [];
                return;
            }
            this.loadUser(searchValue);
        },
        async loadUser(value) {
            if (!value) {
                this.users = [];
                return;
            }
            let res = await $.ajax({
                url: `/common/search_auto.php?term=${value}&type=company&more=hotel`,
                type: 'GET',
                dataType: 'json'
            });
            this.users = res.slice(0, 10);
        },
        onSelect(i, item) {
            this.owner.formState.id = item.key;
        },
        async handleChangeOwner() {
            this.loading = true;
            let res = await $.ajax({
                url: `hotel_owner.php?id=${this.owner.info.hot_id}`,
                data: { hot_company_id: this.owner.formState.id },
                type: 'POST',
                dataType: 'json'
            });
            if (this.lodash.toNumber(res.success) === 1) {
                notification.success({ placement: 'bottomRight', message: 'Cập nhật thành công!' });
                await this.getData(null, {});
                this.owner.open = false;
            } else {
                notification.error({ message: 'Cập nhật thất bại!' });
            }
            this.loading = false;
        },
        // Booking methods
        async openBookingList(record) {
            this.booking.open = true;
            this.booking.hotelInfo = record;
            this.booking.modalTitle = `Danh sách đặt phòng của KS: ${record.hot_name}`;
            this.booking.data_source = [];
            this.booking.pagination.current = 1;
            this.booking.formSearch = {
                // Reset formSearch khi mở modal
                bkho_code: '',
                bkho_name: '',
                bkho_source: null,
                daterangeCreate: [],
                daterangeCheckin: [],
                daterangeConfirm: [],
                daterangeComplete: [],
                disabled: false
            };
            await this.fetchBookingData(1);
        },

        async fetchBookingData(page = 1) {
            this.booking.loading = true;
            const params = {
                ...this.booking.formSearch,
                bkho_hotel_id: this.booking.hotelInfo.hot_id,
                bkho_time_create:
                    this.booking.formSearch.daterangeCreate?.length > 0
                        ? `${this.booking.formSearch.daterangeCreate[0]}-${this.booking.formSearch.daterangeCreate[1]}`
                        : '',
                bkho_checkin:
                    this.booking.formSearch.daterangeCheckin?.length > 0
                        ? `${this.booking.formSearch.daterangeCheckin[0]}-${this.booking.formSearch.daterangeCheckin[1]}`
                        : '',
                bkho_time_success:
                    this.booking.formSearch.daterangeConfirm?.length > 0
                        ? `${this.booking.formSearch.daterangeConfirm[0]}-${this.booking.formSearch.daterangeConfirm[1]}`
                        : '',
                bkho_time_complete:
                    this.booking.formSearch.daterangeComplete?.length > 0
                        ? `${this.booking.formSearch.daterangeComplete[0]}-${this.booking.formSearch.daterangeComplete[1]}`
                        : '',
                bkho_source: this.booking.formSearch.bkho_source ?? -9999,
                page: page,
                pageSize: this.booking.pagination.pageSize,
                json: 1
            };
            let res = await $.ajax({
                url: `list_booking.php?${new URLSearchParams(params).toString()}`,
                dataType: 'json'
            });
            if (this.lodash.toNumber(res.success) === 1) {
                this.booking.data_source = res.data.rows;
                this.status = res.data.status;
                this.booking.pagination = this.lodash.assign(this.booking.pagination, res.data.pagination);
                this.booking.others = this.lodash.assign(this.booking.others, res.data.others);
            }
            this.booking.loading = false;
        },

        async onSearchBooking() {
            if (this.booking.formSearch.disabled) return;
            this.booking.formSearch.disabled = true;
            await this.fetchBookingData(1);
            this.booking.formSearch.disabled = false;
        },

        async handleBookingTableChange(pag) {
            this.booking.pagination.current = pag.current;
            this.booking.pagination.pageSize = pag.pageSize;
            await this.fetchBookingData(pag.current);
        },
        // Mở danh sách phòng
        handleOpenListRoom(record) {
            this.room.bk_code = record.bkho_code;
            this.room.open = true;
            this.room.modalTitle = `Danh sách phòng của booking #${record.bkho_code}: ${record.bkho_checkin} - ${record.bkho_checkout}`;
        },
        handleCloseModalRoom() {
            this.room.open = false;
            this.room.bk_code = '';
        },
        // Mở modal thêm dịch vụ
        async handleOpenService(booking_code, room_id) {
            this.services.modalTitle = `Các dịch vụ sử dụng của Booking #${booking_code}`;
            this.services.bk_code = booking_code;
            await this.fetchService(booking_code, room_id);
        },
        async fetchService(booking_code, room_id = 0) {
            if (room_id > 0) {
                this.services.isEditRoom = true;
                this.services.room_id = room_id;
            } else {
                this.services.isEditRoom = false;
                this.services.room_id = 0;
            }
            this.services.loading = true;
            // Trước khi thêm phải reset lại để tránh việc dữ liệu k đúng
            // Là 1 object cx các phòng và all
            this.services.data_source = {
                all: [
                    {
                        id: new Date().getTime(),
                        service: null,
                        unit: '',
                        price: 0,
                        quantity: 1,
                        money: 0,
                        note: '',
                        room_id: 0
                    }
                ]
            };
            let res = await $.ajax({
                type: 'GET',
                url: `service.php?booking=${booking_code}&json=1`,
                dataType: 'json'
            });
            if (this.lodash.toNumber(res.success) === 1) {
                this.services.open = true;

                // Data service
                this.services.service_data = this.lodash.values(res.data.services);
                // Lưu data service kiểu label => value vào để show lên ô select
                this.services.arr_service = this.services.service_data.map((item) => {
                    return { value: Number(item.ser_id), label: item.ser_name };
                });
                // service cũ
                this.services.current_service = this.lodash.values(res.data.current_service);

                // Thông tin booking
                this.services.booking_info = res.data.booking_info;

                // Thông tin service của từng phòng để show lên khi xem thông tin dịch vụ chung
                this.services.room_service_current = res.data.room_service_current;

                this.services.object_name = res.data.object_name;

                // Lặp qua các phòng của booking
                res.data.room_booking?.map((item) => {
                    this.services.data_source = {
                        ...this.services.data_source,
                        [item.bri_room_item_id]: [
                            {
                                id: new Date().getTime(),
                                service: null,
                                unit: '',
                                price: 0,
                                quantity: 1,
                                money: 0,
                                note: '',
                                room_id: Number(item.bri_room_item_id)
                            }
                        ]
                    };
                });

                this.services.room_service_current = this.lodash.groupBy(this.services.room_service_current, 'room_id');
            } else {
                notification.warning({
                    message: res.data[0]
                });
            }
            this.services.loading = false;
        }
    }
};
</script>

<style></style>
