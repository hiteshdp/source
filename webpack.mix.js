const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

/*mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        //
    ]);*/

mix.combine([
    'resources/assets/css/bootstrap.min.css',
    'resources/assets/css/aos.css',
    'resources/assets/css/jquery.autocomplete.css',
    ], 'public/css/all.css').version();

mix.combine([
    'resources/assets/js/jquery.min.js',
    'resources/assets/js/bootstrap.bundle.min.js',
    'resources/assets/js/slick.js',
    'resources/assets/js/aos.js',
    'resources/assets/js/jquery.validate.js',
    'resources/assets/js/jquery.cookieMessage.min.js',
    'resources/assets/js/jquery.autocomplete.js',
    'resources/assets/js/global.js',
    'resources/assets/js/custom.js',
    'resources/assets/js/bootstrap3-typeahead.min.js',
    'resources/js/sw.js'
], 'public/js/all.js').version();

mix.sass('resources/assets/sass/app.scss', 'public/css');
