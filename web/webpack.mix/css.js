// npm i -D @fullhuman/postcss-purgecss@3.0.0 postcss@7.0.35

const mix = require('laravel-mix');
require('laravel-mix-purgecss');
const glob = require('glob-all');
const path = require('path');

const scss_files = [
    'style',
    'profile',
    'vietgoing'
];
for(let i in scss_files)
    mix.postCss(`theme/css_dev/${scss_files[i]}.css`, `theme/css/${scss_files[i]}.css`);

mix.options({
      processCssUrls: false,
    })
    .purgeCss({
        enabled: true,
        extend: {
            content: [
                path.join(__dirname, '../page/**/*.php'),
                path.join(__dirname, '../page/**/**/*.php'),
                path.join(__dirname, '../layout/*.php'),
                path.join(__dirname, '../profile/*.php'),
                path.join(__dirname, '../profile/view/*.php'),
                path.join(__dirname, '../../Vietgoing_Core/Model/*.php'),
                path.join(__dirname, 'html_static.txt'),
            ]
        }
    });