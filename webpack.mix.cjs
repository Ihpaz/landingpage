const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.autoload({
    jquery: ['$','window.jQuery']
});
mix.combine([
    // Plugins
    'public/assets/plugins/pace/pace.css',
    'public/assets/plugins/icheck/skins/square/_all.css',
    'public/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css',
    'public/assets/plugins/select2/dist/css/select2.min.css',
    'public/assets/plugins/nestable/nestable.css',
    'public/assets/plugins/toast-master/css/jquery.toast.css',
    'public/assets/plugins/sweetalert/sweetalert.css',
    'public/assets/plugins/css-chart/css-chart.css',
    'public/assets/plugins/switchery/dist/switchery.min.css',
    'public/assets/plugins/bootstrap-tagsinput/dist/bootstrap-tagsinput.css',

],'public/dist/mix.css')
    .options({
        processUrls: false
    })
    .version();

mix.combine([
    // Mandatory
    'public/js/jquery.slimscroll.js',
    'public/js/waves.js',
    'public/js/sidebarmenu.js',

    // Plugins
    'public/assets/plugins/pace/pace.js',
    'public/assets/plugins/datatables/jquery.dataTables.min.js',
    'public/assets/plugins/datatables-buttons/js/dataTables.buttons.js',
    'public/vendor/datatables/buttons.server-side.js',
    'public/assets/plugins/icheck/icheck.min.js',
    'public/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js',
    'public/assets/plugins/masknumber/jquery.mask.min.js',
    'public/assets/plugins/toast-master/js/jquery.toast.js',
    'public/assets/plugins/nestable/jquery.nestable.js',
    'public/assets/plugins/select2/dist/js/select2.full.min.js',
    'public/vendor/sweetalert/sweetalert.all.js',
    'public/assets/plugins/switchery/dist/switchery.min.js',
    'public/assets/plugins/bootstrap-tagsinput/dist/bootstrap-tagsinput.js',

    // Custom
    'public/js/custom.min.js',
],'public/dist/mix.js').version();