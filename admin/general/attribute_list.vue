<template>
    <a-form :model="formSearch" name="horizontal_login" class="mt-3" layout="inline" @finish="getData">
        <a-form-item class="mb-3" name="atn_name">
            <a-input v-model:value="formSearch.atn_name" placeholder="Tên thuộc tính"></a-input>
        </a-form-item>
        <a-form-item class="mb-3" name="atn_group">
            <select-custom
                style="width: 150px"
                placeholder="Nhóm"
                :options="others.atn_group"
                show-search
                allow-clear
                v-model:value="formSearch.atn_group"
                :filter-option="utils.filterOption"
            />
        </a-form-item>
        <a-form-item class="mb-3" name="atn_type">
            <select-custom
                style="width: 150px"
                placeholder="Kiểu"
                :options="others.atn_type"
                show-search
                allow-clear
                v-model:value="formSearch.atn_type"
                :filter-option="utils.filterOption"
            />
        </a-form-item>
        <a-form-item class="mb-3" name="atn_alias_search">
            <a-input v-model:value="formSearch.atn_alias_search" placeholder="Alias search"></a-input>
        </a-form-item>
        <a-form-item class="mb-3">
            <a-button type="primary" html-type="submit" style="width: 100%">Tìm kiếm</a-button>
        </a-form-item>
        <a-form-item class="mb-3" v-if="permissions.hasCreate">
            <a-button class="ant-btn-primary-custom" style="width: 100%" @click="openFormAttribute(null)">
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
        @change="handleTableChange"
        bordered
        :loading="loading"
    >
        <template #bodyCell="{ column, record, index }">
            <template v-if="column.dataIndex === 'stt'">
                {{ index + 1 }}
            </template>
            <template v-if="column.dataIndex == 'atn_list_value'">
                <a-typography-link v-if="permissions.hasValue" @click="openListAttribute(record)">
                    Xem/Cập nhật giá trị</a-typography-link
                >
                <span v-else> Xem/Cập nhật giá trị </span>
            </template>
            <template v-if="column.dataIndex == 'active'">
                <a-checkbox
                    v-model:checked="record.atn_active"
                    @change="handleChangeActive(record, 'atn_active')"
                    :checked-value="1"
                    :unchecked-value="0"
                ></a-checkbox>
            </template>
            <template v-if="column.dataIndex == 'atn_hot'">
                <a-checkbox
                    v-model:checked="record.atn_hot"
                    @change="handleChangeActive(record, 'atn_hot')"
                    :checked-value="1"
                    :unchecked-value="0"
                ></a-checkbox>
            </template>
            <template v-if="column.dataIndex == 'atn_sennet_active'">
                <a-checkbox
                    v-model:checked="record.atn_sennet_active"
                    @change="handleChangeActive(record, 'atn_sennet_active')"
                    :checked-value="1"
                    :unchecked-value="0"
                ></a-checkbox>
            </template>
            <template v-if="column.dataIndex == 'atn_show_filter'">
                <a-checkbox
                    v-model:checked="record.atn_show_filter"
                    @change="handleChangeActive(record, 'atn_show_filter')"
                    :checked-value="1"
                    :unchecked-value="0"
                ></a-checkbox>
            </template>
            <template v-if="column.dataIndex == 'atn_join_meta'">
                <a-checkbox
                    v-model:checked="record.atn_join_meta"
                    @change="handleChangeActive(record, 'atn_join_meta')"
                    :checked-value="1"
                    :unchecked-value="0"
                ></a-checkbox>
            </template>
            <template v-if="column.dataIndex == 'atn_canonical'">
                <a-checkbox
                    v-model:checked="record.atn_canonical"
                    @change="handleChangeActive(record, 'atn_canonical')"
                    :checked-value="1"
                    :unchecked-value="0"
                ></a-checkbox>
            </template>
            <template v-if="column.dataIndex == 'atn_require'">
                <a-checkbox
                    v-model:checked="record.atn_require"
                    @change="handleChangeActive(record, 'atn_require')"
                    :checked-value="1"
                    :unchecked-value="0"
                ></a-checkbox>
            </template>
            <template v-if="column.dataIndex === 'edit'">
                <EditTwoTone @click="openFormAttribute(record)" />
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
            <a-form-item class="mb-3" label="Lưu ý" style="align-items: baseline">
                Nếu thêm mới mà làm tăng số column lên > 15 thì phải update code ở Class AttributeModel. Tạo thêm xong
                vào DB sửa ID thành 9,14,15 bị thiếu.
            </a-form-item>
            <a-form-item
                class="mb-3"
                name="atn_group"
                label="Nhóm thuộc tính"
                :rules="[{ required: true, message: 'Vui lòng chọn nhóm thuộc tính' }]"
            >
                <select-custom
                    style="width: 100%"
                    placeholder="Nhóm thuộc tính"
                    :options="others.atn_group"
                    show-search
                    v-model:value="formState.atn_group"
                    :filter-option="utils.filterOption"
                />
            </a-form-item>
            <a-form-item
                class="mb-3"
                label="Tên thuộc tính"
                name="atn_name"
                :rules="[{ required: true, message: 'Vui lòng nhập tên thuộc tính' }]"
            >
                <a-input placeholder="Tên thuộc tính" v-model:value="formState.atn_name"></a-input>
            </a-form-item>
            <a-form-item
                class="mb-3"
                name="atn_type"
                label="Kiểu thuộc tính"
                :rules="[{ required: true, message: 'Vui lòng chọn kiểu thuộc tính' }]"
            >
                <select-custom
                    style="width: 100%"
                    placeholder="Kiểu thuộc tính"
                    :options="others.atn_type"
                    show-search
                    v-model:value="formState.atn_type"
                    :filter-option="utils.filterOption"
                />
            </a-form-item>
            <!-- <a-row class="mb-3">
                <a-col :span="5" :xs="0" :sm="5" :md="5" :lg="5" :xl="5" :xxl="5"></a-col>
                <a-checkbox v-model:checked="formState.atn_show_filter" :checked-value="1" :unchecked-value="0"
                    >Cho phép lọc tìm kiếm</a-checkbox
                >
            </a-row>
            <a-row class="mb-3">
                <a-col :span="5" :xs="0" :sm="5" :md="5" :lg="5" :xl="5" :xxl="5"></a-col>
                <a-checkbox v-model:checked="formState.atn_require" :checked-value="1" :unchecked-value="0"
                    >Bắt buộc chọn giá trị</a-checkbox
                >
            </a-row> -->
            <a-form-item class="mb-3" label="Alias search" name="atn_alias_search">
                <a-input placeholder="Alias search" v-model:value="formState.atn_alias_search"></a-input>
            </a-form-item>
            <a-form-item class="mb-3" label="Thứ tự" name="atn_order">
                <a-input-number placeholder="Thứ tự" v-model:value="formState.atn_order"></a-input-number>
            </a-form-item>
            <a-form-item class="mb-3" label="Text ghép meta" name="atn_text_join_meta">
                <a-input placeholder="Text ghép meta" v-model:value="formState.atn_text_join_meta"></a-input>
            </a-form-item>
            <a-form-item class="mb-3" label="Mô tả" name="atn_note">
                <a-textarea placeholder="Mô tả" v-model:value="formState.atn_note" allow-clear></a-textarea>
            </a-form-item>
            <a-row class="mb-3">
                <a-col :span="5" :xs="0" :sm="5" :md="5" :lg="5" :xl="5" :xxl="5"></a-col>
                <a-button type="primary" html-type="submit" :loading="loading">{{
                    formState.atn_id ? 'Cập nhật' : 'Thêm mới'
                }}</a-button>
            </a-row>
        </a-form>
    </a-modal>

    <!-- Modal xem danh sách giá trị của từng thuộc tính -->
    <a-modal
        v-model:open="attribute_list.open"
        width="1000px"
        :body-style="{ maxHeight: '80vh', overflowY: 'auto' }"
        :title="attribute_list.modalTitle"
        :footer="null"
        :destroyOnClose="true"
        centered
    >
        <a-form
            :model="attribute_list.formSearch"
            name="horizontal_login"
            class="mt-3"
            layout="inline"
            @finish="getDataValue"
        >
            <a-form-item class="mb-3" name="atv_name">
                <a-input v-model:value="attribute_list.formSearch.atv_name" placeholder="Tên giá trị"></a-input>
            </a-form-item>
            <a-form-item class="mb-3">
                <a-button type="primary" html-type="submit" style="width: 100%">Tìm kiếm</a-button>
            </a-form-item>
            <a-form-item class="mb-3" v-if="attribute_list.permissions.hasCreate">
                <a-button type="primary" style="width: 100%" @click="openFormAttributeValue(null)">
                    <template #icon>
                        <PlusCircleOutlined />
                    </template>
                    Thêm mới
                </a-button>
            </a-form-item>
        </a-form>
        <a-table
            :columns="attribute_list.columns"
            :data-source="attribute_list.data_source"
            bordered
            :loading="loading"
            :scroll="{ x: 'max-content' }"
        >
            <template #bodyCell="{ column, record, index }">
                <template v-if="column.dataIndex === 'stt'">
                    {{ index + 1 }}
                </template>
                <template v-if="column.dataIndex == 'atv_icon'">
                    <i :class="record.atv_icon"></i>
                </template>
                <template v-if="column.dataIndex == 'atv_value_hexa'">
                    {{ utils.formatNumber(record.atv_value_hexa) }}
                </template>
                <template v-if="column.dataIndex == 'atv_hot'">
                    <a-checkbox
                        v-model:checked="record.atv_hot"
                        @change="handleChangeActive(record, 'atv_hot')"
                        :checked-value="1"
                        :unchecked-value="0"
                    ></a-checkbox>
                </template>
                <template v-if="column.dataIndex == 'atv_active'">
                    <a-checkbox
                        v-model:checked="record.atv_active"
                        @change="handleChangeActive(record, 'atv_active')"
                        :checked-value="1"
                        :unchecked-value="0"
                    ></a-checkbox>
                </template>
                <template v-if="column.dataIndex === 'edit'">
                    <EditTwoTone @click="openFormAttributeValue(record)" />
                </template>
            </template>
            <template #footer>
                <a-row :gutter="15">
                    <a-col
                        >Tổng số bản ghi: <strong>{{ attribute_list.pagination.total }}</strong></a-col
                    >
                </a-row>
            </template>
        </a-table>
    </a-modal>

    <!-- Modal cập nhật thêm mới giá trị -->
    <a-modal
        v-model:open="attribute_value.open"
        width="700px"
        :body-style="{ minHeight: '30vh', overflowY: 'auto' }"
        :title="attribute_value.modalTitle"
        :footer="null"
        :destroyOnClose="true"
    >
        <a-form
            :model="attribute_value.formState"
            class="mt-3 pl-3"
            :label-col="{ span: 5 }"
            :wrapper-col="{ span: 17 }"
            @finish="handleSubmitFormValue"
        >
            <a-form-item class="mb-3" label="Thuộc tính" style="align-items: baseline">
                {{ attribute_value.formState.atn_name }}
            </a-form-item>
            <a-form-item
                class="mb-3"
                label="Tên giá trị"
                name="atv_name"
                :rules="[{ required: true, message: 'Vui lòng nhập tên giá trị' }]"
            >
                <a-input placeholder="Tên giá trị" v-model:value="attribute_value.formState.atv_name"></a-input>
            </a-form-item>
            <a-form-item class="mb-3" label="Mã icon" name="atv_icon" extra="Bao gồm cả tiền tố 'fas far fab...'">
                <a-input placeholder="Mã icon" v-model:value="attribute_value.formState.atv_icon"></a-input>
            </a-form-item>
            <a-form-item class="mb-3" label="Thứ tự" name="atv_order" extra="Thứ tự hiển thị ở bên ngoài website">
                <a-input-number
                    placeholder="Thứ tự"
                    v-model:value="attribute_value.formState.atv_order"
                ></a-input-number>
            </a-form-item>
            <a-row class="mb-3">
                <a-col :span="5" :xs="0" :sm="5" :md="5" :lg="5" :xl="5" :xxl="5"></a-col>
                <a-button type="primary" html-type="submit" :loading="loading">{{
                    attribute_value.formState.atv_id ? 'Cập nhật' : 'Thêm mới'
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
        baseColumns: [
            {
                title: '#',
                dataIndex: 'stt',
                width: '30px',
                align: 'center'
            },
            {
                title: 'Tên thuộc tính',
                dataIndex: 'atn_name',
                sorter: (a, b) => a.atn_name.localeCompare(b.atn_name)
            },
            {
                title: 'Nhóm',
                dataIndex: 'atn_group_text',
            },
            {
                title: 'Kiểu',
                dataIndex: 'atn_type_text',
            },
            {
                title: 'Alias search',
                dataIndex: 'atn_alias_search',
            },
            {
                title: 'Text meta',
                dataIndex: 'atn_text_join_meta',
            },
            {
                title: 'Các giá trị',
                dataIndex: 'atn_list_value',
                align: 'center'
            },
            {
                title: 'Cột',
                dataIndex: 'atn_column',
                sorter: (a, b) => a.atn_column - b.atn_column
            },
            {
                title: 'Thứ tự',
                dataIndex: 'atn_order',
                sorter: (a, b) => a.atn_order - b.atn_order
            },
            {
                title: 'Mô tả',
                dataIndex: 'atn_note',
            }
        ],
        loading: false,
        data_source: [],
        formSearch: {
            atn_name: '',
            atn_group: null,
            atn_type: null,
            atn_alias_search: ''
        },
        permissions: {},
        open: false,
        formState: {
            atn_group: null,
            atn_type: null,
            atn_name: '',
            atn_alias_search: '',
            atn_order: 0,
            atn_note: '',
            atn_text_join_meta: ''
        },
        modalTitle: '',
        pagination: {},
        attribute_list: {
            open: false,
            columns: [
                {
                    title: '#',
                    dataIndex: 'stt',
                    width: '30px',
                    align: 'center'
                },
                {
                    title: 'Tên giá trị',
                    dataIndex: 'atv_name',
                    sorter: (a, b) => a.atv_name.localeCompare(b.atv_name)
                },
                {
                    title: 'Value',
                    dataIndex: 'atv_value_hexa',
                    sorter: (a, b) => a.atv_value_hexa - b.atv_value_hexa
                },
                {
                    title: 'Icon',
                    dataIndex: 'atv_icon',
                    align: 'center'
                },
                {
                    title: 'Thứ tự',
                    dataIndex: 'atv_order',
                    sorter: (a, b) => a.atv_order - b.atv_order
                }
            ],
            data_source: [],
            pagination: {},
            modalTitle: '',
            formSearch: {
                atv_name: ''
            },
            permissions: {},
            atn_id: 0
        },
        attribute_value: {
            formState: {
                atv_name: '',
                atv_order: 0,
                atv_icon: '',
                atn_name: '',
                atv_id: 0
            },
            modalTitle: '',
            open: false
        }
    }),
    computed: {
        columns() {
            // Conditionally include the "Sửa" column
            let baseColumns = [...this.baseColumns];
            if (this.permissions.hasEdit) {
                baseColumns.push(
                    {
                        title: 'Lọc',
                        dataIndex: 'atn_show_filter',
                        align: 'center',
                        width: '30px'
                    },
                    {
                        title: 'Meta',
                        dataIndex: 'atn_join_meta',
                        align: 'center',
                        width: '30px'
                    },
                    {
                        title: 'Canonical',
                        dataIndex: 'atn_canonical',
                        align: 'center',
                    },
                    {
                        title: 'Rq',
                        dataIndex: 'atn_require',
                        align: 'center',
                        width: '30px'
                    },
                    {
                        title: 'Hot',
                        dataIndex: 'atn_hot',
                        align: 'center',
                        width: '30px'
                    },
                    {
                        title: 'Act',
                        dataIndex: 'active',
                        width: '30px',
                        align: 'center'
                    },
                    {
                        title: 'Act SN',
                        dataIndex: 'atn_sennet_active',
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
            await this.getData(params);
        },
        async getData(params = {}) {
            this.loading = true;
            params = {
                ...this.formSearch,
                atn_group: this.formSearch.atn_group ? this.formSearch.atn_group : -9999,
                atn_type: this.formSearch.atn_type ? this.formSearch.atn_type : -9999,
                page: params.page ?? this.pagination.current ?? 1,
                json: 1
            };
            let res = await $.ajax({
                url: `attribute_list.php?${new URLSearchParams(params).toString()}`,
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
        async handleChangeActive(record, field) {
            const id = record.atn_id || record.atv_id;
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
        // Mở form edit
        openFormAttribute(record = null) {
            if (record) {
                this.formState = {
                    atn_id: record.atn_id,
                    atn_group: record.atn_group,
                    atn_type: record.atn_type,
                    atn_name: record.atn_name,
                    atn_alias_search: record.atn_alias_search,
                    atn_order: record.atn_order,
                    atn_note: record.atn_note,
                    atn_text_join_meta: record.atn_text_join_meta
                };
                this.modalTitle = 'Cập nhật thông tin thuộc tính: ' + record.atn_name;
            } else {
                this.formState = {
                    atn_group: null,
                    atn_type: null,
                    atn_name: '',
                    atn_alias_search: '',
                    atn_order: 0,
                    atn_note: '',
                    atn_text_join_meta: ''
                };
                this.modalTitle = 'Thêm mới thuộc tính của khách sạn';
            }
            this.open = true;
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
        // Mở form danh sách các attribute
        async openListAttribute(record) {
            this.attribute_list.modalTitle = 'Danh sách các giá trị của thuộc tính: ' + record.atn_name;
            this.loading = true;
            this.attribute_list.atn_id = record.atn_id;
            this.attribute_value.formState.atn_name = record.atn_name;
            let res = await $.ajax({
                url: `attribute_value_list.php?json=1&id=${record.atn_id}`,
                dataType: 'json'
            });
            if (this.lodash.toNumber(res.success) == 1) {
                this.attribute_list.open = true;
                this.attribute_list.data_source = res.data.rows;
                this.attribute_list.permissions = this.lodash.assign(
                    this.attribute_list.permissions,
                    res.data.permissions
                );
                this.attribute_list.pagination = this.lodash.assign(
                    this.attribute_list.pagination,
                    res.data.pagination
                );
                this.attribute_list.columns = [
                    {
                        title: '#',
                        dataIndex: 'stt',
                        width: '30px',
                        align: 'center'
                    },
                    {
                        title: 'Tên giá trị',
                        dataIndex: 'atv_name',
                        width: '200px',
                        sorter: (a, b) => a.atv_name.localeCompare(b.atv_name)
                    },
                    {
                        title: 'Value',
                        dataIndex: 'atv_value_hexa',
                        width: '80px',
                        sorter: (a, b) => a.atv_value_hexa - b.atv_value_hexa
                    },
                    {
                        title: 'Icon',
                        dataIndex: 'atv_icon',
                        width: '50px',
                        align: 'center'
                    },
                    {
                        title: 'Thứ tự',
                        dataIndex: 'atv_order',
                        width: '80px',
                        sorter: (a, b) => a.atv_order - b.atv_order
                    }
                ];
                if (this.attribute_list.permissions.hasEdit) {
                    this.attribute_list.columns.push(
                        {
                            title: 'Hot',
                            dataIndex: 'atv_hot',
                            width: '30px',
                            align: 'center'
                        },
                        {
                            title: 'Active',
                            dataIndex: 'atv_active',
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
            } else {
                for (let i in res.data) {
                    notification.error({
                        message: res.data[i]
                    });
                }
            }
            this.loading = false;
        },
        async getDataValue(params = {}) {
            this.loading = true;
            params = {
                ...this.attribute_list.formSearch,
                id: this.attribute_list.atn_id,
                json: 1
            };
            let res = await $.ajax({
                url: `attribute_value_list.php?${new URLSearchParams(params).toString()}`,
                dataType: 'json'
            });

            if (this.lodash.toNumber(res.success) == 1) {
                this.attribute_list.data_source = res.data.rows;
                this.attribute_list.pagination = this.lodash.assign(
                    this.attribute_list.pagination,
                    res.data.pagination
                );
            } else {
                for (let i in res.data) {
                    notification.error({
                        message: res.data[i]
                    });
                }
            }
            this.loading = false;
        },
        openFormAttributeValue(record = null) {
            if (record) {
                this.attribute_value.modalTitle =
                    'Cập nhật thông tin giá trị của thuộc tính: ' + this.attribute_value.formState.atn_name;
                this.attribute_value.formState = {
                    ...this.attribute_value.formState,
                    atv_id: record.atv_id,
                    atv_name: record.atv_name,
                    atv_order: record.atv_order,
                    atv_icon: record.atv_icon
                };
            } else {
                this.attribute_value.modalTitle =
                    'Thêm mới giá trị của thuộc tính: ' + this.attribute_value.formState.atn_name;
                this.attribute_value.formState = {
                    ...this.attribute_value.formState,
                    atv_name: '',
                    atv_order: 0,
                    atv_icon: '',
                    atv_id: 0
                };
            }
            this.attribute_value.open = true;
        },
        // Submit form attributes value
        async handleSubmitFormValue() {
            this.loading = true;
            let url = '';
            if (this.attribute_value.formState.atv_id > 0) {
                url = `attribute_value_edit.php?id=${this.attribute_value.formState.atv_id}`;
            } else {
                url = `attribute_value_create.php?id=${this.attribute_list.atn_id}`;
            }
            let res = await $.ajax({
                url: url,
                type: 'POST',
                data: this.attribute_value.formState,
                dataType: 'json'
            });
            if (this.lodash.toNumber(res.success) == 1) {
                notification.success({
                    placement: 'bottomRight',
                    message: 'Cập nhật thành công'
                });
                this.attribute_value.open = false;
                await this.getDataValue();
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
