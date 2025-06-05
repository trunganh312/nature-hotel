<template>
    <a-form :model="formSearch" name="horizontal_login" class="mt-3" layout="inline" @finish="onSearch">
        <a-form-item class="mb-3" name="adm_name">
            <a-input v-model:value="formSearch.adm_name" placeholder="Họ tên"></a-input>
        </a-form-item>
        <a-form-item class="mb-3" name="adm_email">
            <a-input v-model:value="formSearch.adm_email" placeholder="Email"></a-input>
        </a-form-item>
        <a-form-item class="mb-3" name="adm_phone">
            <a-input v-model:value="formSearch.adm_phone" placeholder="Điện thoại"></a-input>
        </a-form-item>
        <a-form-item class="mb-3">
            <a-button :disabled="formSearch.disabled" type="primary" html-type="submit">Tìm kiếm</a-button>
        </a-form-item>
        <a-form-item class="mb-3">
            <a-button class="ant-btn-primary-custom" @click="openFormUser(null)">
                <template #icon>
                    <PlusCircleOutlined />
                </template>
                Thêm mới
            </a-button>
        </a-form-item>
    </a-form>

    <!-- Hiện thi bảng -->
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
            <template v-if="column.dataIndex === 'stt'">
                <a-typography-link v-if="permissions.super" @click="openModalViewLog(record.adm_id)">
                    {{ index + 1 }}
                </a-typography-link>
                <span v-else>{{ index + 1 }}</span>
            </template>
            <template v-if="column.dataIndex === 'info'">
                <a-row>
                    <a-col :span="2"><i class="far fa-envelope"></i></a-col>
                    {{ record.adm_name }}
                </a-row>
                <a-row>
                    <a-col :span="2"><i class="far fa-user"></i></a-col>
                    <a-typography-link v-if="permissions.super" @click="fakeLogin(record.link_fake_login)">
                        {{ record.adm_email }}
                    </a-typography-link>
                    <span v-else>
                        {{ record.adm_email }}
                    </span>
                </a-row>
                <a-row>
                    <a-col :span="2"><i class="far fa-phone-alt"></i></a-col>
                    {{ record.adm_phone }}
                </a-row>
            </template>
            <template v-if="column.dataIndex === 'adm_active'">
                <a-checkbox
                    v-model:checked="record.adm_active"
                    :checked-value="1"
                    :unchecked-value="0"
                    @change="changeStatus(record, 'adm_active')"
                />
            </template>
            <template v-if="column.dataIndex === 'edit'">
                <EditTwoTone @click="openFormUser(record)" />
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

    <!-- Modal sửa, thêm user -->
    <a-modal
        v-model:open="open"
        width="700px"
        :body-style="{ minHeight: '30vh', overflowY: 'auto' }"
        :title="modalTitle"
        :footer="null"
        :destroyOnClose="true"
    >
        <a-form
            :model="formState"
            class="mt-3 pl-3"
            :label-col="{ span: 5 }"
            :wrapper-col="{ span: 17 }"
            @finish="handleSubmitForm"
        >
            <a-form-item
                class="mb-3"
                label="Họ tên"
                name="adm_name"
                :rules="[{ required: true, message: 'Vui lòng nhập họ tên' }]"
            >
                <a-input placeholder="Họ tên" v-model:value="formState.adm_name"></a-input>
            </a-form-item>
            <a-form-item
                class="mb-3"
                label="Email"
                name="adm_email"
                :rules="[{ required: true, message: 'Vui lòng nhập email' }]"
            >
                <a-input placeholder="Email" v-model:value="formState.adm_email"></a-input>
            </a-form-item>
            <a-form-item
                class="mb-3"
                label="Điện thoại"
                name="adm_phone"
                :rules="[{ required: true, message: 'Vui lòng nhập điện thoại' }]"
            >
                <a-input placeholder="Điện thoại" v-model:value="formState.adm_phone"></a-input>
            </a-form-item>
            <a-form-item class="mb-3" label="Link FB" name="adm_link_fb">
                <a-input placeholder="Link FB" v-model:value="formState.adm_link_fb"></a-input>
            </a-form-item>
            <a-form-item
                class="mb-3"
                label="Nhóm quyền"
                name="group"
                :rules="[{ required: true, message: 'Vui lòng chọn Nhóm quyền' }]"
            >
                <select-custom
                    v-model:value="formState.group"
                    :options="others.list_group"
                    mode="tags"
                    placeholder="Chọn Nhóm quyền"
                    ref="parentSelect"
                    @change="
                        () => {
                            this.$refs.parentSelect.blur();
                        }
                    "
                ></select-custom>
            </a-form-item>
            <a-row class="mb-3" v-if="formState.adm_id > 0">
                <a-col :span="5" :xs="0" :sm="5" :md="5" :lg="5" :xl="5" :xxl="5"></a-col>
                <a-checkbox v-model:checked="formState.reset_pw" :checked-value="1" :unchecked-value="0">
                    Reset password
                </a-checkbox>
            </a-row>
            <a-row class="mb-3">
                <a-col :span="5" :xs="0" :sm="5" :md="5" :lg="5" :xl="5" :xxl="5"></a-col>
                <a-button type="primary" html-type="submit" :loading="loading">{{
                    formState.adm_id ? 'Cập nhật' : 'Thêm mới'
                }}</a-button>
            </a-row>
        </a-form>
    </a-modal>

    <!-- Modal xem log -->
    <modal-view-log :viewLogData="viewLogData" @onSearchViewLogData="onSearchViewLogData"></modal-view-log>
</template>
<script>
import { Image, Tag, notification, Popconfirm, Mentions, Rate, Checkbox, AutoComplete } from '@lib/ant-design-vue';
import utils from '@root/utils';
import { ref } from '@lib/vue';
import { EditOutlined, DeleteTwoTone, EditTwoTone, PlusCircleOutlined } from '@lib/@ant-design/icons-vue';
import ModalViewLog from '../components/modal-view-log.vue';
import SelectCustom from '@admin/components/select-custom.vue';
export default {
    components: {
        EditOutlined,
        ATag: Tag,
        AImage: Image,
        DeleteTwoTone,
        EditTwoTone,
        APopconfirm: Popconfirm,
        AMentions: Mentions,
        ARate: Rate,
        ACheckbox: Checkbox,
        AAutoComplete: AutoComplete,
        ModalViewLog,
        SelectCustom,
        PlusCircleOutlined
    },
    data: () => ({
        baseColumns: [
            {
                title: '#',
                dataIndex: 'stt',
                align: 'center',
                width: '30px',
                order: 0
            },
            {
                title: 'ID',
                dataIndex: 'adm_id',
                align: 'center',
                width: '30px',
                order: 1
            },
            {
                title: 'Thông tin',
                dataIndex: 'info',
                width: '200px',
                order: 3
            },
            {
                title: 'Ngày tạo',
                dataIndex: 'adm_time_create',
                align: 'center',
                width: '100px',
                sorter: true,
                sortDirections: ['descend', 'ascend'],
                order: 7
            },
            {
                title: 'Last Online',
                dataIndex: 'adm_last_online',
                align: 'center',
                width: '100px',
                sorter: true,
                sortDirections: ['descend', 'ascend'],
                order: 8
            },
            {
                title: 'Nhóm quyền',
                dataIndex: 'adm_group',
                width: '150px',
                order: 9
            },
            {
                title: 'Phòng ban',
                dataIndex: 'adm_department',
                width: '150px',
                order: 10
            }
        ],
        loading: false,
        data_source: [],
        pagination: {
            total: 0,
            current: 0,
            pageSize: 10,
            pageSizeOptions: ['10', '20', '30', '50']
        },
        formSearch: {
            adm_name: '',
            adm_email: '',
            adm_phone: '',
            disabled: false
        },
        size: 'large',
        others: {},
        permissions: {},
        open: false,
        formState: {
            adm_name: '',
            adm_email: '',
            adm_phone: '',
            adm_link_fb: '',
            reset_pw: 0,
            group: null,
            adm_id: null
        },
        modalTitle: '',
        viewLogData: {
            modalTitle: '',
            openModal: false,
            formSearch: {
                daterange: null,
                type_log: null
            },
            loading: false,
            columns: [
                { title: 'STT', dataIndex: 'stt', align: 'center', width: '35px' },
                { title: 'Thời gian', dataIndex: 'log_time', align: 'center', width: '120px' },
                { title: 'Tài khoản', dataIndex: 'user_name', align: 'center', width: '100px' },
                { title: 'Nội dung', dataIndex: 'log_content' }
            ],
            data_source: [],
            others: {},
            id: ''
        }
    }),
    watch: {},
    computed: {
        columns() {
            // Conditionally include the "Sửa" column
            let baseColumns = [...this.baseColumns];
            if (this.permissions.hasEdit) {
                baseColumns.push(
                    {
                        title: 'Act',
                        dataIndex: 'adm_active',
                        align: 'center',
                        sorter: true,
                        sortDirections: ['descend', 'ascend'],
                        width: '30px',
                        order: 11
                    },
                    {
                        title: 'Sửa',
                        dataIndex: 'edit',
                        align: 'center',
                        sorter: true,
                        sortDirections: ['descend', 'ascend'],
                        width: '30px',
                        order: 12
                    }
                );
            }

            return baseColumns.sort((a, b) => a.order - b.order);
        }
    },
    created() {
        this.data_source = window.appData.rows;
        this.pagination = this.lodash.assign(this.pagination, window.appData.pagination);
        this.others = this.lodash.assign(this.others, window.appData.others);
        this.permissions = this.lodash.assign(this.permissions, window.appData.permissions);
        if (window.innerWidth <= 768) {
            this.size = 'small';
        }
    },
    methods: {
        /**
         * Xử lý tải lại data khi chuyển trang/sort trên table
         *
         * @param {object} pag
         * @param {object} filters
         * @param {object} sorter
         */
        async handleTableChange(pag, filters, sorter) {
            const searchParams = {
                ...this.formSearch
            };
            const params = {
                ...searchParams,
                sort: sorter.order == 'ascend' ? 'asc' : 'desc',
                pageSize: pag.pageSize
            };
            if (sorter.field == 'adm_time_create') {
                params.fieldsort = 'adm_time_create';
            } else if (sorter.field == 'adm_last_online') {
                params.fieldsort = 'adm_last_online';
            } else if (sorter.field == 'adm_active') {
                params.fieldsort = 'adm_active';
            }
            await this.getData(pag.current, params);
        },
        /**
         * Lấy ds khách sạn theo bộ lọc/phân trang
         *
         * @param {number} page Trang cần lấy
         * @param {object} params Tham số tìm kiếm
         */
        async getData(page = 1, params = {}) {
            this.loading = true;
            params = {
                ...params,
                ...this.formSearch,
                page: params.page ?? this.pagination.current ?? 1
            };
            params.json = 1;
            params.page = page;
            let res = await $.ajax({
                url: `admin_list.php?${new URLSearchParams(params).toString()}`,
                dataType: 'json'
            });

            if (this.lodash.toNumber(res.success) === 1) {
                this.data_source = res.data.rows;
                this.pagination = this.lodash.assign(this.pagination, res.data.pagination);
            }
            this.loading = false;
        },
        /**
         * Thực hiện tìm kiếm ks từ form
         *
         * @param {object} values Tham số tìm kiếm
         */
        async onSearch(values) {
            let params = {
                ...this.formSearch
            };

            if (this.formSearch.disabled) return;

            this.formSearch.disabled = true;
            await this.getData(1, params);
            this.formSearch.disabled = false;
        },
        // Change status
        async changeStatus(record, field = 'adm_active') {
            const params = {
                field,
                id: record.adm_id
            };
            let res = await $.ajax({
                url: `/common/active.php?${new URLSearchParams(params).toString()}`,
                dataType: 'json'
            });
            if (this.lodash.toNumber(res.success) === 1) {
                notification.success({
                    placement: 'bottomRight',
                    message: 'Cập nhật thành công!'
                });
                await this.getData(1, {});
            } else {
                notification.error({
                    message: 'Cập nhật thất bại!'
                });
            }
        },
        // Fake login
        fakeLogin(link) {
            window.open(link, '_blank');
        },
        openFormUser(record) {
            if (record) {
                this.formState = {
                    adm_name: record.adm_name || '',
                    adm_email: record.adm_email || '',
                    adm_phone: record.adm_phone || '',
                    adm_link_fb: record.adm_link_fb || '',
                    group: record.group,
                    reset_pw: 0,
                    adm_id: record.adm_id
                };
                this.modalTitle = 'Cập nhật thông tin tài khoản: ' + record.adm_name + ' - ' + record.adm_email;
            } else {
                this.formState = {
                    adm_name: '',
                    adm_email: '',
                    adm_phone: '',
                    adm_link_fb: '',
                    reset_pw: 0,
                    group: null
                };
                this.modalTitle = 'Thêm mới tài khoản Admin CRM';
            }
            this.open = true;
        },
        async handleSubmitForm() {
            this.loading = true;
            let url = '';
            if (this.formState.adm_id > 0) {
                url = `admin_edit.php?id=${this.formState.adm_id}`;
            } else {
                url = `admin_create.php`;
            }
            let res = await $.ajax({
                url: url,
                type: 'POST',
                data: this.formState,
                dataType: 'json'
            });
            if (this.lodash.toNumber(res.success) == 1) {
                notification.success({
                    placement: 'bottomRight',
                    message: 'Cập nhật thành công'
                });
                this.open = false;
                await this.getData();
            } else {
                for (let i in res.data) {
                    notification.error({
                        message: res.data[i]
                    });
                }
            }
            this.loading = false;
        },
        // Get data view log
        async fetchViewLog(params = {}) {
            this.viewLogData.loading = true;
            params = {
                ...params,
                id: this.viewLogData.id,
                table: this.others.talo_id,
                page: 1
            };
            let res = await $.ajax({
                url: `/common/view_log_data.php?${new URLSearchParams(params).toString()}`,
                dataType: 'json',
                method: 'GET'
            });
            if (this.lodash.toNumber(res.success) === 1) {
                // Set data view log
                this.viewLogData.data_source = res.data.rows;

                // Set type log
                this.viewLogData.others = res.data.others;
                this.viewLogData.openModal = true;
            } else {
                message.info(res.data[0]);
            }
            this.viewLogData.loading = false;
        },
        // Mở modal view log
        openModalViewLog(id) {
            this.viewLogData.modalTitle = `Lịch sử thay đổi dữ liệu`;

            this.viewLogData.id = id;
            this.fetchViewLog();
        },
        // Search view log data
        onSearchViewLogData() {
            const searchParams = {
                type_log: this.viewLogData.formSearch.type_log,
                date_range: this.viewLogData.formSearch.daterange
                    ? utils.formatDateRange(this.viewLogData.formSearch.daterange)
                    : ''
            };
            this.fetchViewLog(searchParams);
        }
    }
};
</script>
<style></style>
