@extends('layout.app')

@section('breadcrumb')
<div class="row page-titles">
    <div class="col-lg-5 align-self-center">
        <h3>{{$title}}</h3>
    </div>
    <div class="col-lg-7 align-self-center">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('backend.dashboard.index')}}">Home</a>
            </li>
            <li class="breadcrumb-item">
                <label class="mb-0">Administrator</label>
            </li>
            <li class="breadcrumb-item">
                <label class="mb-0">Tools</label>
            </li>
            <li class="breadcrumb-item active">
                <strong>{{$title}}</strong>
            </li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-5">
        <div class="card shadow">
            <div class="card-body">
                <div id="markdown" class="changelog-scroll">
                    {{$file_changelog}}
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card tabs-container">
            <ul class="nav nav-tabs customtab">
                @if(config('app.debug'))
                <li><a class="nav-link active" data-toggle="tab" href="#server-information"> <i class="fa fa-desktop"></i> Server Environment</a></li>
                @endif
                <li><a class="nav-link @if(!config('app.debug')) active @endif" data-toggle="tab" href="#system-disk"> <i class="fa fa-database"></i> System Disk</a></li>
                <li><a class="nav-link" data-toggle="tab" href="#maintenance"> <i class="fa fa-github"></i> Maintener</a></li>
            </ul>
            <div class="tab-content shadow">
                @if(config('app.debug'))
                <div id="server-information" class="tab-pane active">
                    <div class="p-20">
                        <ul class="list-group">
                            <li class="list-group-item">PHP Version: {{ $server_env['version'] }}</li>
                            <li class="list-group-item">Server Software: {{ $server_env['server_software'] }}</li>
                            <li class="list-group-item">Server OS: {{ $server_env['server_os'] }}</li>
                            <li class="list-group-item">Database: {{ $server_env['database_connection_name'] }}</li>
                            <li class="list-group-item">SSL Installed: {!! $server_env['ssl_installed'] ? '<span class="fa fa-check"></span>' : '<span class="fa fa-times"></span>' !!}</li>
                            <li class="list-group-item">Cache Driver: {{ $server_env['cache_driver'] }}</li>
                            <li class="list-group-item">Session Driver: {{ $server_env['session_driver'] }}</li>
                            <li class="list-group-item">Openssl Ext: {!! $server_env['openssl'] ? '<span class="fa fa-check"></span>' : '<span class="fa fa-times"></span>' !!}</li>
                            <li class="list-group-item">PDO Ext: {!! $server_env['pdo'] ? '<span class="fa fa-check"></span>' : '<span class="fa fa-times"></span>' !!}</li>
                            <li class="list-group-item">Mbstring Ext: {!! $server_env['mbstring'] ? '<span class="fa fa-check"></span>' : '<span class="fa fa-times"></span>' !!}</li>
                            <li class="list-group-item">Tokenizer Ext: {!! $server_env['tokenizer'] ? '<span class="fa fa-check"></span>' : '<span class="fa fa-times"></span>'!!}</li>
                            <li class="list-group-item">XML Ext: {!! $server_env['xml'] ? '<span class="fa fa-check"></span>' : '<span class="fa fa-times"></span>' !!}</li>
                            @foreach($server_extras as $key => $value)
                            <li class="list-group-item">{{ $key }}: {!! is_bool($value) ? ($value ? '<span class="fa fa-check"></span>' : '<span class="fa fa-times"></span>') : $extraStatValue !!}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
                <div id="system-disk" class="tab-pane @if(!config('app.debug')) active @endif">
                    <div class="p-20">
                        <ul class="list-group">
                            <li class="list-group-item">Uptime: {{$uptime}}</li>
                            <li class="list-group-item">CPU Usage: {{$cpu_load}} %</li>
                            <li class="list-group-item">Memory Usage: {{$memory_usage}} GB / {{$total_ram}} GB</li>
                            <li class="list-group-item">Database Usage: {{$db_used}}</li>
                            <li class="list-group-item">Storage Usage: {{HumanReadable::bytesToHuman($disk_used)}} / {{HumanReadable::bytesToHuman($disk_total)}}</li>
                        </ul>
                    </div>
                </div>
                <div id="maintenance" class="tab-pane">
                    <div class="p-20">
                        <ul class="list-group">
                            <li class="list-group-item">Git Version: {{@$git_version}}</li>
                            <li class="list-group-item">Last Update: {{@$git_last_update}}</li>
                            <li class="list-group-item">Maintener:
                                <ul>
                                    @foreach(@$git_developer as $data)
                                    <li>{!! $data !!}</li>
                                    @endforeach
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.changelog-scroll {
    max-height: 800px;
}

.list-group-item {
    padding: 5px 15px;
}
</style>
@endpush

@push('scripts')
<script type="text/javascript">
var converter = new showdown.Converter(),
    text = $('#markdown').html(),
    html = converter.makeHtml(text);

$('#markdown').html(html);

$('.changelog-scroll').slimscroll({
    position: 'right',
    height: '400px',
});
</script>
@endpush