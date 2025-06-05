<template>
    <a-layout>
        <template v-if="btnCollapse">
            <a-layout-sider
                v-if="openSidebar"
                width="280"
                style="background: #fff; border-right: 1px solid rgba(5, 5, 5, 0.06)"
                v-model:collapsed="collapsed"
                :trigger="null"
                collapsible
                :collapsed-width="0"
            >
                <div class="text-center" style="margin-top: 15px">
                    <a href="/">
                        <img :src="logo" style="max-width: 100%" alt="Logo SENNET" />
                    </a>
                </div>
                <a-menu v-model:openKeys="openKeys" v-model:selectedKeys="selectedKeys" :items="menus" mode="inline">
                </a-menu>
            </a-layout-sider>
        </template>
        <template v-else>
            <a-layout-sider
                v-if="openSidebar"
                width="280"
                style="background: #fff"
                v-model:collapsed="collapsed"
                :trigger="null"
                collapsible
            >
                <div class="text-center" style="margin-top: 15px">
                    <a href="/"><img :src="logo" style="max-width: 100%" alt="Logo SENNET" /></a>
                </div>
                <a-menu v-model:openKeys="openKeys" v-model:selectedKeys="selectedKeys" :items="menus" mode="inline">
                </a-menu>
            </a-layout-sider>
        </template>
        <a-layout>
            <a-layout-header style="background: #fff; padding: 0" v-if="openHeader">
                <template v-if="openSidebar">
                    <menu-unfold-outlined v-if="collapsed" class="trigger" @click="toggleSidebar" />
                    <menu-fold-outlined v-else class="trigger" @click="toggleSidebar" />
                </template>
                <a-page-header v-if="showPageHeader" title="Trang chủ" @back="handleRedirectHome" />

                <div class="float-right mr-4" style="cursor: pointer; display: flex; gap: 10px; align-items: center">
                    <!-- Notifications Dropdown -->
                    <a-dropdown v-if="openHeader">
                        <div class="ant-dropdown-link mr-3" @click.prevent style="height: 50px; margin-top: 5px">
                            <a-badge :dot="notification.total > 0">
                                <i class="fal fa-bell" style="font-size: 16px"></i>
                            </a-badge>
                        </div>
                        <template #overlay>
                            <a-menu>
                                <a-menu-item v-if="notification.data_user.show" key="ci">
                                    <a :href="notification.data_user.link">
                                        {{ notification.data_user.title }}
                                        <a-badge :count="notification.data_user.total"></a-badge>
                                    </a>
                                </a-menu-item>
                            </a-menu>
                        </template>
                    </a-dropdown>
                    <!-- User Dropdown -->
                    <a-dropdown v-if="user !== null">
                        <div class="ant-dropdown-link" @click.prevent style="height: 50px">
                            <a-avatar :src="user.avatar_admin" />
                            <span style="margin-left: 3px">{{ user.name }}</span>
                        </div>
                        <template #overlay>
                            <a-menu>
                                <a-menu-item key="profile">
                                    <a :href="'/account/profile.php'">
                                        <i class="fas fa-user-circle"></i> Trang cá nhân
                                    </a>
                                </a-menu-item>
                                <a-menu-item key="logout">
                                    <a href="/logout.php"> <i class="fas fa-sign-out-alt"></i> Thoát </a>
                                </a-menu-item>
                            </a-menu>
                        </template>
                    </a-dropdown>
                </div>
            </a-layout-header>
            <a-layout-content
                @click="handleClickOutside"
                :scrence="scrence"
                :style="{ padding: `${!openHeader ? '0' : '24px'}`, margin: `${!openHeader ? '0' : '24px 16px'}` }"
            >
                <h6 v-if="openHeader">{{ title }}</h6>
                <component :is="scrence"></component>
            </a-layout-content>
        </a-layout>
    </a-layout>
</template>

<script>
import {
    Layout,
    LayoutSider,
    LayoutHeader,
    LayoutContent,
    Avatar,
    Dropdown,
    Menu,
    MenuItem,
    Badge,
    PageHeader
} from '@lib/ant-design-vue';
import { MenuFoldOutlined, MenuUnfoldOutlined } from '@lib/@ant-design/icons-vue';
import { h } from '@lib/vue';

export default {
    components: {
        ABadge: Badge,
        ALayout: Layout,
        AAvatar: Avatar,
        ADropdown: Dropdown,
        AMenu: Menu,
        AMenuItem: MenuItem,
        ALayoutSider: LayoutSider,
        ALayoutContent: LayoutContent,
        ALayoutHeader: LayoutHeader,
        APageHeader: PageHeader,
        MenuFoldOutlined,
        MenuUnfoldOutlined
    },
    data() {
        return {
            collapsed: false,
            openKeys: [],
            selectedKeys: [],
            logo: '',
            hotel: '',
            menus: [],
            user: null,
            openSidebar: false,
            openHeader: false,
            notification: [],
            btnCollapse: false,
            showPageHeader: false
        };
    },
    props: {
        scrence: {
            type: String,
            required: true
        },
        title: {
            type: String
        }
    },
    created() {
        this.user = window.appData.user;
        this.openSidebar = window.appData.sidebar;
        if (this.openSidebar) {
            this.getSidebar();
        }
        this.openHeader = window.appData.header;
        // Check if mobile
        if (window.innerWidth <= 768) {
            this.collapsed = true;
            this.btnCollapse = true;
        }

        // Lắng nghe sự kiện resize
        window.addEventListener('resize', this.resizeWidth);
    },
    destroyed() {
        // Hủy bỏ sự kiện resize khi component bị hủy
        window.removeEventListener('resize', this.resizeWidth);
    },
    mounted() {
        if (window.appData.header) {
            this.$nextTick(() => {
                this.getHeader();
            });
        }
    },
    methods: {
        async getSidebar() {
            const sidebar_data = window.appData.sidebar_data;
            this.logo = sidebar_data.logo;
            this.menus = sidebar_data.menus;
            if (window.innerWidth <= 768) {
                this.openKeys = [];
            } else {
                this.openKeys = sidebar_data.menu_open_keys;
            }
            this.selectedKeys = sidebar_data.menu_selected_keys;
            this.menus = sidebar_data.menus;
            this.menus.forEach((menu) => {
                menu.icon = h('span', { class: 'anticon' }, [h('i', { class: menu.icon })]);

                // Kiểm tra nếu có children
                if (menu.children && menu.children.length) {
                    menu.children.forEach((child) => {
                        if (child.route) {
                            child.label = h(
                                'a',
                                {
                                    href: child.route
                                },
                                child.label
                            );
                        }

                        if (child.children && child.children.length) {
                            child.children.forEach((subChild) => {
                                if (subChild.route) {
                                    subChild.label = h(
                                        'a',
                                        {
                                            href: subChild.route
                                        },
                                        subChild.label
                                    );
                                }
                            });
                        }
                    });
                }
            });
        },
        async getHeader() {
            let res = await $.ajax({
                url: '/common/get_header.php'
            });
            if (res.success === 1) {
                this.notification = res.data;
            }
        },
        openRoute(row) {
            let route = row.item.originItemValue.route;
            if (route) {
                window.location.href = row.item.originItemValue.route;
            }
        },
        toggleSidebar() {
            this.collapsed = !this.collapsed;
        },
        handleClickOutside() {
            if (window.innerWidth <= 768) {
                this.collapsed = true;
                this.btnCollapse = true;
            }
        },
        resizeWidth() {
            if (window.innerWidth <= 768) {
                this.collapsed = true;
                this.btnCollapse = true;
            } else {
                this.btnCollapse = false;
            }
        },
        handleRedirectHome() {
            window.location.href = '/home';
        }
    }
};
</script>

<style scoped>
::v-deep span.anticon.ant-menu-item-icon {
    width: 18px;
}

.ant-layout-header .trigger {
    font-size: 18px;
    line-height: 64px;
    padding: 0 24px;
    cursor: pointer;
    transition: color 0.3s;
}

.ant-layout-header .trigger:hover {
    color: #1890ff;
}

.ant-layout-sider .logo {
    height: 32px;
    background: rgba(255, 255, 255, 0.3);
    margin: 16px;
}

.ant-layout-content {
    background: #fff;
    padding: 24px;
    min-height: 80vh !important;
}

/* Trên mobile thì mới áp dụng */
@media (max-width: 768px) {
    ::v-deep .ant-layout-sider {
        position: fixed !important;
        top: 0;
        bottom: 0;
        z-index: 9999999 !important;
        overflow-y: scroll;
    }
    ::v-deep .btn-collapse {
        position: absolute !important;
        top: 13px;
        right: 16px;
        font-size: 18px !important;
    }

    ::v-deep .ant-layout-content {
        background: #fff;
        padding: 10px !important;
        margin: 10px 10px !important;
        min-height: 80vh !important;
    }
}

::v-deep .ant-scroll-number-only-unit {
    font-size: 12px;
}

::v-deep .ant-badge-count {
    height: 100%;
    width: 100%;
    padding: 0px 8px;
}

::v-deep .ant-page-header-heading-left {
    align-items: baseline !important;
}
</style>
