<template>
    <a-form
        :model="formState"
        :label-col="{ span: 5 }"
        :wrapper-col="{ span: 10 }"
        class="mt-3 pl-3"
        @finish="onSubmit"
    >
        <a-form-item class="mb-3" name="cocr_ip" label="IP" :rules="[{ required: true, message: 'Bạn chưa ID' }]">
            <a-input v-model:value="formState.cocr_ip" placeholder="IP"></a-input>
        </a-form-item>
        <a-form-item
            class="mb-3"
            name="cocr_password_default"
            label="Password Default"
            :rules="[{ required: true, message: 'Bạn nhập password' }]"
        >
            <a-input v-model:value="formState.cocr_password_default" placeholder="IP"></a-input>
        </a-form-item>
        <a-row>
            <a-col :span="5" :xs="0" :sm="5" :md="5" :lg="5" :xl="5" :xxl="5"></a-col>
            <a-button type="primary" html-type="submit" class="custom-btn" :loading="loading">
                {{ formState.cocr_id > 0 ? 'Cập nhật' : 'Them moi' }}
            </a-button>
        </a-row>
    </a-form>
</template>
<script>
import { PlusOutlined, LoadingOutlined } from '@lib/@ant-design/icons-vue';
import { Upload, notification, Switch } from '@lib/ant-design-vue';
import { ref } from '@lib/vue';
import utils from '@root/utils';
export default {
    components: {
        AUpload: Upload,
        PlusOutlined,
        LoadingOutlined,
        ASwitch: Switch
    },
    data: () => ({
        permissions: {},
        sources: [],
        loading: false,
        formState: {
            cocr_ip: '',
            cocr_id: '',
            cocr_password_default: ''
        },
        formRef: null
    }),
    watch: {},
    computed: {},
    created() {
        this.formRef = ref();
        this.formState = window.appData.record_info;
    },
    methods: {
        // Tạo mới request
        async onSubmit() {
            this.loading = true;
            let res = await $.ajax({
                type: 'POST',
                url: `crm_config.php?id=${this.formState.cocr_id}`,
                data: this.formState,
                dataType: 'json',
                contentType: 'application/x-www-form-urlencoded; charset=UTF-8'
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
        }
    }
};
</script>
<style scoped></style>
