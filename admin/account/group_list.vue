<template>
    <a-form :model="formSearch" name="horizontal_login" class="mt-3" layout="inline" @finish="onSearch">
        <a-form-item class="mb-3" name="gro_name">
            <a-input v-model:value="formSearch.gro_name" placeholder="Tên nhóm quyền"></a-input>
        </a-form-item>
        <a-form-item class="mb-3">
            <a-button :disabled="formSearch.disabled" type="primary" html-type="submit">Tìm kiếm</a-button>
        </a-form-item>
        <a-form-item class="mb-3" v-if="permissions.create">
            <a-button class="ant-btn-primary-custom" @click="openFormCreate">
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
        :row-key="(row) => row.per_id"
        :pagination="pagination"
        :loading="loading"
        @change="handleTableChange"
        :scroll="{ x: 'max-content' }"
        bordered
    >
        <template #bodyCell="{ column, record, index }">
            <template v-if="column.dataIndex === 'stt'">
                {{ index + 1 }}
            </template>
            <template v-if="column.dataIndex == 'gro_no_level'">
                <a-checkbox v-model:checked="record.gro_no_level" @change="changeStatus(record, 'gro_no_level')" />
            </template>
            <template v-if="column.dataIndex == 'gro_active'">
                <a-checkbox v-model:checked="record.gro_active" @change="changeStatus(record, 'gro_active')" />
            </template>
            <template v-if="column.dataIndex == 'gro_note'">
                <a-typography-paragraph
                    v-if="record.gro_note?.length && record.gro_note.length >= 40"
                    style="font-size: 14px"
                    :ellipsis="{ rows: 2, expandable: true, symbol: 'Xem thêm' }"
                    :content="record.gro_note"
                />
            </template>

            <template v-if="column.dataIndex == 'permission'">
                <a-typography-link @click="handleShowModalUpdate(record)">Phân quyền</a-typography-link>
            </template>
            <template v-if="column.dataIndex === 'edit'">
                <EditTwoTone @click="openFormEdit(record)" />
            </template>
        </template>
    </a-table>

    <!-- Drawer thêm và cập nhật -->
    <a-drawer :title="formTitle" placement="right" :size="size" @close="closeForm" :open="formOpen">
        <template #footer>
            <a-button key="submit" type="primary" :loading="loading" @click="onSubmitForm" class="float-right"
                >Xác nhận</a-button
            >
            <a-button key="back" @click="closeForm()" :loading="loading" class="mr-3 float-right">Hủy bỏ</a-button>
        </template>

        <a-form :ref="formRef" :model="formState" :label-col="{ span: 7 }" :wrapper-col="{ span: 17 }">
            <a-form-item
                class="mb-3"
                label="Tên nhóm quyền"
                name="gro_name"
                :rules="[{ required: true, message: 'Bạn chưa nhập tên nhóm quyền' }]"
            >
                <a-input v-model:value="formState.gro_name"></a-input>
            </a-form-item>
            <a-form-item class="mb-3" label="Mô tả" name="gro_note">
                <a-textarea rows="4" allow-clear v-model:value="formState.gro_note"></a-textarea>
            </a-form-item>
        </a-form>
    </a-drawer>

    <a-drawer :title="formTitle" placement="right" :size="size" @close="closeForm" :open="modalOpen">
        <template #footer>
            <a-button key="submit" type="primary" :loading="loadingModal" @click="handleUpdate" class="float-right"
                >Xác nhận</a-button
            >
            <a-button key="back" @click="closeForm()" class="mr-3 float-right">Hủy bỏ</a-button>
        </template>
        <template v-for="(item, index) in data">
            <div style="margin-top: 10px">
                <a-typography-title :level="5"
                    >{{ index + 1 }}. {{ item.module_name }}

                    <a-checkbox
                        v-model:checked="item.checkAll"
                        :indeterminate="item.indeterminate"
                        @change="onCheckAllChange(item)"
                        class="ml-2"
                    >
                        Chọn tất cả
                    </a-checkbox>
                </a-typography-title>

                <a-checkbox-group
                    v-model:value="item.checkedList"
                    :options="item.permissions"
                    @change="onModuleCheckChange(item)"
                    class="checkbox-group"
                />
            </div>
        </template>
    </a-drawer>
</template>
<script>
import { Checkbox, notification, Modal, CheckboxGroup, Typography } from '@lib/ant-design-vue';
import { EditTwoTone, PlusCircleOutlined } from '@lib/@ant-design/icons-vue';
import { ref } from '@lib/vue';
export default {
    components: {
        ACheckbox: Checkbox,
        EditTwoTone,
        AModal: Modal,
        ACheckboxGroup: CheckboxGroup,
        ATypography: Typography,
        PlusCircleOutlined
    },
    data: () => ({
        groupIdTarget: '',
        modalOpen: false,
        baseColumns: [
            {
                title: '#',
                dataIndex: 'stt',
                align: 'center',
                width: '35px'
            },
            {
                title: 'Tên nhóm quyền',
                dataIndex: 'gro_name',
                width: '200px'
            },
            {
                title: 'Mô tả nhóm',
                dataIndex: 'gro_note',
                width: '500px'
            }
        ],
        loading: false,
        data_source: [],
        pagination: {
            total: 0,
            current: 0,
            pageSize: 10,
            pageSizeOptions: []
        },
        formSearch: {
            gro_name: '',
            disabled: false
        },
        others: [],
        formTitle: '',
        formOpen: false,
        formState: {
            gro_name: '',
            gro_note: ''
        },
        permissions: [],
        isEdit: false,
        perrmisstionState: [],
        data: [],
        loadingModal: false,
        size: 'large'
    }),
    watch: {},
    computed: {
        columns() {
            const baseColumns = [...this.baseColumns];
            if (this.permissions.permission) {
                baseColumns.push({
                    title: 'Phân quyền',
                    dataIndex: 'permission',
                    width: '150px',
                    align: 'center'
                });
            }
            if (this.permissions.edit) {
                baseColumns.push(
                    {
                        title: 'Không giới hạn quyền',
                        dataIndex: 'gro_no_level',
                        width: '200px',
                        align: 'center'
                    },
                    {
                        title: 'Act',
                        dataIndex: 'gro_active',
                        width: '70px',
                        align: 'center'
                    },
                    {
                        title: 'Sửa',
                        dataIndex: 'edit',
                        width: '70px',
                        align: 'center'
                    }
                );
            }
            return baseColumns;
        }
    },
    created() {
        this.data_source = window.appData.rows;
        this.pagination = this.lodash.assign(this.pagination, window.appData.pagination);
        this.others = this.lodash.assign(this.others, window.appData.others);
        this.permissions = this.lodash.assign(this.permissions, window.appData.permissions);
        this.formSearch = this.lodash.assign(this.formSearch, window.appData.params);
        this.permissionState = {};
        this.formRef = ref();
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
            const params = {
                fieldsort: sorter.field,
                sort: sorter.order == 'ascend' ? 'asc' : 'desc'
            };
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

            params.json = 1;
            params.page = page;
            params = {
                ...params,
                page: params.page ?? this.pagination.current ?? 1
            };
            let res = await $.ajax({
                url: `group_list.php?${new URLSearchParams(params).toString()}`,
                dataType: 'json'
            });

            if (this.lodash.toNumber(res.success) === 1) {
                this.data_source = res.data.rows;
                this.pagination = this.lodash.assign(this.pagination, res.data.pagination);
            }
            this.loading = false;
        },
        /**
         * Xử lý khi nhấn nút tìm kiếm
         */
        async onSearch() {
            await this.getData(1, { ...this.formSearch });
        },
        async changeStatus(record, field) {
            const params = {
                field,
                id: record.gro_id
            };
            let res = await $.ajax({
                url: `/common/active.php?${new URLSearchParams(params).toString()}`,
                dataType: 'json'
            });
            if (this.lodash.toNumber(res.success) === 1) {
                notification.success({
                    placement: 'bottomRight',
                    message: 'Cập nhật thành công'
                });
                await this.getData(null, {});
            } else {
                notification.error({
                    message: 'Thay đổi trạng thái thất bại'
                });
            }
        },
        // Mở form edit
        openFormEdit(record) {
            this.formTitle = `Sửa thông tin nhóm quyền: ${record.gro_name} `;
            this.formOpen = true;
            this.formState = { ...record };
            this.isEdit = true;
        },
        // Mở form create
        openFormCreate() {
            this.formTitle = `Thêm mới nhóm quyền`;
            this.formOpen = true;
            this.isEdit = false;
        },
        // Đóng form
        closeForm() {
            this.formTitle = '';
            this.formOpen = false;
            this.formState = {
                gro_name: '',
                gro_note: ''
            };
            this.modalOpen = false;
        },
        // Submit form để thêm mới hoặc sửa
        onSubmitForm() {
            let url = '';
            if (!this.isEdit) {
                url = `group_create.php`;
            } else {
                url = `group_edit.php?id=${this.formState.gro_id}`;
            }
            const formData = {
                gro_name: this.formState.gro_name,
                gro_note: this.formState.gro_note
            };
            this.loading = true;
            this.formRef.value
                .validate()
                .then(async () => {
                    let res = await $.ajax({
                        type: 'POST',
                        url,
                        data: formData,
                        dataType: 'json',
                        contentType: 'application/x-www-form-urlencoded; charset=UTF-8'
                    });
                    if (this.lodash.toNumber(res.success) === 1) {
                        notification.success({
                            placement: 'bottomRight',
                            message: 'Cập nhật thành công!'
                        });
                        this.closeForm();
                        this.getData(null, {});
                    } else {
                        for (let i in res.data) {
                            notification.error({
                                message: res.data[i]
                            });
                        }
                    }
                    this.loading = false;
                })
                .catch(() => {
                    this.loading = false;
                });
        },
        // Cập nhật quyền cho nhóm quyền
        async handleUpdate() {
            this.loadingModal = true;
            const selectedValues = this.getAllSelectedValues();
            const formData = {
                permission: selectedValues
            };
            let res = await $.ajax({
                type: 'POST',
                data: formData,
                dataType: 'json',
                contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                url: `set_permission.php?group=${this.groupIdTarget}`
            });
            if (this.lodash.toNumber(res.success) == 1) {
                notification.success({
                    placement: 'bottomRight',
                    message: 'Cập nhật quyền thành công'
                });
                this.modalOpen = false;
                await this.getData(1, {});
                this.loadingModal = false;
            }
            this.groupIdTarget = '';
        },
        // Xử lý sự kiện show modal cập nhật nhóm quyền
        async handleShowModalUpdate(record) {
            this.formTitle = `Phân quyền cho nhóm: ${record.gro_name}`;
            this.modalOpen = true;
            this.groupIdTarget = record.gro_id;
            const params = {
                json: 1,
                group: record.gro_id
            };
            let res = await $.ajax({
                url: `set_permission.php?${new URLSearchParams(params).toString()}`,
                dataType: 'json'
            });
            this.data = res.data.modules.map((item) => {
                const checkedList = item.permissions
                    .filter((permission) => permission.is_checked == true)
                    .map((permission) => Number(permission.per_id));
                const permission = item.permissions.map((i) => {
                    return {
                        value: Number(i.per_id),
                        label: i.per_name
                    };
                });
                return {
                    ...item,
                    permissions: permission,
                    checkedList: checkedList,
                    checkAll: checkedList.length === permission.length,
                    indeterminate: !!checkedList.length && checkedList.length < permission.length
                };
            });
        },
        // Xử lý sự kiện check all 1 module nhóm quyền
        onCheckAllChange(module) {
            module.checkedList = module.checkAll ? module.permissions.map((p) => p.value) : [];
            module.indeterminate = false;
        },
        // Xử lý check 1 quyền trong 1 nhóm quyền,
        // và kiểm tra xem nếu check hêt quyền trong nhóm quyền đó r thì tick check all
        onModuleCheckChange(module) {
            module.indeterminate = !!module.checkedList.length && module.checkedList.length < module.permissions.length;
            module.checkAll = module.checkedList.length === module.permissions.length;
        },
        // Lấy ra tất cả quyên được check vào trong 1 mảng
        getAllSelectedValues() {
            const selectedValues = [];
            this.data.forEach((module) => {
                selectedValues.push(...module.checkedList);
            });
            return selectedValues;
        }
    }
};
</script>
<style scoped>
.checkbox-group {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}
</style>
