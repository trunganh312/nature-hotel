<template>
  <div>
    <!-- Bộ lọc -->
    <a-row :gutter="16" class="my-3 ">
      <a-col :span="6">
        <a-select
          v-model:value="filterCityId"
          show-search
          placeholder="Chọn thành phố để lọc"
          :options="cityOptions"
          :filter-option="filterOption"
          style="width: 100%"
          @change="handleFilterChange"
          allow-clear
        />
      </a-col>
    </a-row>

    <!-- Bảng -->
    <a-table
      :columns="columns"
      :data-source="dataSource"
      :row-key="(row) => row.cit_id"
      :loading="loading"
      bordered
      :scroll="{ x: 'max-content' }"
      class="mt-3"
    >
      <!-- Cột Số thứ tự -->
      <template #bodyCell="{ column, record, index }">
        <template v-if="column.dataIndex === 'stt'">
          {{ index + 1 }}
        </template>
        <!-- Cột Ảnh -->
        <template v-if="column.dataIndex === 'image'">
<a-image
        width="100px"
        style="aspect-ratio: 16 / 9; object-fit: cover"
        :src="record.cit_image_url"
        fallback="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMIAAADDCAYAAADQvc6UAAABRWlDQ1BJQ0MgUHJvZmlsZQAAKJFjYGASSSwoyGFhYGDIzSspCnJ3UoiIjFJgf8LAwSDCIMogwMCcmFxc4BgQ4ANUwgCjUcG3awyMIPqyLsis7PPOq3QdDFcvjV3jOD1boQVTPQrgSkktTgbSf4A4LbmgqISBgTEFyFYuLykAsTuAbJEioKOA7DkgdjqEvQHEToKwj4DVhAQ5A9k3gGyB5IxEoBmML4BsnSQk8XQkNtReEOBxcfXxUQg1Mjc0dyHgXNJBSWpFCYh2zi+oLMpMzyhRcASGUqqCZ16yno6CkYGRAQMDKMwhqj/fAIcloxgHQqxAjIHBEugw5sUIsSQpBobtQPdLciLEVJYzMPBHMDBsayhILEqEO4DxG0txmrERhM29nYGBddr//5/DGRjYNRkY/l7////39v///y4Dmn+LgeHANwDrkl1AuO+pmgAAADhlWElmTU0AKgAAAAgAAYdpAAQAAAABAAAAGgAAAAAAAqACAAQAAAABAAAAwqADAAQAAAABAAAAwwAAAAD9b/HnAAAHlklEQVR4Ae3dP3PTWBSGcbGzM6GCKqlIBRV0dHRJFarQ0eUT8LH4BnRU0NHR0UEFVdIlFRV7TzRksomPY8uykTk/zewQfKw/9znv4yvJynLv4uLiV2dBoDiBf4qP3/ARuCRABEFAoBEgghggQAQZQKAnYEaQBAQaASKIAQJEkAEEegJmBElAoBEgghggQAQZQKAnYEaQBAQaASKIAQJEkAEEegJmBElAoBEgghggQAQZQKAnYEaQBAQaASKIAQJEkAEEegJmBElAoBEgghggQAQZQKAnYEaQBAQaASKIAQJEkAEEegJmBElAoBEgghggQAQZQKAnYEaQBAQaASKIAQJEkAEEegJmBElAoBEgghggQAQZQKAnYEaQBAQaASKIAQJEkAEEegJmBElAoBEgghggQAQZQKAnYEaQBAQaASKIAQJEkAEEegJmBElAoBEgghggQAQZQKAnYEaQBAQaASKIAQJEkAEEegJmBElAoBEgghggQAQZQKAnYEaQBAQaASKIAQJEkAEEegJmBElAoBEgghggQAQZQKAnYEaQBAQaASKIAQJEkAEEegJmBElAoBEgghggQAQZQKAnYEaQBAQaASKIAQJEkAEEegJmBElAoBEgghggQAQZQKAnYEaQBAQaASKIAQJEkAEEegJmBElAoBEgghgg0Aj8i0JO4OzsrPv69Wv+hi2qPHr0qNvf39+iI97soRIh4f3z58/u7du3SXX7Xt7Z2enevHmzfQe+oSN2apSAPj09TSrb+XKI/f379+08+A0cNRE2ANkupk+ACNPvkSPcAAEibACyXUyfABGm3yNHuAECRNgAZLuYPgEirKlHu7u7XdyytGwHAd8jjNyng4OD7vnz51dbPT8/7z58+NB9+/bt6jU/TI+AGWHEnrx48eJ/EsSmHzx40L18+fLyzxF3ZVMjEyDCiEDjMYZZS5wiPXnyZFbJaxMhQIQRGzHvWR7XCyOCXsOmiDAi1HmPMMQjDpbpEiDCiL358eNHurW/5SnWdIBbXiDCiA38/Pnzrce2YyZ4//59F3ePLNMl4PbpiL2J0L979+7yDtHDhw8vtzzvdGnEXdvUigSIsCLAWavHp/+qM0BcXMd/q25n1vF57TYBp0a3mUzilePj4+7k5KSLb6gt6ydAhPUzXnoPR0dHl79WGTNCfBnn1uvSCJdegQhLI1vvCk+fPu2ePXt2tZOYEV6/fn31dz+shwAR1sP1cqvLntbEN9MxA9xcYjsxS1jWR4AIa2Ibzx0tc44fYX/16lV6NDFLXH+YL32jwiACRBiEbf5KcXoTIsQSpzXx4N28Ja4BQoK7rgXiydbHjx/P25TaQAJEGAguWy0+2Q8PD6/Ki4R8EVl+bzBOnZY95fq9rj9zAkTI2SxdidBHqG9+skdw43borCXO/ZcJdraPWdv22uIEiLA4q7nvvCug8WTqzQveOH26fodo7g6uFe/a17W3+nFBAkRYENRdb1vkkz1CH9cPsVy/jrhr27PqMYvENYNlHAIesRiBYwRy0V+8iXP8+/fvX11Mr7L7ECueb/r48eMqm7FuI2BGWDEG8cm+7G3NEOfmdcTQw4h9/55lhm7DekRYKQPZF2ArbXTAyu4kDYB2YxUzwg0gi/41ztHnfQG26HbGel/crVrm7tNY+/1btkOEAZ2M05r4FB7r9GbAIdxaZYrHdOsgJ/wCEQY0J74TmOKnbxxT9n3FgGGWWsVdowHtjt9Nnvf7yQM2aZU/TIAIAxrw6dOnAWtZZcoEnBpNuTuObWMEiLAx1HY0ZQJEmHJ3HNvGCBBhY6jtaMoEiJB0Z29vL6ls58vxPcO8/zfrdo5qvKO+d3Fx8Wu8zf1dW4p/cPzLly/dtv9Ts/EbcvGAHhHyfBIhZ6NSiIBTo0LNNtScABFyNiqFCBChULMNNSdAhJyNSiECRCjUbEPNCRAhZ6NSiAARCjXbUHMCRMjZqBQiQIRCzTbUnAARcjYqhQgQoVCzDTUnQIScjUohAkQo1GxDzQkQIWejUogAEQo121BzAkTI2agUIkCEQs021JwAEXI2KoUIEKFQsw01J0CEnI1KIQJEKNRsQ80JECFno1KIABEKNdtQcwJEyNmoFCJAhELNNtScABFyNiqFCBChULMNNSdAhJyNSiECRCjUbEPNCRAhZ6NSiAARCjXbUHMCRMjZqBQiQIRCzTbUnAARcjYqhQgQoVCzDTUnQIScjUohAkQo1GxDzQkQIWejUogAEQo121BzAkTI2agUIkCEQs021JwAEXI2KoUIEKFQsw01J0CEnI1KIQJEKNRsQ80JECFno1KIABEKNdtQcwJEyNmoFCJAhELNNtScABFyNiqFCBChULMNNSdAhJyNSiECRCjUbEPNCRAhZ6NSiAARCjXbUHMCRMjZqBQiQIRCzTbUnAARcjYqhQgQoVCzDTUnQIScjUohAkQo1GxDzQkQIWejUogAEQo121BzAkTI2agUIkCEQs021JwAEXI2KoUIEKFQsw01J0CEnI1KIQJEKNRsQ80JECFno1KIABEKNdtQcwJEyNmoFCJAhELNNtScABFyNiqFCBChULMNNSdAhJyNSiEC/wGgKKC4YMA4TAAAAABJRU5ErkJggg=="
    ></a-image>
</template>
        <!-- Cột Active -->
        <template v-if="column.dataIndex === 'active'">
          <a-checkbox :checked="record.cit_active" />
        </template>
        <!-- Cột Hot -->
        <template v-if="column.dataIndex === 'hot'">
          <a-checkbox :checked="record.cit_hot" />
        </template>
        <!-- Cột Sửa -->
        <template v-if="column.dataIndex === 'action'">
          <edit-two-tone style="cursor: pointer; font-size: 16px;" />
        </template>
      </template>
    </a-table>
  </div>
</template>

<script>
import { notification } from '@lib/ant-design-vue';
import { EditTwoTone } from '@lib/@ant-design/icons-vue';
import { Image } from '@lib/ant-design-vue';

export default {
  components: {
    EditTwoTone,
    AImage: Image,
  },
  data() {
    return {
      columns: [
        { title: '#', dataIndex: 'stt', align: 'center', width: '50px' },
        { title: 'Ảnh', dataIndex: 'image', align: 'center', width: '100px' },
        { title: 'Tên', dataIndex: 'cit_name', width: '200px' },
        { title: 'Tên khác', dataIndex: 'cit_name_other', width: '200px' },
        { title: 'Active', dataIndex: 'active', align: 'center', width: '120px' },
        { title: 'Hot', dataIndex: 'hot', align: 'center', width: '100px' },
        { title: 'Sửa', dataIndex: 'action', align: 'center', width: '100px' },
      ],
      dataSource: [],
      loading: false,
      filterCityId: null,
      cityOptions: [],
    };
  },
  created() {
    this.fetchCityOptions();
    this.getData();
  },
  methods: {
    async fetchCityOptions() {
      try {
        const res = await $.ajax({
          url: 'city.php',
          type: 'POST',
          data: JSON.stringify({ action: 'getCities' }),
          contentType: 'application/json',
          dataType: 'json',
        });
        if (res.success === 1) {
          this.cityOptions = res.data.data; // Dữ liệu đã ở định dạng {value, label}
        } else {
          notification.error({
            message: 'Lỗi',
            description: 'Không tải được danh sách thành phố!',
          });
        }
      } catch (error) {
        notification.error({
          message: 'Lỗi',
          description: 'Không tải được danh sách thành phố: ' + error.message,
        });
      }
    },

    filterOption(input, option) {
      return option.label.toLowerCase().indexOf(input.toLowerCase()) >= 0;
    },

    handleFilterChange(value) {
      this.filterCityId = value === undefined ? null : value;
      this.getData();
    },

    async getData() {
      this.loading = true;
      try {
        const requestData = { action: 'getData' };
        if (this.filterCityId !== null && this.filterCityId !== undefined) {
          requestData.cit_id = this.filterCityId;
        }

        const res = await $.ajax({
          url: 'city.php',
          type: 'POST',
          data: JSON.stringify(requestData),
          contentType: 'application/json',
          dataType: 'json',
        });

        if (res.success === 1) {
          this.dataSource = res.data.data.rows || []; // Sửa để lấy rows trực tiếp
        } else {
          notification.error({
            message: 'Lỗi',
            description: 'Không tải được dữ liệu!',
          });
        }
      } catch (error) {
        notification.error({
          message: 'Lỗi',
          description: 'Có lỗi xảy ra khi tải dữ liệu: ' + error.message,
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