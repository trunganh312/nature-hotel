<template>
    <a-form-item class="mb-3" :label="label" :extra="extra" :rules="[{ required: required, message: 'Vui chọn ảnh' }]">
        <a-upload
            :file-list="localFileList"
            :name="name"
            list-type="picture-card"
            class="avatar-uploader"
            :show-upload-list="false"
            :action="action"
            :before-upload="beforeUpload"
            @change="handleChange"
        >
            <img v-if="urlImage" :src="urlImage" alt="image" class="uploaded-image" />
            <div v-else>
                <loading-outlined v-if="loading"></loading-outlined>
                <plus-outlined v-else></plus-outlined>
                <div class="ant-upload-text">{{ text }}</div>
            </div>
        </a-upload>
    </a-form-item>
</template>

<script>
import { PlusOutlined, LoadingOutlined } from '@lib/@ant-design/icons-vue';
import { Upload } from '@lib/ant-design-vue';
import { ref } from 'vue';

export default {
    components: {
        AUpload: Upload,
        PlusOutlined,
        LoadingOutlined
    },
    props: {
        text: {
            type: String,
            default: 'Chọn ảnh'
        },
        label: {
            type: String,
            required: true
        },
        name: {
            type: String,
            required: true
        },
        fileList: {
            type: Array,
            default: () => []
        },
        urlImage: {
            type: String,
            default: ''
        },
        action: {
            type: String,
            default: '/hrm/account/company'
        },
        extra: {
            type: String,
            default: ''
        },
        required: {
            type: Boolean,
            default: false
        }
    },
    setup(props, { emit }) {
        const localFileList = ref([...props.fileList]);

        const handleChange = (info) => {
            localFileList.value = info.fileList; // Cập nhật localFileList
            emit('handle-change', info, props.name);
        };

        const beforeUpload = (file) => {
            emit('before-upload', file);
        };

        return {
            localFileList,
            handleChange,
            beforeUpload
        };
    }
};
</script>

<style scoped>
.avatar-uploader > .ant-upload {
    width: 128px;
    height: 128px;
}

.avatar-uploader > .ant-upload img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.ant-upload-select-picture-card i {
    font-size: 32px;
    color: #999;
}

.ant-upload-select-picture-card .ant-upload-text {
    margin-top: 8px;
    color: #666;
}
</style>
