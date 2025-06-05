<template>
    <a-form :model="formSearch" name="horizontal_login" class="mt-3" layout="inline" @finish="getData">
        <a-form-item class="mb-3" name="doc_name">
            <a-input v-model:value="formSearch.doc_name" placeholder="Tên danh mục"></a-input>
        </a-form-item>
        <a-form-item class="mb-3" name="doc_parent_id">
            <select-custom
                style="width: 300px"
                placeholder="Chọn cấp cha"
                :options="others.doc_parents"
                show-search
                v-model:value="formSearch.doc_parent_id"
                :filter-option="utils.filterOption"
                allow-clear
            />
        </a-form-item>
        <a-form-item class="mb-3">
            <a-button type="primary" html-type="submit" style="width: 100%">Tìm kiếm</a-button>
        </a-form-item>
        <a-form-item class="mb-3" v-if="permissions.hasCreate">
            <a-button class="ant-btn-primary-custom" style="width: 100%" @click="handleRedirect(null)">
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
            <template v-if="column.dataIndex == 'active'">
                <a-checkbox
                    v-model:checked="record.doc_active"
                    @change="handleChangeActive(record)"
                    :checked-value="1"
                    :unchecked-value="0"
                ></a-checkbox>
            </template>
            <template v-if="column.dataIndex === 'edit'">
                <EditTwoTone @click="handleRedirect(record)" />
            </template>
            <template v-if="column.dataIndex === 'doc_icon'"> <i :class="record.doc_icon"></i></template>
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
        baseColumns: [
            {
                title: '#',
                dataIndex: 'stt',
                width: '30px',
                align: 'center'
            },
            {
                title: 'Tên danh mục',
                dataIndex: 'doc_name'
            },
            {
                title: 'Tên danh mục cha',
                dataIndex: 'doc_parent_name'
            },
            {
                title: 'Icon',
                dataIndex: 'doc_icon',
                align: 'center'
            },
            {
                title: 'Thứ tự',
                dataIndex: 'doc_order',
                align: 'center'
            }
        ],
        loading: false,
        data_source: [],
        formSearch: {
            doc_name: '',
            doc_parent_id: null
        },
        permissions: {},
        pagination: {
            total: 0,
            current: 0,
            pageSize: 30
        }
    }),
    computed: {
        columns() {
            // Conditionally include the "Sửa" column
            let baseColumns = [...this.baseColumns];
            if (this.permissions.hasEdit) {
                baseColumns.push(
                    {
                        title: 'Act',
                        dataIndex: 'active',
                        width: '30px',
                        align: 'center'
                    },
                    {
                        title: 'Sửa',
                        dataIndex: 'edit',
                        width: '30px',
                        align: 'center'
                    }
                );
            }

            return baseColumns;
        }
    },
    created() {
        this.data_source = window.appData.rows;
        this.permissions = this.lodash.assign(this.permissions, window.appData.permissions);
        this.pagination = this.lodash.assign(this.pagination, window.appData.pagination);
        this.others = this.lodash.assign(this.others, window.appData.others);
        this.formSearch = this.lodash.assign(this.formSearch, window.appData.params);
        this.formSearch = {
            ...this.formSearch,
            doc_parent_id: this.formSearch.doc_parent_id ? this.formSearch.doc_parent_id : null
        };
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
            const formDataSearch = {
                ...this.formSearch,
                pageSize: pag.pageSize,
                page: pag.current
            };
            await this.getData(formDataSearch);
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
                url: `document_list.php?${new URLSearchParams(params).toString()}`,
                dataType: 'json'
            });

            if (this.lodash.toNumber(res.success) == 1) {
                this.data_source = res.data.rows;
                this.pagination = this.lodash.assign(this.pagination, res.data.pagination);
                this.params = this.lodash.assign(this.params, res.data.params);
            } else {
                for (let i in res.data) {
                    notification.error({
                        message: res.data[i]
                    });
                }
            }
            this.loading = false;
        },
        async handleChangeActive(record, field = 'doc_active') {
            const id = record.doc_id;
            this.loading = true;
            let res = await $.ajax({
                url: `/common/active.php?field=${field}&id=${id}`,
                type: 'GET',
                dataType: 'json'
            });

            if (this.lodash.toNumber(res.success) == 1) {
                notification.success({
                    placement: 'bottomRight',
                    message: 'Cập nhật thành công'
                });
            } else {
                for (let i in res.data) {
                    notification.error({
                        message: res.data[i]
                    });
                }
            }
            this.loading = false;
        },
        // Submit form
        async handleSubmitForm() {
            this.loading = true;
            let url = '';
            if (this.formState.atn_id > 0) {
                url = `attribute_edit.php?id=${this.formState.atn_id}`;
            } else {
                url = `attribute_create.php`;
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
        handleRedirect(record) {
            if (record?.doc_id) {
                window.location.href = `/general/document_edit.php?id=${record.doc_id}`;
            } else {
                window.location.href = `/general/document_create.php`;
            }
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
