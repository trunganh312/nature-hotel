<template>
    <ckeditor :editor="editor" v-model="localContent" :config="editorConfig"></ckeditor>
</template>

<script>
import {
    ClassicEditor,
    Bold,
    Essentials,
    Heading,
    Indent,
    IndentBlock,
    Italic,
    List,
    ListProperties,
    Paragraph,
    SelectAll,
    TodoList,
    Underline,
    Undo,
    Image,
    ImageToolbar,
    ImageCaption,
    ImageStyle,
    ImageResize,
    LinkImage,
    ImageUpload,
    SimpleUploadAdapter,
    Alignment,
    Link,
    Table,
    TableToolbar,
    TableProperties,
    TableCellProperties
} from '@lib/ckeditor5';
import '@lib/ckeditor5/dist/ckeditor5.css';

export default {
    data() {
        return {
            editor: ClassicEditor,
            localContent: this.content,
            editorConfig: {
                plugins: [
                    Bold,
                    Essentials,
                    Heading,
                    Indent,
                    IndentBlock,
                    Italic,
                    List,
                    ListProperties,
                    Paragraph,
                    SelectAll,
                    TodoList,
                    Underline,
                    Undo,
                    Image,
                    ImageToolbar,
                    ImageCaption,
                    ImageStyle,
                    ImageResize,
                    LinkImage,
                    ImageUpload,
                    SimpleUploadAdapter,
                    Alignment,
                    Link,
                    Table,
                    TableToolbar,
                    TableProperties,
                    TableCellProperties
                ],
                toolbar: [
                    'undo',
                    'redo',
                    '|',
                    'heading',
                    '|',
                    'bold',
                    'italic',
                    'underline',
                    '|',
                    'link',
                    '|',
                    'alignment',
                    '|',
                    'bulletedList',
                    'numberedList',
                    'outdent',
                    'indent',
                    '|',
                    'imageUpload',
                    '|',
                    'insertTable'
                ],
                image: {
                    toolbar: [
                        'imageStyle:inline', // Kiểu ảnh inline
                        'imageStyle:block', // Kiểu ảnh block
                        'imageStyle:side', // Kiểu ảnh nằm bên
                        '|',
                        'toggleImageCaption', // Bật/tắt chú thích
                        'imageTextAlternative' // Văn bản thay thế cho hình ảnh
                    ],
                    styles: ['inline', 'block', 'side']
                },
                simpleUpload: {
                    uploadUrl: '/upload_image_ckeditor', // API nhận file upload
                    maxFileSize: 10 * 1024 * 1024,
                    errorHandler: function (error) {
                        console.error('Upload failed:', error);
                    }
                },

                table: {
                    contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells'],
                    tableProperties: {
                        borderColors: ['#000', '#f00', '#0f0', '#00f'],
                        backgroundColors: ['#fff', '#f00', '#0f0', '#00f']
                    },
                    tableCellProperties: {
                        borderColors: ['#000', '#f00', '#0f0', '#00f'],
                        backgroundColors: ['#fff', '#f00', '#0f0', '#00f']
                    }
                },
                licenseKey: '<YOUR_LICENSE_KEY>',
                mention: {
                    // Mention configuration
                }
            }
        };
    },
    props: {
        value: {
            type: String,
            required: true
        }
    },
    watch: {
        value: {
            handler(value) {
                this.localContent = value; // Sync local copy when prop changes
            },
            deep: true, // Watch deeply for changes in nested properties
            immediate: true // Sync immediately on initialization
        },
        localContent: function (value) {
            this.$emit('input', value);
        }
    }
};
</script>
<style>
/* This selector targets the editable element (excluding comments). */
.ck-editor__editable_inline:not(.ck-comment__input *) {
    height: 400px;
    overflow-y: auto;
}
</style>
