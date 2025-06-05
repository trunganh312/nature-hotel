<template>
    <a-form :model="formSearch" name="horizontal_login" class="mt-3" layout="inline" @finish="getData">
        <a-form-item class="mb-2" name="talo_table">
            <a-input v-model:value="formSearch.talo_table" placeholder="Tên bảng"></a-input>
        </a-form-item>
        <a-form-item class="mb-2" name="fie_field">
            <a-input v-model:value="formSearch.fie_field" placeholder="Tên trường"></a-input>
        </a-form-item>
        <a-form-item class="mb-2" name="fie_name">
            <a-input v-model:value="formSearch.fie_name" placeholder="Tên lưu log"></a-input>
        </a-form-item>
        <a-form-item class="mb-2" name="fie_table_target">
            <a-input v-model:value="formSearch.fie_table_target" placeholder="Bảng lấy dữ liệu"></a-input>
        </a-form-item>
        <a-form-item class="mb-2" name="fie_variable">
            <a-input v-model:value="formSearch.fie_variable" placeholder="Biến giá trị"></a-input>
        </a-form-item>
        <a-form-item class="mb-2" name="fie_type">
            <select-custom
                style="width: 150px"
                placeholder="Kiểu dữ liệu"
                :options="others.fie_type"
                show-search
                allow-clear
                v-model:value="formSearch.fie_type"
                :filter-option="utils.filterOption"
            />
        </a-form-item>
        <a-form-item class="mb-2">
            <a-button type="primary" html-type="submit" style="width: 100%">Tìm kiếm</a-button>
        </a-form-item>
        <a-form-item class="mb-2">
            <a-button class="ant-btn-primary-custom" style="width: 100%" @click="openModal(null)">
                <template #icon>
                    <PlusCircleOutlined />
                </template>
                Thêm mới
            </a-button>
        </a-form-item>
    </a-form>

    <!-- Table Thêm dịch vụ -->
    <a-table
        :columns="columns"
        :data-source="data_source"
        :pagination="pagination"
        bordered
        :loading="loading"
        :scroll="{ x: 'max-content' }"
        @change="handleTableChange"
    >
        <template #bodyCell="{ column, record, index }">
            <template v-if="column.dataIndex === 'stt'">
                {{ index + 1 }}
            </template>
            <template v-if="column.dataIndex === 'edit'">
                <EditTwoTone @click="openModal(record)" />
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

    <!-- Modal sửa dịch vụ -->
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
                class="mb-2"
                name="fie_table_id"
                label="Bảng"
                :rules="[{ required: true, message: 'Vui lòng chọn bảng' }]"
            >
                <select-custom
                    placeholder="Bảng"
                    :options="others.list_table"
                    show-search
                    v-model:value="formState.fie_table_id"
                    :filter-option="utils.filterOption"
                />
            </a-form-item>
            <a-form-item
                class="mb-2"
                label="Tên trường"
                name="fie_field"
                :rules="[{ required: true, message: 'Vui lòng nhập tên trường' }]"
            >
                <a-input placeholder="Tên trường" v-model:value="formState.fie_field"></a-input>
            </a-form-item>
            <a-form-item
                class="mb-2"
                label="Tên lưu log"
                name="fie_name"
                :rules="[{ required: true, message: 'Vui lòng nhập tên lưu log' }]"
            >
                <a-input placeholder="Tên lưu log" v-model:value="formState.fie_name"></a-input>
            </a-form-item>
            <a-form-item class="mb-2" name="fie_type" label="Kiểu dữ liệu">
                <select-custom
                    placeholder="Kiểu dữ liệu"
                    :options="others.fie_type"
                    show-search
                    v-model:value="formState.fie_type"
                    :filter-option="utils.filterOption"
                />
            </a-form-item>
            <a-form-item
                class="mb-2"
                label="Biến lấy giá trị"
                name="fie_variable"
                extra="Nếu kiểu dữ liệu là Constant. Ko bao gồm $."
                v-if="formState.fie_type == constants.constant"
            >
                <a-input placeholder="Biến lấy giá trị" v-model:value="formState.fie_variable"></a-input>
            </a-form-item>
            <template v-if="formState.fie_type == constants.database">
                <a-form-item
                    class="mb-2"
                    label="Bảng lấy dữ liệu"
                    name="fie_table_target"
                    extra="Nếu kiểu dữ liệu là Database"
                >
                    <a-input placeholder="Bảng lấy dữ liệu" v-model:value="formState.fie_table_target"></a-input>
                </a-form-item>
                <a-form-item class="mb-2" label="Trường ID" name="fie_field_id" extra="Trường ID của bảng lấy dữ liệu">
                    <a-input placeholder="Trường ID" v-model:value="formState.fie_field_id"></a-input>
                </a-form-item>
                <a-form-item
                    class="mb-2"
                    label="Trường giá trị "
                    name="fie_field_value"
                    extra="Trường lấy ra text hiển thị của bảng lấy dữ liệu"
                >
                    <a-input placeholder="Trường giá trị" v-model:value="formState.fie_field_value"></a-input>
                </a-form-item>
            </template>
            <a-form-item class="mb-2" label="Mô tả" name="fie_description">
                <a-textarea placeholder="Mô tả" v-model:value="formState.fie_description" allow-clear></a-textarea>
            </a-form-item>
            <a-row class="mb-2">
                <a-col :span="5" :xs="0" :sm="5" :md="5" :lg="5" :xl="5" :xxl="5"></a-col>
                <a-button type="primary" html-type="submit" :loading="loading">{{
                    formState.fie_id ? 'Cập nhật' : 'Thêm mới'
                }}</a-button>
            </a-row>
        </a-form>
    </a-modal>
</template>
<script>
import { ref } from '@lib/vue';
import {
    MessageTwoTone,
    CloseCircleTwoTone,
    CheckCircleTwoTone,
    EditTwoTone,
    PlusCircleOutlined
} from '@lib/@ant-design/icons-vue';
import { Tag, notification, Modal, message, Checkbox, InputNumber, AutoComplete } from '@lib/ant-design-vue';
import utils from '@root/utils';
import SelectCustom from '@admin/components/select-custom.vue';

export default {
    components: {
        MessageTwoTone,
        ATag: Tag,
        AModal: Modal,
        ACheckbox: Checkbox,
        CloseCircleTwoTone,
        CheckCircleTwoTone,
        AInputNumber: InputNumber,
        AAutoComplete: AutoComplete,
        EditTwoTone,
        SelectCustom,
        PlusCircleOutlined
    },
    data: () => ({
        others: {},
        columns: [
            {
                title: '#',
                dataIndex: 'stt',
                width: '35px',
                align: 'center'
            },
            {
                title: 'Bảng',
                dataIndex: 'talo_table',
                width: '170px'
            },
            {
                title: 'Tên trường',
                dataIndex: 'fie_field',
                sorter: true,
                sortDirections: ['descend', 'ascend'],
                width: '150px'
            },
            {
                title: 'Tên lưu log',
                dataIndex: 'fie_name',
                width: '200px',
                sorter: true
            },
            {
                title: 'Kiểu DL',
                dataIndex: 'fie_type_text',
                width: '100px'
            },
            {
                title: 'Bảng lấy DL',
                dataIndex: 'fie_table_target',
                width: '100px'
            },
            {
                title: 'Trường ID',
                dataIndex: 'fie_field_id',
                width: '100px'
            },
            {
                title: 'Trường giá trị',
                dataIndex: 'fie_field_value',
                width: '150px'
            },
            {
                title: 'Biến giá trị',
                dataIndex: 'fie_variable',
                width: '70px',
                sorter: true
            },
            {
                title: 'Mô tả',
                dataIndex: 'fie_description',
                width: '70px'
            },
            {
                title: 'Sửa',
                dataIndex: 'edit',
                width: '30px',
                align: 'center'
            }
        ],
        loading: false,
        data_source: [],
        formSearch: {
            talo_table: '',
            fie_field: '',
            fie_name: '',
            fie_type: null,
            fie_table_target: '',
            fie_variable: ''
        },
        open: false,
        formState: {
            fie_table_id: null,
            fie_field: '',
            fie_name: '',
            fie_type: null,
            fie_table_target: '',
            fie_variable: '',
            fie_description: '',
            fie_field_id: '',
            fie_field_value: '',
            fie_id: null
        },
        modalTitle: '',
        pagination: {},
        constants: {}
    }),
    computed: {},
    created() {
        this.data_source = window.appData.rows;
        this.pagination = this.lodash.assign(this.pagination, window.appData.pagination);
        this.constants = this.lodash.assign(this.constants, window.appData.constants);
        this.others = this.lodash.assign(this.others, window.appData.others);
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
                ...this.formSearch,
                fie_type: this.formSearch.fie_type ?? -9999
            };
            const params = {
                ...searchParams,
                sort: sorter.order == 'ascend' ? 'asc' : 'desc',
                pageSize: pag.pageSize,
                page: pag.current
            };
            if (sorter.field == 'fie_field') {
                params.fieldsort = 'fie_field';
            } else if (sorter.field == 'fie_name') {
                params.fieldsort = 'fie_name';
            } else if (sorter.field == 'fie_variable') {
                params.fieldsort = 'fie_variable';
            }

            await this.getData(params);
        },
        async getData(params = {}) {
            this.loading = true;
            params = {
                ...this.formSearch,
                ...params,
                fie_type: this.formSearch.fie_type ?? -9999,
                page: params.page ?? this.pagination.current ?? 1,
                json: 1
            };
            let res = await $.ajax({
                url: `field_log_list.php?${new URLSearchParams(params).toString()}`,
                dataType: 'json'
            });

            if (this.lodash.toNumber(res.success) == 1) {
                this.data_source = res.data.rows;
                this.pagination = this.lodash.assign(this.pagination, res.data.pagination);
            } else {
                for (let i in res.data) {
                    notification.error({
                        message: res.data[i]
                    });
                }
            }
            this.loading = false;
        },
        // Mở form edit
        openModal(record = null) {
            if (record) {
                this.formState = {
                    fie_table_id: record.fie_table_id,
                    fie_field: record.fie_field,
                    fie_name: record.fie_name,
                    fie_type: record.fie_type,
                    fie_table_target: record.fie_table_target,
                    fie_variable: record.fie_variable,
                    fie_description: record.fie_description,
                    fie_field_id: record.fie_field_id,
                    fie_field_value: record.fie_field_value,
                    fie_id: record.fie_id
                };
                this.modalTitle = 'Cập nhật bảng lưu log: ' + record.talo_table;
            } else {
                this.formState = {
                    fie_table_id: null,
                    fie_field: '',
                    fie_name: '',
                    fie_type: null,
                    fie_table_target: '',
                    fie_variable: '',
                    fie_description: '',
                    fie_field_id: '',
                    fie_field_value: '',
                    fie_id: null
                };
                this.modalTitle = 'Thêm mới bảng lưu log';
            }
            this.open = true;
        },
        // Submit form
        async handleSubmitForm() {
            this.loading = true;
            let url = '';
            if (this.formState.fie_id > 0) {
                url = `field_log_edit.php?id=${this.formState.fie_id}`;
            } else {
                url = `field_log_create.php`;
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
        }
    }
};
</script>
<style scoped>
.custom-row {
    border: 1px solid #cccc;
}

.custom-col {
    border-right: 1px solid #cccc;
    padding: 5px;
}
</style>
