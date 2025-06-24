<template>
    <a-form
        :model="formState"
        :label-col="{ span: 3 }"
        :wrapper-col="{ span: 21 }"
        class="mt-3 pl-3"
        @finish="onSubmit"
    >
        <a-form-item
            class="mb-3"
            name="doc_name"
            label="Tên danh mục"
            :rules="[{ required: true, message: 'Bạn chưa nhập tên danh mục' }]"
        >
            <a-input style="width: 100%" v-model:value="formState.doc_name" />
        </a-form-item>
        <a-form-item class="mb-3" name="doc_img" label="">
            <image-upload
                :urlImage="previewImage"
                @handle-change="handleChangeImage"
                @before-upload="beforeUploadImage"
                :fileList="fileList"
                :name="'doc_img'"
                :action="'/'"
                :label="'Ảnh bài viết'"
            ></image-upload>
        </a-form-item>
        <a-form-item class="mb-3" name="doc_slug" label="Slug">
            <a-input style="width: 100%" v-model:value="formState.doc_slug" disabled />
        </a-form-item>
        <a-form-item class="mb-3" name="doc_icon" label="Icon">
            <a-input style="width: 100%" v-model:value="formState.doc_icon" />
        </a-form-item>
        <a-form-item class="mb-3" name="doc_order" label="Thứ tự">
            <a-input-number style="width: 100%" v-model:value="formState.doc_order" />
        </a-form-item>
        <a-form-item class="mb-3" name="doc_parent_id" label="Danh mục cha">
            <select-custom
                style="width: 100%"
                show-search
                allow-clear
                :filter-option="utils.filterOption"
                :options="others.categories"
                placeholder="Chọn danh mục cha"
                v-model:value="formState.doc_parent_id"
            />
        </a-form-item>
        <a-form-item
            class="mb-3"
            name="doc_content"
            label="Nội dung"
            :rules="[{ required: true, message: 'Bạn chưa nhập nội dung' }]"
        >
            <ckeditor v-model="formState.doc_content" />
        </a-form-item>
        <a-row>
            <a-col :span="3" :xs="0" :sm="3" :md="3" :lg="3" :xl="3" :xxl="3"></a-col>
            <a-button type="primary" html-type="submit" class="custom-btn" :loading="loading">Cập nhật</a-button>
        </a-row>
    </a-form>
</template>
<script>
import { PlusOutlined, LoadingOutlined } from '@lib/@ant-design/icons-vue';
import { Upload, notification, Switch } from '@lib/ant-design-vue';
import { ref } from '@lib/vue';
import utils from '@root/utils';
import Ckeditor from '../components/ckeditor.vue';
import SelectCustom from '@admin/components/select-custom.vue';
import ImageUpload from '@admin/components/ImageUpload.vue';
export default {
    components: {
        AUpload: Upload,
        PlusOutlined,
        LoadingOutlined,
        ASwitch: Switch,
        Ckeditor,
        SelectCustom,
        ImageUpload,
    },
    data: () => ({
        loading: false,
        formState: {
            doc_name: '',
            doc_content: '',
            doc_parent_id: null,
            doc_slug: '',
            doc_id: '',
            doc_icon: '',
            doc_order: 0
        },
        formRef: null,
        others: {},
        id: 0,
        fileList: [],
        previewImage: null,
    }),
    watch: {
        'formState.doc_name': {
            handler(newValue) {
                this.formState.doc_slug = utils.toSlug(newValue);
            },
            immediate: true
        }
    },
    computed: {},
    created() {
        this.formRef = ref();
        this.others = window.appData.others;
        this.formState = {
            ...window.appData.row,
            doc_parent_id: window.appData.row.doc_parent_id ? window.appData.row.doc_parent_id : null
        };
        // Gán ảnh preview
        this.previewImage = this.others.doc_img_url || null;
    },
    methods: {
        async handleChangeImage(info, name) {
            if (info.file.status === 'uploading') {
                this.loading = true;
                return;
            }
            if (info.file.status === 'done') {
                this.getBase64(info.file.originFileObj, (base64Url) => {
                    if (name === 'doc_img') {
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
        // Tạo mới request
        async onSubmit() {
            this.loading = true;
            const formData = new FormData();
            
            Object.keys(this.formState).forEach(key => {
                formData.append(key, this.formState[key]);
            });
            if (this.fileList.length > 0) {
                this.fileList.forEach((file) => {
                    formData.append('doc_image', file.originFileObj);
                });
            } 
            let res = await $.ajax({
                type: 'POST',
                url: `document_edit.php?id=${this.formState.doc_id}`,
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json'
            });
            if (this.lodash.toNumber(res.success) === 1) {
                notification.success({
                    placement: 'bottomRight',
                    message: 'Cập nhật thành công!'
                });
                // Về trang danh sách
                window.location.href = `/general/document_list.php?doc_name=${this.formState.doc_name}&doc_parent_id=${this.formState.doc_parent_id}`;
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
<style scoped></style>
