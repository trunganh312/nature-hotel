<template>
    <a-form :model="formSearch" name="horizontal_login" class="mt-3" layout="inline" @finish="getData">
        <a-form-item class="mb-3" name="bak_name">
            <a-input v-model:value="formSearch.bak_name" placeholder="Thông tin ngân hàng"></a-input>
        </a-form-item>
        <a-form-item class="mb-3">
            <a-button type="primary" html-type="submit" style="width: 100%">Tìm kiếm</a-button>
        </a-form-item>
        <a-form-item class="mb-3" v-if="permissions.hasCreate">
            <a-button class="ant-btn-primary-custom" style="width: 100%" @click="openFormBank(null)">
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
                <EditTwoTone @click="openFormBank(record)" />
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
            :label-col="{ span: 6 }"
            :wrapper-col="{ span: 17 }"
            @finish="handleSubmitForm"
        >
            <a-form-item
                class="mb-3"
                label="Tên ngân hàng"
                name="bak_name"
                :rules="[{ required: true, message: 'Vui lòng nhập tên ngân hàng' }]"
            >
                <a-input placeholder="Tên ngân hàng" v-model:value="formState.bak_name"></a-input>
            </a-form-item>
            <a-form-item
                class="mb-3"
                label="Tên ngân hàng (EN)"
                name="bak_name_en"
                :rules="[{ required: true, message: 'Vui lòng nhập tên ngân hàng (EN)' }]"
            >
                <a-input placeholder="Tên ngân hàng (EN)" v-model:value="formState.bak_name_en"></a-input>
            </a-form-item>
            <a-form-item
                class="mb-3"
                label="BIN"
                name="bak_bin"
                :rules="[{ required: true, message: 'Vui lòng nhập mã BIN' }]"
            >
                <a-input placeholder="BIN" v-model:value="formState.bak_bin"></a-input>
            </a-form-item>
            <a-form-item
                class="mb-3"
                label="Tên viết tắt"
                name="bak_abbreviation"
                :rules="[{ required: true, message: 'Vui lòng nhập tên viết tắt' }]"
            >
                <a-input placeholder="Tên viết tắt" v-model:value="formState.bak_abbreviation"></a-input>
            </a-form-item>
            <a-row class="mb-3">
                <a-col :span="6" :xs="0" :sm="6" :md="6" :lg="6" :xl="6" :xxl="6"></a-col>
                <a-button type="primary" html-type="submit" :loading="loading">{{
                    formState.bak_id ? 'Cập nhật' : 'Thêm mới'
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
        baseColumns: [
            {
                title: '#',
                dataIndex: 'stt',
                width: '35px',
                align: 'center'
            },
            {
                title: 'Tên ngân hàng',
                dataIndex: 'bak_name',
                width: '300px'
            },
            {
                title: 'Tên ngân hàng (EN)',
                dataIndex: 'bak_name_en',
                width: '350px'
            },
            {
                title: 'BIN',
                dataIndex: 'bak_bin',
                width: '70px'
            },
            {
                title: 'Tên viết tắt',
                dataIndex: 'bak_abbreviation',
                width: '100px'
            }
        ],
        loading: false,
        data_source: [],
        banks: [],
        formSearch: {
            bak_name: ''
        },
        permissions: {},
        open: false,
        formState: {
            bak_id: 0,
            bak_name: '',
            bak_name_en: '',
            bak_bin: '',
            bak_abbreviation: ''
        },
        modalTitle: '',
        pagination: {}
    }),
    computed: {
        columns() {
            // Conditionally include the "Sửa" column
            let baseColumns = [...this.baseColumns];
            if (this.permissions.hasEdit) {
                baseColumns.push({
                    title: 'Sửa',
                    dataIndex: 'edit',
                    width: '30px',
                    align: 'center'
                });
            }

            return baseColumns;
        }
    },
    created() {
        this.data_source = window.appData.rows;
        this.permissions = this.lodash.assign(this.permissions, window.appData.permissions);
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
                sort: sorter.order == 'ascend' ? 'asc' : 'desc',
                page: pag.current,
                pageSize: pag.pageSize
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
                url: `bank_list.php?${new URLSearchParams(params).toString()}`,
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
        openFormBank(record = null) {
            if (record) {
                this.formState = {
                    ...record
                };
                this.modalTitle = 'Cập nhật ngân hàng: ' + record.bak_name;
            } else {
                this.formState = {};
                this.modalTitle = 'Thêm mới ngân hàng';
            }
            this.open = true;
        },
        // Submit form
        async handleSubmitForm() {
            this.loading = true;
            let url = '';
            if (this.formState.bak_id > 0) {
                url = `bank_edit.php?id=${this.formState.bak_id}`;
            } else {
                url = `bank_create.php`;
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
