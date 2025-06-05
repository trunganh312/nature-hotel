<template>
  <a-form :model="formSearch" layout="inline" class="my-4" @finish="getData">
    <a-form-item name="app_app_id">
      <a-input v-model:value="formSearch.app_app_id" placeholder="App ID"></a-input>
    </a-form-item>
    <a-form-item>
      <a-button type="primary" html-type="submit">Tìm kiếm</a-button>
    </a-form-item>
    <a-form-item v-if="permissions.hasCreate">
      <a-button class="ant-btn-primary-custom" @click="openForm(null)">
        <template #icon><PlusCircleOutlined /></template>
        Thêm mới
      </a-button>
    </a-form-item>
  </a-form>

  <a-table
    :columns="columns"
    :data-source="data_source"
    :pagination="pagination"
    @change="handleTableChange"
    bordered
    :loading="loading"
    :scroll="{ x: 'max-content' }"
  >
    <template #bodyCell="{ column, record, index }">
      <template v-if="column.dataIndex === 'stt'">{{ index + 1 }}</template>
      <template v-if="column.dataIndex === 'app_active'">
        <a-checkbox
          v-model:checked="record.app_active"
          @change="handleChangeActive(record, 'app_active')"
          :checked-value="1"
          :unchecked-value="0"
        ></a-checkbox>
      </template>
      <template v-if="column.dataIndex === 'edit'">
        <EditTwoTone @click="openForm(record)" />
      </template>
    </template>
    <template #footer>
      <a-row>Tổng số: <strong>{{ pagination.total }}</strong></a-row>
    </template>
  </a-table>

  <a-modal
    v-model:open="open"
    width="700px"
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
      <a-form-item label="App ID" name="app_app_id" :rules="[{ required: true, message: 'Enter App ID' }]">
        <a-row :gutter="8">
          <a-col :span="20">
            <a-input v-model:value="formState.app_app_id" placeholder="App ID"></a-input>
          </a-col>
          <a-col :span="4">
            <a-button :loading="loadingAppId" @click="generateAppId">
              <template #icon><ReloadOutlined /></template>
            </a-button>
          </a-col>
        </a-row>
      </a-form-item>
      <a-form-item label="App Secret" name="app_app_secret">
        <a-row :gutter="8">
          <a-col :span="20">
            <a-input v-model:value="formState.app_app_secret" placeholder="App Secret"></a-input>
          </a-col>
          <a-col :span="4">
            <a-button :loading="loadingAppSecret" @click="generateAppSecret">
              <template #icon><ReloadOutlined /></template>
            </a-button>
          </a-col>
        </a-row>
      </a-form-item>
      <a-form-item label="Requests" name="app_request">
        <a-input-number v-model:value="formState.app_request" placeholder="Requests"></a-input-number>
      </a-form-item>
      <a-form-item label="Max Requests" name="app_request_max">
        <a-input-number v-model:value="formState.app_request_max" placeholder="Max Requests"></a-input-number>
      </a-form-item>
      <a-row class="mb-3">
        <a-col :span="5"></a-col>
        <a-button type="primary" html-type="submit" :loading="loading">
          {{ formState.app_id ? 'Cập nhật' : 'Thêm mới' }}
        </a-button>
      </a-row>
    </a-form>
  </a-modal>
</template>

<script>
import { ref } from '@lib/vue';
import { EditTwoTone, PlusCircleOutlined, ReloadOutlined } from '@lib/@ant-design/icons-vue';
import { notification, Checkbox } from '@lib/ant-design-vue';

export default {
  components: { EditTwoTone, PlusCircleOutlined, ReloadOutlined, ACheckbox: Checkbox },
  data: () => ({
    loading: false,
    loadingAppId: false,
    loadingAppSecret: false,
    data_source: [],
    formSearch: { app_app_id: '', app_active: null },
    permissions: {},
    open: false,
    modalTitle: '',
    pagination: { total: 0, current: 1, pageSize: 10 },
    formState: {
      app_id: 0,
      app_app_id: '',
      app_app_secret: '',
      app_request: 0,
      app_request_max: 0,
      app_token_expired: 0,
      app_active: 0
    },
    baseColumns: [
      { title: '#', dataIndex: 'stt', width: '30px', align: 'center' },
      { title: 'App ID', dataIndex: 'app_app_id', width: '150px', sorter: (a, b) => a.app_app_id.localeCompare(b.app_app_id) },
      { title: 'App Secret', dataIndex: 'app_app_secret', width: '150px' },
      { title: 'Requests', dataIndex: 'app_request', width: '100px', sorter: (a, b) => a.app_request - b.app_request },
      { title: 'Max Requests', dataIndex: 'app_request_max', width: '100px', sorter: (a, b) => a.app_request_max - b.app_request_max },
      { title: 'Active', dataIndex: 'app_active', width: '60px', align: 'center' }
    ]
  }),
  computed: {
    columns() {
      let cols = [...this.baseColumns];
      if (this.permissions.hasEdit) {
        cols.push({ title: 'Sửa', dataIndex: 'edit', width: '30px', align: 'center' });
      }
      return cols;
    }
  },
  created() {
    this.data_source = window.appData?.rows ?? [];
    this.permissions = window.appData?.permissions ?? {};
    this.pagination = window.appData?.pagination ?? { total: 0, current: 1, pageSize: 10 };
  },
  methods: {
    async generateAppId() {
      this.loadingAppId = true;
      try {
        const res = await $.ajax({
          url: 'application_generate.php?action=generate_app_id',
          dataType: 'json'
        });
        if (res.success == 1) {
          this.formState.app_app_id = res.data.app_app_id;
        } else {
          notification.error({ message: res.error || 'Tạo App ID thất bại' });
        }
      } catch (error) {
        notification.error({ message: 'Lỗi kết nối' });
      }
      this.loadingAppId = false;
    },
    async generateAppSecret() {
      this.loadingAppSecret = true;
      try {
        const res = await $.ajax({
          url: 'application_generate.php?action=generate_app_secret',
          dataType: 'json'
        });
        if (res.success == 1) {
          this.formState.app_app_secret = res.data.app_app_secret;
        } else {
          notification.error({ message: res.error || 'Tạo App Secret thất bại' });
        }
      } catch (error) {
        notification.error({ message: 'Lỗi kết nối' });
      }
      this.loadingAppSecret = false;
    },
    async getData(params = {}) {
      this.loading = true;
      params = { ...this.formSearch, json: 1, page: this.pagination.current ?? 1, ...params };
      try {
        const res = await $.ajax({
          url: `application.php?${new URLSearchParams(params).toString()}`,
          dataType: 'json'
        });
        if (res.success == 1) {
          this.data_source = res.data.rows ?? [];
          this.pagination = res.data.pagination;
        } else {
          notification.error({ message: res.error || 'Lỗi tải dữ liệu' });
        }
      } catch (error) {
        notification.error({ message: 'Lỗi kết nối' });
      }
      this.loading = false;
    },
    async handleTableChange(pag, filters, sorter) {
      const params = {
        ...this.formSearch,
        page: pag.current,
        sort: sorter.order == 'ascend' ? 'asc' : 'desc',
        sortField: sorter.field
      };
      await this.getData(params);
    },
    async handleChangeActive(record, field) {
      this.loading = true;
      try {
        const res = await $.ajax({
          url: `application.php?action=toggle_active&field=${field}&id=${record.app_id}`,
          dataType: 'json'
        });
        if (res.success == 1) {
          notification.success({ message: 'Cập nhật thành công' });
          record.app_active = record.app_active == 1 ? 0 : 1;
          await this.getData();
        } else {
          notification.error({ message: res.error || 'Cập nhật thất bại' });
        }
      } catch (error) {
        notification.error({ message: 'Lỗi kết nối' });
      }
      this.loading = false;
    },
    openForm(record = null) {
      if (record) {
        this.formState = { ...record };
        this.modalTitle = `Sửa token: ${record.app_app_id}`;
      } else {
        this.formState = { app_id: 0, app_app_id: '', app_app_secret: '', app_request: 0, app_request_max: 0, app_token_expired: 0, app_active: 0 };
        this.modalTitle = 'Thêm mới token';
      }
      this.open = true;
    },
    async handleSubmitForm() {
      this.loading = true;
      const url = this.formState.app_id ? `application_edit.php?id=${this.formState.app_id}` : 'application_create.php';
      try {
        const res = await $.ajax({
          url,
          type: 'POST',
          data: this.formState,
          dataType: 'json'
        });
        if (res.success == 1) {
          notification.success({ message: 'Lưu thành công' });
          this.open = false;
          await this.getData();
        } else {
          notification.error({ message: res.error || 'Lưu thất bại' });
        }
      } catch (error) {
        notification.error({ message: 'Lỗi kết nối' });
      }
      this.loading = false;
    }
  }
};
</script>