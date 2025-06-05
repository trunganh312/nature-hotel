<template>
    <a-select
        v-bind="$attrs"
        v-on="$listeners"
        :open="dropdownOpen"
        :mode="mode"
        @keydown="handleKeyDown"
        @blur="handleBlur"
        @click="handleClick"
        :options="options"
        :value="localValue"
        @change="updateValue"
    >
        <slot></slot>
    </a-select>
</template>

<script>
export default {
    props: {
        value: [String, Number, Object, Array], // Nhận dữ liệu từ cha
        options: {
            type: Array,
            default: () => []
        },
        mode: {
            type: String,
            default: 'default'
        }
    },
    data() {
        return {
            dropdownOpen: false,
            localValue: this.value || this.value == 0 ? this.value : undefined, // Tạo bản sao giá trị để quản lý,
            // Check xem đã chọn xong chưa
            isSelected: false
        };
    },
    watch: {
        value(newValue) {
            this.localValue = newValue; // Cập nhật localValue khi prop value thay đổi
        },
        options: {
            handler(newOptions) {
                // Nếu options rỗng (đang tải), không làm gì cả
                if (!newOptions || newOptions.length === 0) {
                    return;
                }

                // Chỉ kiểm tra khi options đã có dữ liệu
                if (this.mode === 'multiple' && Array.isArray(this.localValue)) {
                    // Xử lý cho chế độ multiple
                    const filteredValues = this.localValue.filter((val) => newOptions.some((opt) => opt.value === val));
                    if (filteredValues.length !== this.localValue.length) {
                        this.localValue = filteredValues.length ? filteredValues : undefined;
                        this.$emit('update:value', this.localValue);
                    }
                } else if (this.localValue !== undefined) {
                    // Chỉ kiểm tra khi có giá trị
                    const valueExists = newOptions.some((opt) => opt.value === this.localValue);
                    if (!valueExists) {
                        this.localValue = undefined;
                        this.$emit('update:value', undefined);
                    }
                }
            },
            immediate: true // Giữ immediate true để chạy ngay, nhưng có điều kiện kiểm tra options trống
        }
    },

    methods: {
        handleKeyDown(event) {
            if (event.key === 'Enter') {
                event.preventDefault(); // Ngăn hành vi mặc định
                this.dropdownOpen = !this.dropdownOpen;
                // Kiểm tra xem nếu chọn r thì giữ chuyển thành false
                if (this.localValue && this.isSelected && this.mode != 'multiple') {
                    this.dropdownOpen = false;
                    this.isSelected = false;
                }
                if (this.mode == 'multiple') {
                    this.dropdownOpen = true;
                }
            } else if (event.key === 'Escape') {
                this.dropdownOpen = false;
                this.isSelected = false;
            }
        },
        handleClick() {
            this.dropdownOpen = true; // Luôn mở khi người dùng click
        },
        handleBlur() {
            this.dropdownOpen = false; // Đóng dropdown khi mất focus
        },
        updateValue(value) {
            this.localValue = value; // Cập nhật giá trị cục bộ
            this.$emit('update:value', value); // Gửi sự kiện lên cha để cập nhật
            this.dropdownOpen = false;
            this.isSelected = true;
        }
    }
};
</script>
