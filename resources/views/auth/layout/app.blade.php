<!DOCTYPE html>
<html lang="en" class="notranslate" translate="no">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="CMS">
    <meta name="author" content="PT Indonesia Comnets Plus">
    <link rel="icon" sizes="16x16" href="{{asset('favicon.ico')}}">
    <title>PT Indonesia Comnets Plus</title>
    <link href="{{asset('assets/plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('css/style.css')}}" rel="stylesheet">
    <link href="{{asset('css/colors/blue.css')}}" id="theme" rel="stylesheet">
    <link href="{{asset('css/custom.css')}}" rel="stylesheet">
    @yield('css')
    <style>
    .wallpaper-bg {
        background-image: url("{{asset('img/bg/cloud.jpg')}}");
        background-size: cover;
    }
    </style>
    @stack('styles')
</head>

<body>
    <section id="wrapper">
        <div class="login-register wallpaper-bg">
            <div class="row">
                @yield('content')
            </div>
        </div>
    </section>
    @include('layout.partial.modal_show_lg')
    <script src="{{asset('assets/plugins/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('assets/plugins/bootstrap/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('js/jquery.slimscroll.js')}}"></script>
    <script src="{{asset('js/sidebarmenu.js')}}"></script>
    <script src="{{asset('assets/plugins/sticky-kit-master/dist/sticky-kit.min.js')}}"></script>
    @yield('js')
    <script src="{{asset('js/custom.min.js')}}"></script>
    <script src="{{asset('js/app.js')}}"></script>
    @stack('scripts')
    @include('sweetalert::alert')
</body>

</html>