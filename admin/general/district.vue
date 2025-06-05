  <template>
      <div>
        <!-- Bộ lọc -->
        <a-row :gutter="16" class="mb-3">
          <a-col :span="6">
            <a-select
              v-model:value="filterCityId"
              show-search
              placeholder="Chọn thành phố để lọc"
              :options="cities"
              :filter-option="filterOption"
              style="width: 100%"
              @change="handleFilterChange"
              allow-clear
            />
          </a-col>
          <a-col :span="6">
            <a-input
              v-model:value="filterDistrictName"
              placeholder="Tìm kiếm quận/huyện"
              @change="handleFilterChange"
              allow-clear
            />
          </a-col>
        </a-row>

        <a-table
          :columns="columns"
          :data-source="dataSource"
          :row-key="(row) => row.dis_id"
          :loading="loading"
          :pagination="pagination"
          bordered
          :scroll="{ x: 'max-content' }"
          class="mt-3"
        >
          <!-- Cột Số thứ tự -->
          <template #bodyCell="{ column, record, index }">
            <template v-if="column.dataIndex === 'stt'">
              {{ index + 1 + (pagination.current - 1) * pagination.pageSize }}
            </template>
    
            <!-- Cột Ảnh -->
            <template v-if="column.dataIndex === 'image'">
              <a-image
                width="100px"
                style="aspect-ratio: 16 / 9; object-fit: cover"
                :src="record.dis_image_url"
                fallback=""
              ></a-image>
            </template>
    
            <!-- Cột Active -->
            <template v-if="column.dataIndex === 'active'">
              <a-checkbox
                :checked="record.dis_active"
                @change="toggleActive(record)"
              />
            </template>
    
            <!-- Cột Hot -->
            <template v-if="column.dataIndex === 'hot'">
              <a-checkbox
                :checked="record.dis_hot"
                @change="toggleHot(record)"
              />
            </template>
    
            <!-- Cột Sửa -->
            <template v-if="column.dataIndex === 'action'">
              <edit-two-tone @click="openEditModal(record)" style="cursor: pointer; font-size: 16px;" />
            </template>
          </template>
    
          <!-- Footer hiển thị tổng số bản ghi -->
          <template #footer>
            <a-row :gutter="15">
              <a-col>
                Tổng số bản ghi: <strong>{{ pagination.total }}</strong>
              </a-col>
            </a-row>
          </template>
        </a-table>
    
        <!-- Modal chỉnh sửa quận/huyện -->
        <a-modal
          v-model:open="editModalVisible"
          title="Chỉnh sửa quận/huyện"
          :footer="null"
          :destroyOnClose="true"
          width="700px"
        >
          <a-form
            :model="editForm"
            :label-col="{ span: 6 }"
            :wrapper-col="{ span: 16 }"
            @finish="handleEditSubmit"
            ref="formRef"
          >
            <a-form-item
              label="Tên quận/huyện"
              name="dis_name"
              :rules="[{ required: true, message: 'Vui lòng nhập tên quận/huyện!' }]"
            >
              <a-input v-model:value="editForm.dis_name" placeholder="Tên quận/huyện" />
            </a-form-item>
    
            <a-form-item
              label="Tên khác"
              name="dis_name_other"
            >
              <a-input v-model:value="editForm.dis_name_other" placeholder="Tên khác" />
            </a-form-item>
    
            <a-form-item
              label="Thành phố"
              name="dis_city"
              :rules="[{ required: true, message: 'Vui lòng chọn thành phố!' }]"
            >
              <a-select
                v-model:value="editForm.dis_city"
                placeholder="Chọn thành phố"
                :options="cities"
              />
            </a-form-item>
    
            <!-- Upload ảnh -->
            <image-upload
              :urlImage="previewImage"
              @handleChange="handleChangeImage"
              @beforeUpload="beforeUploadImage"
              :fileList="fileList"
              :name="'dis_image'"
              :action="'/'"
              :label="'Ảnh quận/huyện'"
            ></image-upload>
    
            <a-form-item :wrapper-col="{ offset: 6, span: 16 }">
              <a-button type="primary" html-type="submit" :loading="loading">
                Cập nhật
              </a-button>
            </a-form-item>
          </a-form>
        </a-modal>
      </div>
  </template>

  <script>
  import { ref } from '@lib/vue';
  import { EditTwoTone, PlusOutlined, LoadingOutlined } from '@lib/@ant-design/icons-vue';
  import { Checkbox as ACheckbox, notification, message, Upload, Image } from '@lib/ant-design-vue';
  import ImageUpload from '@admin/components/ImageUpload.vue';

  export default {
    components: {
      EditTwoTone,
      AImage: Image,
      ACheckbox,
      AUpload: Upload,
      PlusOutlined,
      LoadingOutlined,
      ImageUpload,
    },
    setup() {
      const formRef = ref();
      return { formRef };
    },
    data() {
      return {
        columns: [
          {
            title: '#',
            dataIndex: 'stt',
            align: 'center',
            width: '50px',
          },
          {
            title: 'Ảnh',
            dataIndex: 'image',
            align: 'center',
            width: '100px',
          },
          {
            title: 'Tên',
            dataIndex: 'dis_name',
            width: '200px',
          },
          {
            title: 'Tên khác',
            dataIndex: 'dis_name_other',
            width: '200px',
          },
          {
            title: 'Thành phố',
            dataIndex: 'cit_name',
            width: '200px',
          },
          {
            title: 'Active',
            dataIndex: 'active',
            align: 'center',
            width: '120px',
          },
          {
            title: 'Hot',
            dataIndex: 'hot',
            align: 'center',
            width: '100px',
          },
          {
            title: 'Sửa',
            dataIndex: 'action',
            align: 'center',
            width: '100px',
          },
        ],
        dataSource: [],
        loading: false,
        pagination: {
          current: 1,
          pageSize: 10,
          total: 0,
          showSizeChanger: true,
          pageSizeOptions: ['10', '20', '50', '100'],
          onChange: (page) => this.getData(page),
          onShowSizeChange: (current, size) => {
            this.pagination.pageSize = size;
            this.pagination.current = 1; // Reset về trang 1 khi thay đổi pageSize
            this.getData(1);
          },
        },
        editModalVisible: false,
        editForm: {
          dis_id: null,
          dis_name: '',
          dis_name_other: '',
          dis_image: '',
          dis_city: null,
        },
        fileList: [],
        previewImage: null,
        cities: [],
        filterCityId: null,       // Bộ lọc thành phố
        filterDistrictName: null, // Bộ lọc tên quận/huyện
      };
    },
    created() {
      this.getData();
    },
    methods: {
      async getData(page = 1) {
        this.loading = true;
        try {
          const requestData = {
            action: 'getData',
            page: page,
            pageSize: this.pagination.pageSize,
          };
          if (this.filterCityId !== null && this.filterCityId !== undefined) {
            requestData.dis_city = this.filterCityId;
          }
          if (this.filterDistrictName !== null && this.filterDistrictName !== '') {
            requestData.dis_name = this.filterDistrictName;
          }

          const res = await $.ajax({
            url: 'district.php',
            type: 'POST',
            data: JSON.stringify(requestData),
            contentType: 'application/json',
            dataType: 'json',
          });

          if (res.success === 1) {
            this.dataSource = res.data.data.rows || [];
            this.pagination.total = res.data.data.pagination.total || 0;
            this.pagination.current = res.data.data.pagination.current || page;
            this.pagination.pageSize = res.data.data.pagination.pageSize || this.pagination.pageSize;
            this.cities = res.data.data.cities || [];
          } else {
            notification.error({
              message: 'Lỗi khi tải dữ liệu',
              description: res.message || 'Có lỗi xảy ra, vui lòng thử lại!',
            });
          }
        } catch (error) {
          notification.error({
            message: 'Lỗi hệ thống',
            description: error.message || 'Có lỗi xảy ra, vui lòng thử lại!',
          });
        } finally {
          this.loading = false;
        }
      },

      filterOption(input, option) {
        return option.label.toLowerCase().indexOf(input.toLowerCase()) >= 0;
      },

      handleFilterChange() {
        this.pagination.current = 1; // Reset về trang 1 khi lọc
        this.getData(1);
      },

      openEditModal(record) {
        this.editForm = { ...record, dis_city: record.dis_city };
        this.fileList = [];
        this.previewImage = record.dis_image_url || null;
        this.editModalVisible = true;
      },

      async handleChangeImage(info, name) {
        if (info.file.status === 'uploading') {
          this.loading = true;
          return;
        }
        if (info.file.status === 'done') {
          this.getBase64(info.file.originFileObj, (base64Url) => {
            if (name === 'dis_image') {
              this.previewImage = base64Url;
              this.fileList = info.fileList;
            }
            this.loading = false;
          });
        }
        if (info.file.status === 'error') {
          this.loading = false;
        }
      },

      beforeUploadImage(file) {
        const isType = file.type === 'image/jpeg' || file.type === 'image/png' || file.type === 'image/webp';
        if (!isType) {
          message.error('Vui lòng chọn file ảnh (JPEG, PNG, WEBP)!');
        }
        return isType;
      },

      getBase64(img, callback) {
        const reader = new FileReader();
        reader.addEventListener('load', () => callback(reader.result));
        reader.readAsDataURL(img);
      },

      async handleEditSubmit() {
        this.loading = true;
        try {
          const formData = new FormData();
          formData.append('dis_id', this.editForm.dis_id);
          formData.append('dis_name', this.editForm.dis_name);
          formData.append('dis_name_other', this.editForm.dis_name_other || '');
          formData.append('dis_city', this.editForm.dis_city);
          this.fileList.forEach((file) => {
            formData.append('dis_image', file.originFileObj);
          });
          if (this.fileList.length === 0) {
            formData.append('dis_image', this.editForm.dis_image || '');
          }

          const res = await $.ajax({
            type: 'POST',
            url: `edit_district.php`,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
          });

          if (res.success === 1) {
            message.success('Cập nhật quận/huyện thành công!');
            this.editModalVisible = false;
            await this.getData(this.pagination.current);
          } else {
            notification.error({
              message: 'Lỗi khi cập nhật',
              description: res.message || 'Có lỗi xảy ra, vui lòng thử lại!',
            });
          }
        } catch (error) {
          notification.error({
            message: 'Lỗi hệ thống',
            description: error.message || 'Có lỗi xảy ra, vui lòng thử lại!',
          });
        } finally {
          this.loading = false;
        }
      },

      async toggleActive(record) {
        this.loading = true;
        try {
          const res = await $.ajax({
            type: 'GET',
            url: `../common/active.php`,
            data: {
              field: 'dis_active',
              id: record.dis_id,
            },
            dataType: 'json',
          });

          if (res.success === 1) {
            message.success('Cập nhật trạng thái Active thành công!');
            this.getData(this.pagination.current);
          } else {
            notification.error({
              message: 'Lỗi khi cập nhật trạng thái',
              description: res.message || 'Có lỗi xảy ra, vui lòng thử lại!',
            });
          }
        } catch (error) {
          notification.error({
            message: 'Lỗi hệ thống',
            description: error.message || 'Có lỗi xảy ra, vui lòng thử lại!',
          });
        } finally {
          this.loading = false;
        }
      },

      async toggleHot(record) {
        this.loading = true;
        try {
          const res = await $.ajax({
            type: 'GET',
            url: `../common/active.php`,
            data: {
              field: 'dis_hot',
              id: record.dis_id,
              type: 'districts',
            },
            dataType: 'json',
          });

          if (res.success === 1) {
            message.success('Cập nhật trạng thái Hot thành công!');
            this.getData(this.pagination.current);
          } else {
            notification.error({
              message: 'Lỗi khi cập nhật trạng thái',
              description: res.message || 'Có lỗi xảy ra, vui lòng thử lại!',
            });
          }
        } catch (error) {
          notification.error({
            message: 'Lỗi hệ thống',
            description: error.message || 'Có lỗi xảy ra, vui lòng thử lại!',
          });
        } finally {
          this.loading = false;
        }
      },
    },
  };
  </script>

  <style scoped>
  .mt-3 {
    margin-top: 16px;
  }
  .mb-3 {
    margin-bottom: 16px;
  }
  </style>