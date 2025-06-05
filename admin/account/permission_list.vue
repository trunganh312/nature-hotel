<template>
    <a-form :model="formSearch" name="horizontal_login" class="mt-3" layout="inline" @finish="getData">
        <a-form-item class="mb-3" name="per_name">
            <a-input v-model:value="formSearch.per_name" placeholder="Tên quyền"></a-input>
        </a-form-item>
        <a-form-item class="mb-3" name="per_alias">
            <a-input v-model:value="formSearch.per_alias" placeholder="Alias"></a-input>
        </a-form-item>
        <a-form-item class="mb-3" name="mod_group">
            <select-custom
                style="width: 150px"
                placeholder="Nhóm"
                :options="others.mod_group"
                show-search
                allow-clear
                v-model:value="formSearch.mod_group"
                :filter-option="utils.filterOption"
            />
        </a-form-item>
        <a-form-item class="mb-3" name="per_module_id">
            <select-custom
                style="width: 250px"
                placeholder="Module"
                :options="others.modules"
                show-search
                allow-clear
                v-model:value="formSearch.per_module_id"
                :filter-option="utils.filterOption"
            />
        </a-form-item>
        <a-form-item class="mb-3">
            <a-button :disabled="formSearch.disabled" type="primary" html-type="submit">Tìm kiếm</a-button>
        </a-form-item>
        <a-form-item class="mb-3" v-if="permissions.super">
            <a-button class="ant-btn-primary-custom" @click="openFormPermission(null)">
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
            <template v-if="column.dataIndex === 'stt'">{{ index + 1 }}</template>
            <template v-if="column.dataIndex === 'per_company_config'">
                <a-checkbox
                    v-model:checked="record.per_company_config"
                    :checked-value="1"
                    :unchecked-value="0"
                    @change="changeStatus(record, 'per_company_config')"
                />
            </template>
            <template v-if="column.dataIndex === 'per_active'">
                <a-checkbox
                    v-model:checked="record.per_active"
                    :checked-value="1"
                    :unchecked-value="0"
                    @change="changeStatus(record, 'per_active')"
                />
            </template>
            <template v-if="column.dataIndex === 'per_check_owner'">
                <a-checkbox
                    v-model:checked="record.per_check_owner"
                    :checked-value="1"
                    :unchecked-value="0"
                    @change="changeStatus(record, 'per_check_owner')"
                />
            </template>
            <template v-if="column.dataIndex === 'per_allow_leader'">
                <a-checkbox
                    v-model:checked="record.per_allow_leader"
                    :checked-value="1"
                    :unchecked-value="0"
                    @change="changeStatus(record, 'per_allow_leader')"
                />
            </template>
            <template v-if="column.dataIndex === 'edit'">
                <EditTwoTone @click="openFormPermission(record)" />
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

    <!-- Modal thêm sửa -->
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
            <a-form-item class="mb-3" label="Hệ thống">
                {{ permissions.env ? 'user' : 'admin' }}
            </a-form-item>
            <a-form-item
                class="mb-3"
                name="per_module_id"
                :rules="[{ required: true, message: 'Vui lòng chọn module' }]"
                label="Module"
            >
                <select-custom
                    style="width: 100%"
                    placeholder="Module"
                    :options="others.modules"
                    show-search
                    v-model:value="formState.per_module_id"
                    :filter-option="utils.filterOption"
                    @change="handleChangeModule"
                />
            </a-form-item>
            <a-form-item class="mb-3" name="per_feature_id" label="Tính năng">
                <select-custom
                    style="width: 100%"
                    placeholder="Tính năng"
                    :options="featuresSelect"
                    allow-clear
                    show-search
                    v-model:value="formState.per_feature_id"
                    :filter-option="utils.filterOption"
                />
            </a-form-item>
            <a-form-item
                class="mb-3"
                label="Alias"
                name="per_alias"
                :rules="[{ required: true, message: 'Vui lòng nhập alias' }]"
                extra="Viết liền, nối nhau bởi dấu _"
            >
                <a-input placeholder="Alias" v-model:value="formState.per_alias"></a-input>
            </a-form-item>
            <a-form-item
                class="mb-3"
                label="Tên quyền"
                name="per_name"
                :rules="[{ required: true, message: 'Vui lòng nhập tên quyền' }]"
                extra="Viết liền, nối nhau bởi dấu _"
            >
                <a-input placeholder="Tên quyền" v-model:value="formState.per_name"></a-input>
            </a-form-item>
            <a-form-item class="mb-3" label="Mô tả" name="per_description">
                <a-textarea placeholder="Mô tả" v-model:value="formState.per_description" allow-clear></a-textarea>
            </a-form-item>
            <a-row class="mb-3" v-if="formState.per_id <= 0 && permissions.env">
                <a-col :span="5" :xs="0" :sm="5" :md="5" :lg="5" :xl="5" :xxl="5"></a-col>
                <a-checkbox v-model:checked="formState.per_company_config" :checked-value="1" :unchecked-value="0"
                    >Check quyền sở hữu dữ liệu theo cấu hình của công ty</a-checkbox
                >
            </a-row>
            <a-row class="mb-3" v-if="formState.per_id <= 0 && !permissions.env">
                <a-col :span="5" :xs="0" :sm="5" :md="5" :lg="5" :xl="5" :xxl="5"></a-col>
                <a-checkbox v-model:checked="formState.per_check_owner" :checked-value="1" :unchecked-value="0"
                    >Check quyền sở hữu dữ liệu</a-checkbox
                >
            </a-row>
            <a-row class="mb-3" v-if="formState.per_id <= 0 && !permissions.env">
                <a-col :span="5" :xs="0" :sm="5" :md="5" :lg="5" :xl="5" :xxl="5"></a-col>
                <a-checkbox v-model:checked="formState.per_allow_leader" :checked-value="1" :unchecked-value="0"
                    >Cho phép leader xử lý</a-checkbox
                >
            </a-row>
            <a-row class="mb-3">
                <a-col :span="5" :xs="0" :sm="5" :md="5" :lg="5" :xl="5" :xxl="5"></a-col>
                <a-button type="primary" html-type="submit" :loading="loading">{{
                    formState.per_id ? 'Cập nhật' : 'Thêm mới'
                }}</a-button>
            </a-row>
        </a-form>
    </a-modal>
</template>
<script>
import { Image, Tag, notification, Popconfirm, Mentions, Rate, Checkbox, AutoComplete } from '@lib/ant-design-vue';
import utils from '@root/utils';
import { ref } from '@lib/vue';
import { EditOutlined, DeleteTwoTone, EditTwoTone, PlusCircleOutlined } from '@lib/@ant-design/icons-vue';
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
        SelectCustom,
        PlusCircleOutlined
    },
    data: () => ({
        baseColumns: [
            {
                title: '#',
                dataIndex: 'stt',
                align: 'center',
                width: '35px',
                order: 0
            },
            {
                title: 'Nhóm',
                dataIndex: 'mod_group_text',
                width: '70px',
                order: 1
            },
            {
                title: 'Module',
                dataIndex: 'per_module_text',
                width: '200px',
                order: 2
            },
            {
                title: 'Tên quyền',
                dataIndex: 'per_name',
                sorter: true,
                sortDirections: ['descend', 'ascend'],
                width: '250px',
                order: 3
            },
            {
                title: 'Mô tả',
                dataIndex: 'per_description',
                width: '200px',
                order: 4
            },
            {
                title: 'Sửa',
                dataIndex: 'edit',
                align: 'center',
                width: '30px',
                order: 11
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
            per_name: '',
            per_module_id: null,
            mod_group: null,
            per_alias: '',
            disabled: false
        },
        size: 'large',
        others: {},
        permissions: {},
        formState: {
            per_module_id: null,
            per_feature_id: null,
            per_name: '',
            per_alias: '',
            per_description: '',
            per_check_owner: 0,
            per_allow_leader: 0,
            per_company_config: 0,
            per_id: 0
        },
        open: false,
        modalTitle: '',
        featuresSelect: []
    }),
    watch: {},
    computed: {
        columns() {
            // Conditionally include the "Sửa" column
            let baseColumns = [...this.baseColumns];
            // env là true thì là của user
            if (this.permissions.env) {
                baseColumns.push({
                    title: 'Config',
                    dataIndex: 'per_company_config',
                    align: 'center',
                    width: '30px',
                    sorter: true,
                    sortDirections: ['descend', 'ascend'],
                    order: 7
                });
            } else {
                baseColumns.push(
                    {
                        title: 'Owner',
                        dataIndex: 'per_check_owner',
                        align: 'center',
                        width: '30px',
                        sorter: true,
                        sortDirections: ['descend', 'ascend'],
                        order: 8
                    },
                    {
                        title: 'Leader',
                        dataIndex: 'per_allow_leader',
                        align: 'center',
                        width: '30px',
                        sorter: true,
                        sortDirections: ['descend', 'ascend'],
                        order: 9
                    }
                );
            }

            if (this.permissions.super) {
                baseColumns.push(
                    {
                        title: 'Alias',
                        dataIndex: 'per_alias',
                        width: '250px',
                        sorter: true,
                        sortDirections: ['descend', 'ascend'],
                        order: 5
                    },
                    {
                        title: 'Menu tính năng',
                        dataIndex: 'per_feature_text',
                        width: '200px',
                        sorter: true,
                        sortDirections: ['descend', 'ascend'],
                        order: 6
                    },
                    {
                        title: 'Act',
                        dataIndex: 'per_active',
                        align: 'center',
                        width: '30px',
                        sorter: true,
                        sortDirections: ['descend', 'ascend'],
                        order: 10
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
                ...this.formSearch,
                per_module_id:
                    this.formSearch.per_module_id == null || this.formSearch.per_module_id == undefined
                        ? -9999
                        : this.formSearch.per_module_id,
                mod_group: this.formSearch.mod_group || -9999
            };
            const params = {
                ...searchParams,
                sort: sorter.order == 'ascend' ? 'asc' : 'desc',
                pageSize: pag.pageSize
            };
            if (sorter.field == 'per_allow_leader') {
                params.fieldsort = 'per_allow_leader';
            } else if (sorter.field == 'per_check_owner') {
                params.fieldsort = 'per_check_owner';
            } else if (sorter.field == 'per_company_config') {
                params.fieldsort = 'per_company_config';
            } else if (sorter.field == 'per_active') {
                params.fieldsort = 'per_active';
            } else if (sorter.field == 'per_name') {
                params.fieldsort = 'per_name';
            } else if (sorter.field == 'per_alias') {
                params.fieldsort = 'per_alias';
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
            params.page = page;
            params = {
                ...params,
                ...this.formSearch,
                per_module_id:
                    this.formSearch.per_module_id == null || this.formSearch.per_module_id == undefined
                        ? -9999
                        : this.formSearch.per_module_id,
                mod_group: this.formSearch.mod_group || -9999,
                page: params.page ?? this.pagination.current ?? 1
            };
            params.json = 1;
            params.type = this.permissions.env ? 'user' : 'admin';
            let res = await $.ajax({
                url: `permission_list.php?${new URLSearchParams(params).toString()}`,
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
                ...values,
                per_module_id:
                    values.per_module_id == null || values.per_module_id == undefined ? -9999 : values.per_module_id,
                mod_group: values.mod_group || -9999
            };

            if (this.formSearch.disabled) return;

            this.formSearch.disabled = true;
            await this.getData(null, params);
            this.formSearch.disabled = false;
        },
        // Change status
        async changeStatus(record, field = 'per_active') {
            const params = {
                field,
                type: this.permissions.env ? 'user' : 'admin',
                id: record.per_id
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
                await this.getData(null, {});
            } else {
                notification.error({
                    message: 'Cập nhật thất bại!'
                });
            }
        },
        // Mở form cập nhật chủ sở hữu
        async openFormPermission(record) {
            if (record) {
                this.formState = {
                    per_module_id: record.per_module_id,
                    per_feature_id: record.per_feature_id,
                    per_name: record.per_name,
                    per_alias: record.per_alias,
                    per_description: record.per_description,
                    per_check_owner: record.per_check_owner,
                    per_allow_leader: record.per_allow_leader,
                    per_company_config: record.per_company_config,
                    per_id: record.per_id
                };
                this.modalTitle = 'Sửa thông tin quyền: ' + record.per_name;
                this.featuresSelect = await this.getFeatures(record.per_module_id);
            } else {
                this.formState = {
                    per_module_id: null,
                    per_feature_id: null,
                    per_name: '',
                    per_alias: '',
                    per_description: '',
                    per_check_owner: 0,
                    per_allow_leader: 0,
                    per_company_config: 0,
                    per_id: 0
                };
                this.modalTitle = 'Thêm mới quyền ' + this.permissions.env ? 'user' : 'admin';
            }
            this.open = true;
        },
        // Submit form
        async handleSubmitForm() {
            this.loading = true;
            let url = '';
            if (this.formState.per_id > 0) {
                url = `permission_edit.php?id=${this.formState.per_id}&type=${this.permissions.env ? 'user' : 'admin'}`;
            } else {
                url = `permission_create.php?type=${this.permissions.env ? 'user' : 'admin'}`;
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
                await this.getData(null);
            } else {
                for (let i in res.data) {
                    notification.error({
                        message: res.data[i]
                    });
                }
            }
            this.loading = false;
        },
        // Change module
        async handleChangeModule(id) {
            this.formState.per_feature_id = null;
            this.featuresSelect = await this.getFeatures(id);
        },
        async getFeatures(id) {
            const params = {
                type: 'module',
                json: 1,
                id: id
            };
            let res = await $.ajax({
                url: `/common/get_select_child.php?${new URLSearchParams(params).toString()}`,
                dataType: 'json'
            });
            const features = Object.entries(res.data).map(([value, label]) => ({
                value: Number(value), // Convert string keys to numbers
                label: label
            }));
            return features;
        }
    }
};
</script>
<style></style>
