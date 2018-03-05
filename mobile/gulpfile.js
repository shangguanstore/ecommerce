/*
 |--------------------------------------------------------------------------
 | Elixir 环境安装
 |--------------------------------------------------------------------------
 |
 | 一、安装 Node
 | 访问 nodejs 官网 [ https://nodejs.org/en/ ]，下载node进行安装。
 |
 | 二、配置淘宝 NPM 镜像（可选）
 | npm install -g cnpm --registry=https://registry.npm.taobao.org
 |
 | 三、安装 Gulp
 | cnpm install --global gulp-cli
 |
 | 四、安装 Package
 | cnpm install
 |
 | 五、使用 Gulp 打包样式和脚本
 | gulp （开发模式）
 | gulp --production （生产模式）
 |
 */

const elixir = require('laravel-elixir');

const paths = require('./gulpconf.js');

require('laravel-elixir-vue-2');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Dscmall application. By default, we are compiling the Sass
 | file for your application as well as publishing vendor resources.
 |
 */

elixir.config.assetsPath = '';
elixir.config.css.folder = '';
elixir.config.js.folder = '';
elixir.config.sourcemaps = false;

elixir(mix => {
    mix.styles(paths.css, paths.dist + 'css/app.min.css');
    mix.scripts(paths.js, paths.dist + 'js/app.min.js');
});
