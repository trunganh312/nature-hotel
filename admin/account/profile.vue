<template>
    <a-form
        :model="formState"
        :label-col="{ span: 5 }"
        :wrapper-col="{ span: 10 }"
        class="mt-3 pl-3"
        @finish="onSubmit"
    >
        <a-form-item class="mb-3" label="Họ tên">{{ formState.adm_name }}</a-form-item>
        <a-form-item class="mb-3" label="Email">{{ formState.adm_email }}</a-form-item>
        <a-form-item
            class="mb-3"
            name="adm_sex"
            label="Giới tính"
            :rules="[{ required: true, message: 'Bạn chưa chọn giới tính' }]"
        >
            <select-custom
                v-model:value="formState.adm_sex"
                placeholder="Giới tính"
                :options="others.adm_sex"
                :filter-option="utils.filterOption"
            />
        </a-form-item>
        <a-form-item class="mb-3" name="adm_address" label="Địa chỉ">
            <a-input v-model:value="formState.adm_address" placeholder="Địa chỉ"></a-input>
        </a-form-item>
        <a-form-item class="mb-3" name="adm_phone" label="Điện thoại">
            <a-input v-model:value="formState.adm_phone" placeholder="Điện thoại"></a-input>
        </a-form-item>
        <a-form-item class="mb-3" name="current_pwd" label="Mật khẩu hiện tại">
            <a-input-password v-model:value="formState.current_pwd" placeholder="Mật khẩu hiện tại"></a-input-password>
        </a-form-item>
        <a-form-item class="mb-3" name="new_password" label="Mật khẩu mới">
            <a-input-password v-model:value="formState.new_password" placeholder="Mật khẩu mới"></a-input-password>
        </a-form-item>
        <a-form-item class="mb-3" name="new_password_confirm" label="Nhập lại mật khẩu mới">
            <a-input-password
                v-model:value="formState.new_password_confirm"
                placeholder="Nhập lại mật khẩu mới"
            ></a-input-password>
        </a-form-item>
        <image-upload
            :urlImage="urlAvatar"
            @handleChange="handleChangeAvatar"
            @beforeUpload="beforeUploadAvatar"
            :fileList="fileListAvatar"
            :name="'adm_avatar'"
            :action="'/'"
            :label="'Avatar'"
        ></image-upload>
        <a-row>
            <a-col :span="5" :xs="0" :sm="5" :md="5" :lg="5" :xl="5" :xxl="5"></a-col>
            <a-button type="primary" html-type="submit" class="custom-btn" :loading="loading">Cập nhật</a-button>
        </a-row>
    </a-form>
</template>
<script>
import { PlusOutlined, LoadingOutlined } from '@lib/@ant-design/icons-vue';
import { Upload, notification, message } from '@lib/ant-design-vue';
import { ref } from '@lib/vue';
import utils from '@root/utils';
import ImageUpload from '@admin/components/ImageUpload.vue';
import SelectCustom from '@admin/components/select-custom.vue';
export default {
    components: {
        AUpload: Upload,
        PlusOutlined,
        LoadingOutlined,
        ImageUpload,
        SelectCustom
    },
    data: () => ({
        urlAvatar: '',
        fileListAvatar: [],
        loading: false,
        formState: {
            adm_name: '',
            adm_email: '',
            adm_sex: null,
            adm_address: '',
            adm_phone: '',
            new_password: '',
            new_password_confirm: ''
        },
        others: {},
        formRef: null,
        loading: false
    }),
    created() {
        this.formRef = ref();
        this.formState = {
            adm_name: window.appData.record_info.adm_name,
            adm_email: window.appData.record_info.adm_email,
            adm_sex: window.appData.record_info.adm_sex,
            adm_address: window.appData.record_info.adm_address,
            adm_phone: window.appData.record_info.adm_phone
        };
        this.urlAvatar = window.appData.record_info.adm_avatar;
        this.others = window.appData.others;
    },
    methods: {
        // Cập nhật thông tin công ty
        async onSubmit() {
            // Tạo đối tượng FormData
            const formData = new FormData();

            // Thêm file vào FormData
            this.fileListAvatar.forEach((file) => {
                formData.append('adm_avatar', file.originFileObj);
            });
            // Thêm các trường form khác vào FormData
            Object.keys(this.formState).forEach((key) => {
                formData.append(key, this.formState[key]);
            });

            this.loading = true;
            let res = await $.ajax({
                type: 'POST',
                url: `profile.php?id=${this.formState.adm_id}`,
                data: formData,
                dataType: 'json',
                processData: false, // Không chuyển đổi dữ liệu thành chuỗi
                contentType: false // Để jQuery tự thiết lập Content-Type
            });
            if (this.lodash.toNumber(res.success) === 1) {
                notification.success({
                    placement: 'bottomRight',
                    message: 'Cập nhật thành công!'
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
                    if (name === 'adm_avatar') {
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
