<template>
    <a-modal
        v-model:open="viewLogData.openModal"
        width="900px"
        :body-style="{ maxHeight: '60vh', overflowY: 'auto', overflowX: 'hidden' }"
        :title="viewLogData.modalTitle"
        :footer="null"
        :destroyOnClose="true"
    >
        <!-- Form search -->
        <a-form :model="viewLogData.formSearch" layout="inline" class="mt-3" @finish="onSearchViewLogData">
            <a-form-item class="mb-3" name="daterange">
                <a-range-picker
                    v-model:value="viewLogData.formSearch.daterange"
                    value-format="DD/MM/YYYY"
                    format="DD/MM/YYYY"
                />
            </a-form-item>
            <a-form-item class="mb-3" name="type_log">
                <select-custom
                    v-model:value="viewLogData.formSearch.type_log"
                    style="width: 150px"
                    placeholder="Kiểu"
                    :options="viewLogData?.others?.types"
                    show-search
                    allow-clear
                    :filter-option="utils.filterOption"
                />
            </a-form-item>
            <a-button class="mb-3" type="primary" html-type="submit">Tìm kiếm</a-button>
        </a-form>

        <!-- Lịch sử dữ liệu -->
        <a-table
            :columns="viewLogData.columns"
            :data-source="viewLogData.data_source"
            :loading="viewLogData.loading"
            :scroll="{ x: 'max-content' }"
            bordered
        >
            <template #bodyCell="{ column, record }">
                <template v-if="column.dataIndex === 'log_content'">
                    <span v-html="record.log_content"></span>
                </template>
            </template>
        </a-table>
    </a-modal>
</template>
<script>
import { Image, Tag, notification, Popconfirm, Mentions, Rate, Checkbox, AutoComplete } from '@lib/ant-design-vue';
import utils from '@root/utils';
import { ref } from '@lib/vue';
import { EditOutlined, DeleteTwoTone, EditTwoTone } from '@lib/@ant-design/icons-vue';
import SelectCustom from '@admin/components/select-custom.vue';
export default {
    components: {
        EditOutlined,
        ATag: Tag,
        AImage: Image,
        DeleteTwoTone,
        EditTwoTone,
        APopconfirm: Popconfirm,
        AMentions: Mentions,
        ARate: Rate,
        ACheckbox: Checkbox,
        AAutoComplete: AutoComplete,
        SelectCustom
    },
    data: () => ({}),
    watch: {},
    computed: {},
    props: {
        viewLogData: Object
    },
    methods: {
        onSearchViewLogData() {
            this.$emit('onSearchViewLogData');
        }
    }
};
</script>
<style></style>
