<template>
    <!-- Button thêm mới phòng ban, nếu có quyền thì mới show -->
    <template v-if="hasCreate">
        <a-button @click="openFormAdd" class="m-2 float-right ant-btn-primary-custom">
            <template #icon>
                <PlusCircleOutlined />
            </template>
            Thêm mới
        </a-button>
    </template>

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
        <template #bodyCell="{ column, record }">
            <template v-if="column.dataIndex === 'dep_avatar'">
                <a-image
                    width="100px"
                    :src="record.dep_avatar"
                    fallback="data:image/png;base64,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"
                ></a-image>
            </template>
            <template v-if="column.dataIndex === 'member'">
                <a-typography-link v-if="hasManageMember" @click="hanldeOpenMember(record)">{{
                    record.total_member
                }}</a-typography-link>
                <span v-else>{{ record.total_member }}</span>
            </template>
            <template v-if="column.dataIndex === 'edit'">
                <EditTwoTone @click="openFormEdit(record)" />
            </template>
            <template v-if="column.dataIndex === 'dep_active'">
                <a-checkbox v-model:checked="record.dep_active" @change="changeStatus(record)"></a-checkbox>
            </template>
        </template>
    </a-table>

    <!-- Drawer thêm mới phòng ban -->
    <a-drawer :title="formTitle" placement="right" :size="size" @close="closeForm" :open="formOpenAdd">
        <template #footer>
            <a-button key="submit" type="primary" :loading="loading" @click="onSubmitForm" class="float-right"
                >Xác nhận</a-button
            >
            <a-button key="back" @click="closeForm()" :loading="loading" class="mr-3 float-right">Hủy bỏ</a-button>
        </template>

        <a-form
            :ref="formRef"
            :model="formState"
            :label-col="{ span: 7 }"
            :wrapper-col="{ span: 17 }"
            @keydown.enter="handleEnterForm"
        >
            <a-form-item
                class="mb-3"
                label="Tên phòng ban"
                name="dep_name"
                :rules="[{ required: true, message: 'Bạn chưa nhập tên phòng ban' }]"
            >
                <a-input v-model:value="formState.dep_name" placeholder="Tên Phòng/Ban"></a-input>
            </a-form-item>
            <a-form-item class="mb-3" name="dep_parent_id" label="Phòng/Ban cấp trên">
                <select-custom
                    v-model:value="formState.dep_parent_id"
                    style="width: 100%"
                    placeholder="Chọn Phòng/Ban cấp trên"
                    :options="others.list_department"
                    show-search
                    allow-clear
                    :filter-option="utils.filterOption"
                    ref="parentSelect"
                />
            </a-form-item>
            <a-form-item
                class="mb-3"
                label="Người quản lý"
                name="dep_manager_name"
                :rules="[{ required: true, message: 'Bạn chưa nhập Người quản lý' }]"
            >
                <a-auto-complete
                    v-model:value="formState.dep_manager_name"
                    placeholder="Nhập để tìm người quản lý"
                    @search="onSearchUser"
                    :options="optionsUser"
                    @select="onSelect"
                    @keydown.enter.prevent
                >
                </a-auto-complete>
            </a-form-item>
            <a-form-item class="mb-3" label="Giới thiệu" name="dep_description">
                <a-textarea v-model:value="formState.dep_description" placeholder="Giới thiệu"></a-textarea>
            </a-form-item>
            <image-upload
                :urlImage="urlAvatar"
                @handleChange="handleChangeAvatar"
                @beforeUpload="beforeUploadAvatar"
                :fileList="fileListAvatar"
                :name="'avatar'"
                :label="'Avatar'"
                :action="'department_edit.php'"
            ></image-upload>
        </a-form>
    </a-drawer>

    <!-- Drawer cập nhật phòng ban -->
    <a-drawer :title="formTitle" placement="right" :size="size" @close="closeForm" :open="formOpenEdit">
        <template #footer>
            <a-button key="submit" type="primary" :loading="loading" @click="onSubmitFormEdit" class="float-right"
                >Xác nhận</a-button
            >
            <a-button key="back" @click="closeForm()" :loading="loading" class="mr-3 float-right">Hủy bỏ</a-button>
        </template>

        <a-form
            :ref="formRef"
            :model="formState"
            :label-col="{ span: 7 }"
            :wrapper-col="{ span: 17 }"
            @keydown.enter="handleEnterFormEdit"
        >
            <a-form-item
                class="mb-3"
                label="Tên phòng ban"
                name="dep_name"
                :rules="[{ required: true, message: 'Bạn chưa nhập tên phòng ban' }]"
            >
                <a-input v-model:value="formState.dep_name" placeholder="Tên Phòng/Ban"></a-input>
            </a-form-item>
            <a-form-item class="mb-3" name="dep_parent_id" label="Phòng/Ban cấp trên">
                <select-custom
                    v-model:value="formState.dep_parent_id"
                    style="width: 200px"
                    placeholder="Chọn Phòng/Ban cấp trên"
                    :options="others.list_department"
                    show-search
                    allow-clear
                    :filter-option="utils.filterOption"
                    ref="parentSelectEdit"
                />
            </a-form-item>
            <a-form-item
                class="mb-3"
                label="Người quản lý"
                name="dep_manager_name"
                :rules="[{ required: true, message: 'Bạn chưa nhập Người quản lý' }]"
            >
                <a-auto-complete
                    v-model:value="formState.dep_manager_name"
                    placeholder="Nhập để tìm người quản lý"
                    @search="onSearchUser"
                    :options="optionsUser"
                    @select="onSelect"
                    @keydown.enter.prevent
                >
                </a-auto-complete>
            </a-form-item>
            <a-form-item class="mb-3" label="Giới thiệu" name="dep_description">
                <a-textarea v-model:value="formState.dep_description" placeholder="Giới thiệu"></a-textarea>
            </a-form-item>
            <image-upload
                :urlImage="urlAvatar"
                @handleChange="handleChangeAvatar"
                @beforeUpload="beforeUploadAvatar"
                :fileList="fileListAvatar"
                :name="'avatar'"
                :label="'Avatar'"
                :action="'department_edit.php'"
            ></image-upload>
        </a-form>
    </a-drawer>

    <!-- Drawer danh sách thành viên-->
    <a-drawer placement="right" :size="size" @close="closeForm" :open="member.open" :title="member.modalTitle">
        <a-form
            :model="member.formSearch"
            name="horizontal_login"
            class="mt-3 pl-3"
            layout="inline"
            @finish="onSearchMember"
        >
            <a-form-item class="mb-3" name="adm_name">
                <a-input v-model:value="member.formSearch.adm_name" placeholder="Họ tên"></a-input>
            </a-form-item>
            <a-form-item class="mb-3" name="adm_email">
                <a-input v-model:value="member.formSearch.adm_email" placeholder="Email"></a-input>
            </a-form-item>
            <a-form-item class="mb-3" name="adm_phone">
                <a-input v-model:value="member.formSearch.adm_phone" placeholder="Điện thoại"></a-input>
            </a-form-item>
            <a-form-item class="mb-3">
                <a-button :disabled="member.formSearch.disabled" type="primary" html-type="submit">Tìm kiếm</a-button>
            </a-form-item>
            <a-form-item class="mb-3">
                <a-button type="primary" v-if="member.hasUpdate" @click="handleOpenAddMember">
                    <template #icon>
                        <PlusCircleOutlined />
                    </template>
                    Thêm mới nhân sự
                </a-button>
            </a-form-item>
        </a-form>

        <!-- Hiện thi bảng -->
        <a-table
            :columns="memberColumns"
            :data-source="member.data_source"
            :row-key="(row) => row.mel_id"
            :pagination="member.pagination"
            :loading="member.loading"
            @change="handleTableChangeMember"
            :scroll="{ x: 'max-content' }"
            bordered
        >
            <template #bodyCell="{ column, record }">
                <template v-if="column.dataIndex === 'picture'">
                    <a-image
                        width="100px"
                        :src="
                            record.adm_avatar ||
                            'data:image/png;base64,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'
                        "
                    ></a-image>
                </template>
                <template v-if="column.dataIndex === 'info'">
                    <a-tag
                        :bordered="false"
                        color="processing"
                        v-if="record.group"
                        v-for="item in record.group.split(',')"
                        >{{ item }}</a-tag
                    >
                    <a-typography-title :level="5">{{ record.adm_name }}</a-typography-title>
                    <a-typography-paragraph class="mb-1"
                        ><i class="fal fa-envelope"></i> {{ record.adm_email }}</a-typography-paragraph
                    >
                    <a-typography-paragraph class="mb-1"
                        ><i class="fal fa-phone-alt"></i> {{ record.adm_phone }}
                    </a-typography-paragraph>
                </template>
                <template v-if="column.dataIndex === 'department'">
                    <a-tag
                        :bordered="false"
                        color="processing"
                        v-if="record.department"
                        v-for="item in record.department.split(',')"
                    >
                        {{ item }}
                    </a-tag>
                </template>
                <template v-if="column.dataIndex === 'delete'">
                    <a-popconfirm
                        cancelText="Hủy bỏ"
                        okText="Xác nhận"
                        @confirm="handleDelete(record.adm_id)"
                        :title="`Bạn có chắc chắn muốn xóa nhân sự này khỏi Phòng/Ban ${member.department_name}?`"
                    >
                        <DeleteTwoTone />
                    </a-popconfirm>
                </template>
            </template>
        </a-table>
    </a-drawer>

    <!-- Modal thêm mới nhân sự vào phòng ban -->
    <a-modal
        :title="addMember.modalTitle"
        :footer="null"
        v-model:open="addMember.open"
        width="800px"
        :body-style="{ maxHeight: '60vh', overflowY: 'auto' }"
        :destroyOnClose="true"
    >
        <a-spin :spinning="addMember.loading">
            <a-form
                :ref="addMember.formRef"
                :model="addMember.formState"
                :label-col="{ span: 5 }"
                :wrapper-col="{ span: 17 }"
                @finish="onSubmitFormAddMemberToDepartment"
            >
                <a-form-item class="mb-3" label="Phòng/Ban">
                    {{ member.department_name }}
                </a-form-item>
                <a-form-item
                    class="mb-3"
                    label="Nhân viên"
                    name="deac_account_name"
                    :rules="[{ required: true, message: 'Bạn chưa nhập nhân viên' }]"
                >
                    <a-auto-complete
                        v-model:value="addMember.formState.deac_account_name"
                        placeholder="Nhập để tìm người nhân viên"
                        @search="onSearchUser"
                        :options="optionsUser"
                        @select="onSelect"
                        @keydown.enter.prevent
                    >
                    </a-auto-complete>
                </a-form-item>
                <a-row>
                    <a-col :span="5" :xs="0" :sm="5" :md="5" :lg="5" :xl="5" :xxl="5"></a-col>
                    <a-col :span="5">
                        <a-button type="primary" html-type="submit" :loading="addMember.loading">
                            <template #icon>
                                <PlusCircleOutlined />
                            </template>
                            Thêm mới
                        </a-button>
                    </a-col>
                </a-row>
            </a-form>
        </a-spin>
    </a-modal>
</template>
<script>
import {
    Image,
    Tag,
    notification,
    Checkbox,
    Mentions,
    Modal,
    Popconfirm,
    Spin,
    message,
    AutoComplete
} from '@lib/ant-design-vue';
import utils from '@root/utils';
import { ref } from '@lib/vue';
import { EditOutlined, DeleteTwoTone, EditTwoTone, PlusCircleOutlined } from '@lib/@ant-design/icons-vue';
import SelectCustom from '@admin/components/select-custom.vue';
import ImageUpload from '@admin/components/ImageUpload.vue';
export default {
    components: {
        EditOutlined,
        AAutoComplete: AutoComplete,
        ATag: Tag,
        AImage: Image,
        DeleteTwoTone,
        EditTwoTone,
        AModal: Modal,
        ACheckbox: Checkbox,
        AMentions: Mentions,
        APopconfirm: Popconfirm,
        ASpin: Spin,
        ImageUpload,
        SelectCustom,
        PlusCircleOutlined
    },
    data: () => ({
        DOMAIN_STATIC: '',
        urlAvatar: '',
        fileListAvatar: [],
        baseColumns: [
            {
                title: '#',
                dataIndex: 'stt',
                align: 'center',
                width: '30px'
            },
            {
                title: 'Avatar',
                dataIndex: 'dep_avatar',
                width: '90px'
            },
            {
                title: 'Tên Phòng/Ban',
                dataIndex: 'dep_name',
                width: '200px'
            },
            {
                title: 'Cấp trên',
                dataIndex: 'dep_parent_name',
                align: 'center',
                width: '200px'
            },
            {
                title: 'Người quản lý',
                dataIndex: 'manager',
                align: 'center',
                width: '300px'
            },
            {
                title: 'Giới thiệu',
                dataIndex: 'dep_description',
                width: '200px'
            },
            {
                title: 'Thành viên',
                dataIndex: 'member',
                align: 'center',
                width: '100px'
            }
        ],
        formState: {
            dep_name: '',
            dep_parent_id: null,
            dep_manager_id: '',
            dep_description: '',
            dep_manager_name: ''
        },
        formStateEdit: {
            adm_name: '',
            adm_email: '',
            group: [],
            department: ''
        },
        formItem: {},
        loading: false,
        data_source: [],
        pagination: {
            total: 0,
            current: 0,
            pageSize: 10,
            pageSizeOptions: []
        },
        formOpenAdd: false,
        formOpenEdit: false,
        formTitle: '',
        others: [],
        department_id: 0,
        hasEdit: false,
        hasManageMember: false,
        hasCreate: false,

        users: [],
        member: {
            open: false,
            modalTitle: '',
            formSearch: {
                adm_name: '',
                adm_email: '',
                adm_phone: '',
                disabled: false
            },
            data_source: [],
            pagination: {
                total: 0,
                current: 0,
                pageSize: 10,
                pageSizeOptions: []
            },
            baseColumns: [
                {
                    title: '#',
                    dataIndex: 'stt'
                },
                {
                    title: 'Avatar',
                    dataIndex: 'picture'
                },
                {
                    title: 'Thông tin',
                    dataIndex: 'info'
                },
                {
                    title: 'Ngày tham gia',
                    dataIndex: 'deac_time_create',
                    align: 'center'
                },
                {
                    title: 'Phòng/Ban',
                    dataIndex: 'department'
                },
                {
                    title: 'Ghi chú',
                    dataIndex: 'deac_note'
                }
            ],
            loading: false,
            permissions: {},
            hasUpdate: false,
            hasPermission: false,
            dep_id: '',
            department_name: ''
        },
        addMember: {
            open: false,
            modalTitle: '',
            formState: {
                deac_account_id: '',
                deac_account_name: '',
                dep_description: ''
            },
            loading: false,
            formRef: null
        },
        size: 'large'
    }),
    watch: {},
    computed: {
        columns() {
            // Conditionally include the "Sửa" column
            let baseColumns = [...this.baseColumns];
            if (this.hasEdit) {
                baseColumns = [
                    ...baseColumns,
                    {
                        title: 'Act',
                        dataIndex: 'dep_active',
                        sorter: (a, b) => (a.dep_active === b.dep_active ? 0 : a.dep_active ? -1 : 1),
                        align: 'center',
                        width: '35px'
                    },
                    {
                        title: 'Sửa',
                        dataIndex: 'edit',
                        align: 'center',
                        width: '35px'
                    }
                ];
            }

            return baseColumns;
        },
        optionsUser() {
            return this.users.map((user) => ({
                key: user.id,
                value: user.value,
                payload: user
            }));
        },
        memberColumns() {
            // Conditionally include the "Sửa" column
            let baseColumns = [...this.member.baseColumns];
            if (this.member.hasUpdate) {
                baseColumns = [
                    ...baseColumns,
                    {
                        title: 'Xóa',
                        dataIndex: 'delete',
                        align: 'center'
                    }
                ];
            }
            return baseColumns;
        }
    },
    created() {
        this.data_source = window.appData.rows;
        this.pagination = this.lodash.assign(this.pagination, window.appData.pagination);
        this.others = this.lodash.assign(this.others, window.appData.others);
        this.formSearch = this.lodash.assign(this.formSearch, window.appData.params);
        this.hasEdit = window.appData.hasEdit;
        this.hasManageMember = window.appData.hasManageMember;
        this.hasCreate = window.appData.hasCreate;
        this.DOMAIN_STATIC = window.appData.DOMAIN_STATIC;
        this.formRef = ref();
        if (window.innerWidth <= 768) {
            this.size = 'middle';
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
            await this.getData(pag.current);
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
                ...this.formSearch,
                page: params.page ?? this.pagination.current ?? 1
            };
            let res = await $.ajax({
                url: `department_list.php?${new URLSearchParams(params).toString()}`,
                dataType: 'json'
            });

            if (this.lodash.toNumber(res.success) === 1) {
                this.data_source = res.data.rows;
                this.pagination = this.lodash.assign(this.pagination, res.data.pagination);
            }
            this.loading = false;
        },
        // Submit form thêm mới phòng ban
        async onSubmitForm() {
            this.loading = true;
            this.formRef.value
                .validate()
                .then(async () => {
                    // Tạo đối tượng FormData
                    const formData = new FormData();

                    // Thêm file vào FormData
                    this.fileListAvatar.forEach((file) => {
                        formData.append('avatar', file.originFileObj);
                    });
                    // Thêm các trường form khác vào FormData
                    Object.keys(this.formState).forEach((key) => {
                        formData.append(key, this.formState[key]);
                    });

                    let res = await $.ajax({
                        type: 'POST',
                        url: 'department_create.php',
                        data: formData,
                        dataType: 'json',
                        processData: false,
                        contentType: false
                    });
                    if (this.lodash.toNumber(res.success) === 1) {
                        notification.success({
                            placement: 'bottomRight',
                            message: 'Cập nhật thành công!'
                        });
                        this.closeForm();
                        this.getData();
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
        // Sự kiện enter submit
        handleEnterForm(event) {
            const parentSelect = this.$refs.parentSelect;

            if (parentSelect) {
                const isFocused = parentSelect.$el.contains(document.activeElement);

                if (isFocused) {
                    if (parentSelect.open) {
                        event.preventDefault();
                    } else {
                        parentSelect.open = true;
                        event.preventDefault();
                    }
                    return;
                }
            }

            this.onSubmitForm();
        },
        // Submit form cập nhật phòng ban
        async onSubmitFormEdit() {
            const params = {
                id: this.formState.id
            };
            this.loading = true;
            this.formRef.value
                .validate()
                .then(async () => {
                    // Tạo đối tượng FormData
                    const formData = new FormData();

                    // Thêm file vào FormData
                    this.fileListAvatar.forEach((file) => {
                        formData.append('avatar', file.originFileObj);
                    });
                    // Thêm các trường form khác vào FormData
                    Object.keys(this.formState).forEach((key) => {
                        formData.append(key, this.formState[key]);
                    });

                    let res = await $.ajax({
                        type: 'POST',
                        url: `department_edit.php?${new URLSearchParams(params).toString()}`,
                        data: formData,
                        dataType: 'json',
                        processData: false,
                        contentType: false
                    });
                    if (this.lodash.toNumber(res.success) === 1) {
                        notification.success({
                            placement: 'bottomRight',
                            message: 'Cập nhật thành công!'
                        });
                        this.closeForm();
                        await this.getData(1, {});
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
        // Sự kiện enter submit form edit
        handleEnterFormEdit(event) {
            const parentSelect = this.$refs.parentSelectEdit;

            if (parentSelect) {
                const isFocused = parentSelect.$el.contains(document.activeElement);

                if (isFocused) {
                    if (parentSelect.open) {
                        event.preventDefault();
                    } else {
                        parentSelect.open = true;
                        event.preventDefault();
                    }
                    return;
                }
            }

            this.onSubmitFormEdit();
        },
        /**
         * Cập nhật lại thông tin mở form
         */
        async closeForm() {
            this.formOpenAdd = false;
            this.formOpenEdit = false;
            this.member.open = false;
            this.formState = {
                dep_name: '',
                dep_parent_id: null,
                dep_manager_id: '',
                dep_description: '',
                dep_manager_name: ''
            };
        },
        /**
         * Mở form cập nhật
         */
        openFormAdd() {
            this.formTitle = `Thêm mới Phòng/Ban`;
            this.formOpenAdd = true;
            this.formState = {
                dep_name: '',
                dep_parent_id: null,
                dep_manager_id: '',
                dep_description: '',
                dep_manager_name: ''
            };
            this.urlAvatar = '';
            this.fileListAvatar = [];
            this.users = [];
        },
        async openFormEdit(row) {
            let manager_id = '';
            if (row.manager) {
                const manager = await this.loadUser(row.manager.split('-')[1].trim());
                manager_id = manager[0].id;
            } else {
                manager_id = null;
            }

            this.formTitle = `Sửa thông tin Phòng/Ban ${row.dep_name}`;
            this.formOpenEdit = true;
            this.formState = {
                dep_name: row.dep_name,
                dep_parent_id: Number(row.parent_id) == 0 ? null : Number(row.parent_id),
                dep_manager_id: manager_id,
                dep_description: row.dep_description,
                dep_manager_name: row.manager,
                id: row.id
            };
            this.urlAvatar = row.dep_avatar ? row.dep_avatar : '';
            this.users = [];
        },
        // Tìm kiếm user khi nhập @
        onSearchUser(searchValue) {
            if (!searchValue) {
                this.users = [];
                return;
            }
            this.loadUser(searchValue);
        },
        // Tìm kiếm user
        async loadUser(value) {
            if (!value) {
                this.users = [];
                return;
            }
            let res = await $.ajax({
                url: `/common/search_auto.php?term=${value}&type=admin`,
                type: 'GET',
                dataType: 'json'
            });
            this.users = res.slice(0, 10);
            return res;
        },
        // Xử lý sự kiện khi chọn user thì đưa value id vào ô input hidden
        onSelect(i, item) {
            this.formState.dep_manager_id = item.key;
            this.addMember.formState.deac_account_id = item.key;
        },
        async changeStatus(record) {
            const params = {
                field: 'dep_active',
                id: record.id
            };
            let res = await $.ajax({
                url: `/common/active.php?${new URLSearchParams(params).toString()}`,
                dataType: 'json'
            });
            if (this.lodash.toNumber(res.success) === 1) {
                notification.success({
                    placement: 'bottomRight',
                    message: 'Thay đổi trạng thái thành công'
                });
                await this.getData(1, {});
            } else {
                notification.error({
                    message: 'Thay đổi trạng thái thất bại'
                });
            }
        },
        // Mở form danh sách nhân sự
        hanldeOpenMember(record) {
            this.member.modalTitle = `Danh sách nhân sự Phòng/Ban: ${record.dep_name}`;
            this.member.dep_id = record.id;
            this.fetchMembers();
        },
        // Lấy data danh sách nhân sự theo phòng ban
        async fetchMembers(page = 1, params = {}) {
            params.json = 1;
            params.page = page;
            params.id = this.member.dep_id;
            this.member.open = true;
            this.member.loading = true;
            let res = await $.ajax({
                url: `department_member.php?${new URLSearchParams(params).toString()}`,
                dataType: 'json'
            });
            if (this.lodash.toNumber(res.success) === 1) {
                this.member.data_source = res.data.rows;
                this.member.pagination = this.lodash.assign(this.pagination, res.data.pagination);
                this.member.others = this.lodash.assign(this.others, res.data.others);
                this.member.formSearch = this.lodash.assign(this.formSearch, res.data.params);
                this.member.hasUpdate = res.data.hasUpdate;
                this.member.hasPermission = res.data.hasPermission;
                this.member.department_name = res.data.department_name;
            } else {
                notification.error({
                    message: res.msg
                });
            }
            this.member.loading = false;
        },
        async handleTableChangeMember(pag, filters, sorter) {
            await this.getData(pag.current);
        },
        // Search member
        async onSearchMember() {
            let params = {
                ...this.member.formSearch
            };

            if (this.member.formSearch.disabled) return;

            this.member.formSearch.disabled = true;
            await this.fetchMembers(1, params);
            this.member.formSearch.disabled = false;
        },
        // Mở modal thêm mới nhân sự vào công ty
        handleOpenAddMember() {
            this.addMember.modalTitle = `Thêm mới nhân sự vào Phòng/Ban ${this.member.department_name}`;
            this.addMember.open = true;
            this.addMember.formRef = ref();
            this.addMember.formState = {
                deac_account_id: '',
                deac_account_name: '',
                dep_description: ''
            };
        },
        // Thêm 1 nhân sự vào 1 phòng ban
        async onSubmitFormAddMemberToDepartment() {
            this.addMember.loading = true;
            let res = await $.ajax({
                type: 'POST',
                url: `department_member_add.php?id=${this.member.dep_id}`,
                data: this.addMember.formState,
                dataType: 'json',
                contentType: 'application/x-www-form-urlencoded; charset=UTF-8'
            });
            if (this.lodash.toNumber(res.success) === 1) {
                notification.success({
                    placement: 'bottomRight',
                    message: 'Cập nhật thành công!'
                });
                this.fetchMembers();
                this.getData();
                this.addMember.open = false;
            } else {
                for (let i in res.data) {
                    notification.error({
                        message: res.data[i]
                    });
                }
            }
            this.addMember.loading = false;
        },
        // Xóa nhân sự khỏi phòng ban
        async handleDelete(id) {
            const formData = {
                department: Number(this.member.dep_id),
                member: id
            };
            let res = await $.ajax({
                type: 'POST',
                url: `department_member_remove.php`,
                data: formData,
                dataType: 'json',
                contentType: 'application/x-www-form-urlencoded; charset=UTF-8'
            });
            if (this.lodash.toNumber(res.status) === 1) {
                notification.success({
                    placement: 'bottomRight',
                    message: 'Cập nhật thành công!'
                });
                this.fetchMembers();
                this.getData();
            } else {
                notification.error({
                    message: res.error
                });
            }
            this.loading = false;
        },
        getBase64(img, callback) {
            const reader = new FileReader();
            reader.addEventListener('load', () => callback(reader.result));
            reader.readAsDataURL(img);
        },
        async handleChangeAvatar(info, name) {
            if (info.file.status === 'uploading') {
                this.loading = true;
                return;
            }
            if (info.file.status === 'done') {
                this.getBase64(info.file.originFileObj, async (base64Url) => {
                    if (name === 'avatar') {
                        this.urlAvatar = base64Url;
                        this.fileListAvatar = info.fileList;
                    }

                    this.loading = false;
                });
            }
            if (info.file.status === 'error') {
                this.loading = false;
            }
        },
        beforeUploadAvatar(file) {
            const isType = file.type === 'image/jpeg' || file.type === 'image/png' || file.type === 'image/webp';
            if (!isType) {
                message.error('Vui lòng truyền đúng loại ảnh');
            }

            return isType;
        }
    }
};
</script>
<style></style>
