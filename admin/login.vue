<template>
    <a-spin :spinning="loading" tip="Đang tải...">
        <a-row style="min-height: 100vh; justify-content: center" :style="style">
            <a-col
                :span="16"
                :xs="0"
                :sm="0"
                :md="0"
                :lg="12"
                :xl="16"
                :xxl="16"
                :style="{
                    backgroundImage: `url(${domain_user}/theme/img/bg-login.jpg)`
                }"
                class="login-custom"
            >
            </a-col>
            <a-col :span="8" :xs="20" :sm="20" :md="20" :lg="12" :xl="8" :xxl="8">
                <div class="title-login mt-5">
                    <span class="login100-form-title p-b-59"> SENNET </span>
                    <span class="login100-form-title p-b-59 bold-500">Đăng nhập tài khoản</span>
                </div>
                <a-form :model="formState" :wrapper-col="{ span: 19 }" @finish="onSubmit" @keydown.enter="onSubmit">
                    <a-form-item name="email" :rules="[{ required: true, message: 'Vui lòng nhập email!' }]" class="mt">
                        <a-input
                            placeholder="Email"
                            v-model:value="formState.email"
                            style="padding: 10px; border-radius: 14px"
                        >
                            <template #prefix>
                                <MailOutlined class="site-form-item-icon" style="font-size: 16px" />
                            </template>
                        </a-input>
                    </a-form-item>
                    <a-form-item
                        class="mb-1"
                        name="password"
                        :rules="[{ required: true, message: 'Vui lòng nhập mật khẩu!' }]"
                    >
                        <a-input-password
                            placeholder="Mật khẩu"
                            v-model:value="formState.password"
                            style="padding: 10px; border-radius: 14px"
                        >
                            <template #prefix>
                                <LockOutlined class="site-form-item-icon" style="font-size: 16px" />
                            </template>
                        </a-input-password>
                    </a-form-item>
                    <a-form-item class="mb-1">
                        <div style="display: flex; justify-content: center">
                            <a-button
                                class="signup-submit"
                                size="large"
                                style="width: 100%; font-weight: bold"
                                type="primary"
                                html-type="submit"
                                :loading="loading"
                            >
                                ĐĂNG NHẬP
                            </a-button>
                        </div>
                    </a-form-item>
                </a-form>
            </a-col>
        </a-row>
    </a-spin>
</template>

<script>
import { Spin, notification, Divider } from '@lib/ant-design-vue';
import { MailOutlined, LockOutlined } from '@lib/@ant-design/icons-vue';
export default {
    components: {
        ASpin: Spin,
        ADivider: Divider,
        MailOutlined,
        LockOutlined
    },
    data: () => ({
        loading: false,
        formState: {
            email: '',
            password: ''
        },
        forgotPassword: {
            open: false,
            modalTitle: 'Quên mật khẩu?',
            formState: {
                email: ''
            },
            loading: false
        },
        style: {},
        domain_user: ''
    }),
    created() {
        window.addEventListener('resize', this.resizeWidth);
        if (window.innerWidth <= 992) {
            this.style = {
                position: 'fixed',
                bottom: 0,
                top: 0,
                left: 0,
                right: 0,
                'background-image': `url('${domain_user}/theme/img/bg-login.jpg')`,
                'background-repeat': 'no-repeat',
                'background-position': 'center',
                'background-size': 'cover',
                'z-index': 1,
                'overflow-y': 'scroll'
            };
        } else {
            this.style = {};
        }
        this.domain_user = domain_user;
    },
    destroyed() {
        // Hủy bỏ sự kiện resize khi component bị hủy
        window.removeEventListener('resize', this.resizeWidth);
    },
    methods: {
        resizeWidth() {
            if (window.innerWidth <= 992) {
                this.style = {
                    position: 'fixed',
                    bottom: 0,
                    top: 0,
                    left: 0,
                    right: 0,
                    'background-image': `url('${domain_user}/theme/img/bg-login.jpg')`,
                    'background-repeat': 'no-repeat',
                    'background-position': 'center',
                    'background-size': 'cover',
                    'z-index': 1,
                    'overflow-y': 'scroll'
                };
            } else {
                this.style = {};
            }
        },
        async onSubmit(values) {
            this.loading = true;
            try {
                let res = await $.ajax({
                    url: 'login.php',
                    data: values,
                    type: 'POST'
                });
                if (Number(res.success) === 1) {
                    notification.success({
                        placement: 'bottomRight',
                        message: 'Đăng nhập thành công!'
                    });
                    // Chuyển vào trang home luôn
                    window.location.href = '/';
                } else {
                    for (let i in res.data) {
                        notification.error({
                            message: res.data[i]
                        });
                    }
                }
            } catch (error) {
                // Vì đăng nhập rơi vào case này mặc dù đã đăng nhập đúng
                // Do redirect_url.
                /**
                    http://sennet.local/login
                    Request Method:
                    POST
                    Status Code:
                    302 Found
                 */
                if (Number(error.status) === 200) {
                    notification.success({
                        placement: 'bottomRight',
                        message: 'Đăng nhập thành công!'
                    });
                    // Chuyển vào trang home luôn
                    window.location.href = '/';
                }
            }
            this.loading = false;
        }
    }
};
</script>
<style scoped>
.login-custom {
    background-repeat: no-repeat;
    background-position: center;
    background-size: cover;
    z-index: 1;
}

@media only screen and (max-width: 768px) {
    .login-custom {
        display: none;
    }
}

@media only screen and (max-width: 993px) {
    ::v-deep .ant-form {
        padding: 50px 40px;
        background-color: white;
        border-radius: 10px;
        margin-bottom: 20px;
    }
}

::v-deep .ant-row .ant-form-row {
    justify-content: center;
}

.signup-submit {
    padding: 15px;
    color: white;
    font-size: 16px;
    text-align: center;
    border: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    outline: none;
    width: 100%;
    border-radius: 14px;
    background-color: #00bfff;
    cursor: pointer;
    font-weight: 600;
    margin-top: 15px;
    margin-bottom: 15px;
}

.signup-submit:hover {
    background-color: #00bfff;
    opacity: 0.8;
}
</style>
