<template>
    <a-form :model="formSearch" name="horizontal_login" class="mt-3" layout="inline" @finish="getData">
        <a-form-item class="mb-3" name="talo_table">
            <a-input v-model:value="formSearch.talo_table" placeholder="Tên bảng"></a-input>
        </a-form-item>
        <a-form-item class="mb-3">
            <a-button type="primary" html-type="submit" style="width: 100%">Tìm kiếm</a-button>
        </a-form-item>
        <a-form-item class="mb-3">
            <a-button class="ant-btn-primary-custom" style="width: 100%" @click="openFormTable(null)">
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
                <EditTwoTone @click="openFormTable(record)" />
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
                class="mb-3"
                label="Tên bảng"
                name="talo_table"
                :rules="[{ required: true, message: 'Vui lòng nhập tên bảng' }]"
            >
                <a-input placeholder="Tên bảng" v-model:value="formState.talo_table"></a-input>
            </a-form-item>
            <a-form-item class="mb-3" label="Ghi chú" name="talo_note">
                <a-textarea placeholder="Ghi chú" v-model:value="formState.talo_note" allow-clear></a-textarea>
            </a-form-item>
            <a-row class="mb-3">
                <a-col :span="5" :xs="0" :sm="5" :md="5" :lg="5" :xl="5" :xxl="5"></a-col>
                <a-button type="primary" html-type="submit" :loading="loading">{{
                    formState.talo_id ? 'Cập nhật' : 'Thêm mới'
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
                title: 'Tên dịch vụ',
                dataIndex: 'talo_table'
            },
            {
                title: 'Ghi chú',
                dataIndex: 'talo_note'
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
            talo_note: ''
        },
        open: false,
        formState: {
            talo_table: '',
            talo_note: '',
            talo_id: null
        },
        modalTitle: '',
        pagination: {}
    }),
    computed: {},
    created() {
        this.data_source = window.appData.rows;
        this.pagination = this.lodash.assign(this.pagination, window.appData.pagination);
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
                page: pag.current
            };

            await this.getData(params);
        },
        async getData(params = {}) {
            this.loading = true;
            params = {
                ...this.formSearch,
                ...params,
                page: params.page ?? this.pagination.current ?? 1,
                json: 1
            };
            let res = await $.ajax({
                url: `table_log_list.php?${new URLSearchParams(params).toString()}`,
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
        openFormTable(record = null) {
            if (record) {
                this.formState = {
                    talo_table: record.talo_table || '',
                    talo_note: record.talo_note || '',
                    talo_id: record.talo_id || 0
                };
                this.modalTitle = 'Cập nhật bảng lưu log: ' + record.talo_table;
            } else {
                this.formState = {
                    talo_table: '',
                    talo_note: '',
                    talo_id: 0
                };
                this.modalTitle = 'Thêm mới bảng lưu log';
            }
            this.open = true;
        },
        // Submit form
        async handleSubmitForm() {
            this.loading = true;
            let url = '';
            if (this.formState.talo_id > 0) {
                url = `table_log_edit.php?id=${this.formState.talo_id}`;
            } else {
                url = `table_log_create.php`;
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
