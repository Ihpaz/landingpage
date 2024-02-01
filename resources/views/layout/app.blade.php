@php
$pjax = false;
if(isset($_GET['_pjax'])) {
    $pjax = true;
}
@endphp

@if(isset($pjax) && $pjax == true)
    @yield('breadcrumb')
    <div class="container-fluid">
        @include('layout.partial.alert_error')
        @yield('content')
    </div>
    <footer class="footer text-center"> © 2023 - {{date('Y')}} <strong>Copyright</strong> PT Indonesia Comnets Plus </footer>
    @stack('styles')
    @stack('scripts')
@else
<!DOCTYPE html>
<html lang="en" class="notranslate" translate="no">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="KORPORAT PORTAL API GATEWAY">
    <meta name="author" content="DIVSTI PT Indonesia Comnets Plus">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" sizes="16x16" href="{{asset('favicon.ico')}}">
    <title>{{!empty($title) ?  config('app.name').' | '.$title : config('app.name')}}</title>
    <link href="{{asset('assets/plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{mix('dist/mix.css')}}" rel="stylesheet">
    @livewireStyles()
    <link href="{{asset('css/style.css')}}" rel="stylesheet">
    <link href="{{asset('css/colors/blue.css')}}" id="theme" rel="stylesheet">
    <link href="{{asset('css/custom.css')}}" rel="stylesheet">
    <pjax-stack-styles>
    @stack('styles')
    </pjax-stack-styles>
</head>
<body class="fix-header fix-sidebar card-no-border">
    <div id="main-wrapper">
        <header class="topbar">
            @include('layout.navbar')
        </header>

        <aside class="left-sidebar">
            <div class="scroll-sidebar">
            @include('layout.sidebar')
            </div>
        </aside>

        <div id="pjax-container" class="page-wrapper">
            @yield('breadcrumb')
            <div class="container-fluid">
                @include('layout.partial.alert_error')
                @yield('content')
            </div>
            <footer class="footer text-center"> © 2023 - {{date('Y')}} <strong>Copyright</strong> PT Indonesia Comnets Plus </footer>
        </div>
        @include('layout.partial.modal_show')
        @include('layout.partial.modal_delete')
        @include('layout.partial.modal_edit_lg')
        @include('layout.partial.modal_show_lg')
        @include('layout.partial.modal_embed_xl')
    </div>
    <script src="{{asset('assets/plugins/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('assets/plugins/pjax/jquery.pjax.js')}}"></script>
    <script src="{{asset('assets/plugins/bootstrap/js/popper.min.js')}}"></script>
    <script src="{{asset('assets/plugins/bootstrap/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('assets/plugins/sticky-kit-master/dist/sticky-kit.min.js')}}"></script>
    @livewireScripts()
    <script src="{{mix('dist/mix.js')}}"></script>
    <script src="{{asset('js/app.js')}}"></script>
    <script type="text/javascript">
    paceOptions = {
        ajax: true,
        document: false,
        eventLag: false,
    };
    loaderNotification("{{route('backend.api.notification')}}","{{route('backend.api.notification.redirect',':id')}}");
    </script>
    <pjax-stack-script>
    @stack('scripts')
    </pjax-stack-script>
    @include('sweetalert::alert')
</body>
</html>
@endif