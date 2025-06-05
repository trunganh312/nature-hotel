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
export default {
    components: {
        AUpload: Upload,
        PlusOutlined,
        LoadingOutlined,
        ASwitch: Switch,
        Ckeditor,
        SelectCustom
    },
    data: () => ({
        loading: false,
        formState: {
            doc_name: '',
            doc_content: '',
            doc_parent_id: null,
            doc_slug: '',
            doc_icon: '',
            doc_order: 0
        },
        formRef: null,
        others: {},
        id: 0
    }),
    watch: {
        'formState.doc_name': {
            handler(newValue) {
                this.formState.doc_slug = utils.toSlug(newValue);
            },
            immediate: true // Cập nhật ngay khi component được khởi tạo
        }
    },
    computed: {},
    created() {
        this.formRef = ref();
        this.others = window.appData.others;
    },
    methods: {
        // Tạo mới request
        async onSubmit() {
            this.loading = true;
            let res = await $.ajax({
                type: 'POST',
                url: `document_create.php`,
                data: this.formState,
                dataType: 'json',
                contentType: 'application/x-www-form-urlencoded; charset=UTF-8'
            });
            if (this.lodash.toNumber(res.success) === 1) {
                notification.success({
                    placement: 'bottomRight',
                    message: 'Cập nhật thành công!'
                });
                window.location.reload();
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
