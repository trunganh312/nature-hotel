<template>
    <a-form :model="formSearch" name="horizontal_login" class="mt-3" layout="inline" @finish="onSearch">
        <a-form-item class="mb-3" name="mod_name">
            <a-input v-model:value="formSearch.mod_name" placeholder="Tên module"></a-input>
        </a-form-item>
        <a-form-item class="mb-3" name="mod_folder">
            <a-input v-model:value="formSearch.mod_folder" placeholder="Tên folder"></a-input>
        </a-form-item>
        <a-form-item class="mb-3">
            <a-button :disabled="formSearch.disabled" type="primary" html-type="submit">Tìm kiếm</a-button>
        </a-form-item>
        <a-form-item class="mb-3">
            <a-button class="ant-btn-primary-custom" @click="openModalModule(null)">
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
            <template v-if="column.dataIndex === 'edit'"> <EditTwoTone @click="openModalModule(record)" /></template>
            <template v-if="column.dataIndex === 'file'">
                <table class="table table_child" style="border: 1px solid #f0f0f0; border-radius: 0">
                    <tbody>
                        <tr v-if="record.total_menu <= 0">
                            <td class="text-center" style="border: none !important">
                                <a-typography-link @click="openModalFeature(null, record)"
                                    >Thêm tính năng</a-typography-link
                                >
                            </td>
                        </tr>
                        <tr v-else v-for="(m, i) in record.menu" :key="i">
                            <td>{{ m.modf_name }}</td>
                            <td class="col_3" style="border-left: 1px solid #f0f0f0">{{ m.modf_file }}</td>
                            <td class="text-right col_4" style="border-left: 1px solid #f0f0f0">{{ m.modf_order }}</td>
                            <td class="text-center col_5" style="border-left: 1px solid #f0f0f0">
                                <EditTwoTone @click="openModalFeature(m, record)" />
                            </td>
                            <td
                                class="text-center col_6"
                                style="border-left: 1px solid #f0f0f0; border-right: 1px solid #f0f0f0"
                            >
                                <a-checkbox
                                    v-model:checked="m.modf_active"
                                    :checked-value="1"
                                    :unchecked-value="0"
                                    @change="changeStatus(m.modf_id, 'modf_active', 'module_feature')"
                                />
                            </td>

                            <td v-if="i == 0" :rowspan="record.total_menu" class="text-center col_7">
                                <a-typography-link @click="openModalFeature(null, record)">
                                    Thêm tính năng
                                </a-typography-link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </template>
            <template v-if="column.dataIndex === 'mod_icon'">
                <i :class="record.mod_icon"></i>
            </template>
            <template v-if="column.dataIndex === 'mod_active'">
                <a-checkbox
                    v-model:checked="record.mod_active"
                    :checked-value="1"
                    :unchecked-value="0"
                    @change="changeStatus(record.mod_id, 'mod_active')"
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

    <!-- Modal thêm/ sửa module -->
    <a-modal
        v-model:open="module.open"
        width="700px"
        :body-style="{ minHeight: '50vh', overflowY: 'auto' }"
        :title="module.modalTitle"
        :footer="null"
        :destroyOnClose="true"
    >
        <a-form
            :model="module.formState"
            class="mt-3 pl-3"
            :label-col="{ span: 5 }"
            :wrapper-col="{ span: 17 }"
            @finish="handleSubmitFormModule"
        >
            <a-form-item
                class="mb-3"
                label="Tên module"
                name="mod_name"
                :rules="[{ required: true, message: 'Vui lòng nhập tên module' }]"
            >
                <a-input placeholder="Tên module" v-model:value="module.formState.mod_name"></a-input>
            </a-form-item>
            <a-form-item
                label="Nhóm"
                class="mb-3"
                name="mod_group"
                :rules="[{ required: true, message: 'Vui lòng chọn nhóm' }]"
            >
                <select-custom
                    placeholder="Nhóm"
                    :options="others.mod_group"
                    show-search
                    v-model:value="module.formState.mod_group"
                    :filter-option="utils.filterOption"
                />
            </a-form-item>
            <a-form-item
                class="mb-3"
                label="Folder con"
                name="mod_folder"
                :rules="[{ required: true, message: 'Vui lòng nhập Folder con' }]"
            >
                <a-input placeholder="Folder con" v-model:value="module.formState.mod_folder"></a-input>
            </a-form-item>
            <a-form-item class="mb-3" label="Icon" name="mod_icon">
                <a-input placeholder="Icon" v-model:value="module.formState.mod_icon"></a-input>
            </a-form-item>
            <a-form-item class="mb-3" label="Thứ tự " name="mod_order">
                <a-input-number
                    style="width: 100%"
                    placeholder="Thứ tự "
                    v-model:value="module.formState.mod_order"
                ></a-input-number>
            </a-form-item>
            <a-form-item class="mb-3" label="Mô tả" name="mod_note">
                <a-textarea placeholder="Mô tả" v-model:value="module.formState.mod_note" allow-clear></a-textarea>
            </a-form-item>
            <a-row class="mb-3">
                <a-col :span="5" :xs="0" :sm="5" :md="5" :lg="5" :xl="5" :xxl="5"></a-col>
                <a-button type="primary" html-type="submit" :loading="loading">{{
                    module.formState.mod_id ? 'Cập nhật' : 'Thêm mới'
                }}</a-button>
            </a-row>
        </a-form>
    </a-modal>

    <!-- Modal thêm/ sửa tính năng -->
    <a-modal
        v-model:open="feature.open"
        width="700px"
        :body-style="{ minHeight: '50vh', overflowY: 'auto' }"
        :title="feature.modalTitle"
        :footer="null"
        :destroyOnClose="true"
    >
        <a-form
            :model="feature.formState"
            class="mt-3 pl-3"
            :label-col="{ span: 5 }"
            :wrapper-col="{ span: 17 }"
            @finish="handleSubmitFormFeature"
        >
            <a-form-item
                class="mb-3"
                label="Tên tính năng"
                name="modf_name"
                :rules="[{ required: true, message: 'Vui lòng nhập tên tính năng' }]"
            >
                <a-input placeholder="Tên tính năng" v-model:value="feature.formState.modf_name"></a-input>
            </a-form-item>
            <a-form-item label="Thuộc nhóm" class="mb-3" name="modf_parent_id">
                <select-custom
                    placeholder="Nhóm"
                    :options="feature.others.modf_parents"
                    show-search
                    allow-clear
                    v-model:value="feature.formState.modf_parent_id"
                    :filter-option="utils.filterOption"
                />
            </a-form-item>
            <a-form-item class="mb-3" label="File thực thi" name="modf_file">
                <a-input placeholder="File thực thi" v-model:value="feature.formState.modf_file"></a-input>
            </a-form-item>
            <a-form-item class="mb-3" label="Thứ tự " name="modf_order">
                <a-input-number
                    style="width: 100%"
                    placeholder="Thứ tự "
                    v-model:value="feature.formState.modf_order"
                ></a-input-number>
            </a-form-item>
            <a-form-item class="mb-3" label="Mô tả" name="modf_note">
                <a-textarea placeholder="Mô tả" v-model:value="feature.formState.modf_note" allow-clear></a-textarea>
            </a-form-item>
            <a-row class="mb-3">
                <a-col :span="5" :xs="0" :sm="5" :md="5" :lg="5" :xl="5" :xxl="5"></a-col>
                <a-button type="primary" html-type="submit" :loading="loading">{{
                    feature.formState.modf_id ? 'Cập nhật' : 'Thêm mới'
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
        columns: [
            {
                title: '#',
                dataIndex: 'stt',
                align: 'center',
                width: '35px',
                order: 1
            },
            {
                title: 'Tên module',
                dataIndex: 'mod_name',
                width: '250px',
                order: 2
            },
            {
                title: 'Folder',
                dataIndex: 'mod_folder',
                width: '100px',
                order: 3
            },
            {
                title: 'Tính năng của module',
                dataIndex: 'file',
                align: 'center',
                width: '800px',
                order: 4
            },
            {
                title: 'Icon',
                dataIndex: 'mod_icon',
                align: 'center',
                width: '30px',
                order: 5
            },

            {
                title: 'Thứ tự',
                dataIndex: 'mod_order',
                align: 'center',
                width: '70px',
                sorter: true,
                sortDirections: ['descend', 'ascend'],
                order: 8
            },
            {
                title: 'Act',
                dataIndex: 'mod_active',
                align: 'center',
                width: '30px',
                sorter: true,
                sortDirections: ['descend', 'ascend'],
                order: 9
            },
            {
                title: 'Sửa',
                dataIndex: 'edit',
                align: 'center',
                width: '30px',
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
            mod_name: '',
            mod_folder: '',
            disabled: false
        },
        size: 'large',
        others: {},
        permissions: {},
        module: {
            open: false,
            modalTitle: '',
            formState: {
                mod_id: 0,
                mod_name: '',
                mod_folder: '',
                mod_note: '',
                mod_icon: '',
                mod_order: 0,
                mod_group: null
            }
        },
        feature: {
            open: false,
            modalTitle: '',
            formState: {
                modf_id: 0,
                modf_name: '',
                modf_file: '',
                modf_note: '',
                modf_order: 0,
                modf_parent_id: null
            },
            others: {},
            module_info: {}
        }
    }),
    watch: {},
    computed: {},
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
            if (sorter.field == 'mod_order') {
                params.fieldsort = 'mod_order';
            } else if (sorter.field == 'mod_active') {
                params.fieldsort = 'mod_active';
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
                page: params.page ?? this.pagination.current ?? 1
            };
            params.json = 1;
            let res = await $.ajax({
                url: `module_list.php?${new URLSearchParams(params).toString()}`,
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
        async changeStatus(id, field = 'mod_active', type = '') {
            const params = {
                field,
                id,
                type
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
        // Mở form edit
        openModalModule(record = null) {
            if (record) {
                this.module.formState = {
                    mod_id: record.mod_id,
                    mod_name: record.mod_name,
                    mod_folder: record.mod_folder,
                    mod_note: record.mod_note,
                    mod_icon: record.mod_icon,
                    mod_order: record.mod_order,
                    mod_group: record.mod_group
                };
                this.module.modalTitle = 'Cập nhật module: ' + record.mod_name;
            } else {
                // Tìm trong datasource lấy order lớn nhất cộng 1
                let maxOrder = 0;
                this.data_source.forEach((item) => {
                    if (this.lodash.toNumber(item.mod_order) > maxOrder) {
                        maxOrder = this.lodash.toNumber(item.mod_order);
                    }
                });
                this.module.formState = {
                    mod_id: 0,
                    mod_name: '',
                    mod_folder: '',
                    mod_note: '',
                    mod_icon: '',
                    mod_order: maxOrder + 1,
                    mod_group: null
                };
                this.module.modalTitle = 'Thêm mới module';
            }
            this.module.open = true;
        },
        // Submit form
        async handleSubmitFormModule() {
            this.loading = true;
            let url = '';
            if (this.module.formState.mod_id > 0) {
                url = `module_edit.php?id=${this.module.formState.mod_id}`;
            } else {
                url = `module_create.php`;
            }
            let res = await $.ajax({
                url: url,
                type: 'POST',
                data: this.module.formState,
                dataType: 'json'
            });
            if (this.lodash.toNumber(res.success) == 1) {
                notification.success({
                    placement: 'bottomRight',
                    message: 'Cập nhật thành công'
                });
                this.module.open = false;
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
        // Mở form edit
        async openModalFeature(record = null, module_info = {}) {
            this.feature.module_info = module_info;
            // Call api để lấy về dữ liệu ô select
            let res = await $.ajax({
                url: `feature_create.php?module_id=${module_info.mod_id}&json=1`,
                dataType: 'json'
            });
            if (res.success == 1) {
                this.feature.others = res.data.others;
                if (record) {
                    this.feature.others.modf_parents = this.feature.others.modf_parents.filter(
                        (item) => item.value != record.modf_id
                    );
                    this.feature.formState = {
                        modf_id: record.modf_id,
                        modf_name: record.modf_name,
                        modf_file: record.modf_file,
                        modf_note: record.modf_note,
                        modf_order: record.modf_order,
                        modf_parent_id: record.modf_parent_id == 0 ? null : record.modf_parent_id
                    };
                    this.feature.modalTitle = 'Sửa tính năng của module ' + module_info.mod_name;
                } else {
                    // Tìm trong datasource lấy order lớn nhất cộng 1
                    let maxOrder = 0;
                    module_info.menu.forEach((item) => {
                        if (this.lodash.toNumber(item.modf_order) > maxOrder) {
                            maxOrder = this.lodash.toNumber(item.modf_order);
                        }
                    });
                    this.feature.formState = {
                        modf_id: 0,
                        modf_name: '',
                        modf_file: '',
                        modf_note: '',
                        modf_order: maxOrder + 1,
                        modf_parent_id: null
                    };
                    this.feature.modalTitle = 'Thêm tính năng cho module ' + module_info.mod_name;
                }
                this.feature.open = true;
            }
        },
        // Submit form
        async handleSubmitFormFeature() {
            this.loading = true;
            let url = '';
            if (this.feature.formState.modf_id > 0) {
                url = `feature_edit.php?id=${this.feature.formState.modf_id}`;
            } else {
                url = `feature_create.php?module_id=${this.feature.module_info.mod_id}`;
            }
            let res = await $.ajax({
                url: url,
                type: 'POST',
                data: this.feature.formState,
                dataType: 'json'
            });
            if (this.lodash.toNumber(res.success) == 1) {
                notification.success({
                    placement: 'bottomRight',
                    message: 'Cập nhật thành công'
                });
                this.feature.open = false;
                await this.getData(null);
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
.col_2 {
    width: 20%;
}
.col_3 {
    width: 25%;
}
.col_4 {
    width: 5%;
}
.col_5 {
    width: 5%;
}
.col_6 {
    width: 5%;
}
.col_7 {
    width: 15%;
}
</style>
