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
            <li class="breadcrumb-item">
                <a href="{{route('cms.activity.index')}}">Activity Log</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>{{$title}}</strong>
            </li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="row pt-2">
    <div class="col-lg-12">
        <div class="card tabs-container">
            <ul class="nav nav-tabs customtab">
                <li><a class="nav-link active show" data-toggle="tab" href="#log-summary"><i class="fa fa-archive"></i> {{$title}}</a></li>
                @if($activity->properties->has('attributes'))
                <li><a class="nav-link" data-toggle="tab" href="#log-detail"><i class="fa fa-search"></i> Detail</a></li>
                @endif
            </ul>
            <div class="tab-content shadow">
                <div id="log-summary" class="tab-pane active show">
                    <div class="p-20">
                        <div class="row">
                            <div class="col-lg-12">
                                <form class="form">
                                    <div class="form-group row mb-0">
                                        <label class="col-lg-2 col-form-label text-right"><strong>User</strong> :</label>
                                        <div class="col-lg-10">
                                            <p class="col-form-label">{{$activity->causer->fullname ?? '-'}}</p>
                                        </div>
                                    </div>
                                    @if($activity->properties->has('impersonator'))
                                    <div class="form-group row mb-0">
                                        <label class="col-lg-2 col-form-label text-right"><strong>Impersonator</strong> :</label>
                                        <div class="col-lg-10">
                                            <p class="col-form-label"><span class="label label-inverse">{{isset($activity->properties['impersonator']['fullname']) ? $activity->properties['impersonator']['fullname'] : $activity->properties['impersonator']}}</span></p>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="form-group row mb-0">
                                        <label class="col-lg-2 col-form-label text-right"><strong>Log Type</strong> :</label>
                                        <div class="col-lg-10">
                                            <p class="col-form-label">{!!$logname!!}</p>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-0">
                                        <label class="col-lg-2 col-form-label text-right font-bold">{{trans('label.description')}} :</label>
                                        <div class="col-lg-10">
                                            <p class="col-form-label">{!!$activity->description!!}</p>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-0">
                                        <label class="col-lg-2 col-form-label text-right"><strong>Timestamp</strong> :</label>
                                        <div class="col-lg-10">
                                            <p class="col-form-label">{{$activity->created_at}}</p>
                                        </div>
                                    </div>
                                    @if($activity->properties->has('url'))
                                    <div class="form-group row mb-0">
                                        <label class="col-lg-2 col-form-label text-right"><strong>Url</strong> :</label>
                                        <div class="col-lg-10">
                                            <p class="col-form-label"><a href="{{$activity->properties->get('url')}}">{{$activity->properties->get('url')}}</a></p>
                                        </div>
                                    </div>
                                    @endif
                                    @if($activity->properties->has('ipaddress'))
                                    <div class="form-group row mb-0">
                                        <label class="col-lg-2 col-form-label text-right"><strong>Ip Address</strong> :</label>
                                        <div class="col-lg-10">
                                            <p class="col-form-label">{{$activity->properties['ipaddress']}}</p>
                                        </div>
                                    </div>
                                    @endif
                                    @if($activity->properties->has('useragent'))
                                    <div class="form-group row mb-0">
                                        <label class="col-lg-2 col-form-label text-right"><strong>User Agent</strong> :</label>
                                        <div class="col-lg-10">
                                            <p class="col-form-label">{{$activity->properties['useragent']}}</p>
                                        </div>
                                    </div>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="log-detail" class="tab-pane">
                    <div class="p-20">
                        <div class="row">
                            <div class="col-lg-6">
                                <form class="form">
                                    @if($activity->properties->has('old'))
                                    <div class="form-group row mb-0">
                                        <label class="col-lg-12 col-form-label text-right text-center"><strong>New</strong></label>
                                    </div>
                                    <hr class="mb-1 mt-0">
                                    @else
                                    <div class="form-group row mb-0">
                                        <label class="col-lg-12 col-form-label text-right text-center"><strong>Attributes</strong></label>
                                    </div>
                                    <hr class="mb-1 mt-0">
                                    @endif
                                    @if($activity->properties->has('attributes'))
                                    @foreach($activity->properties['attributes'] as $index=>$key)
                                    <div class="form-group row mb-0">
                                        <label class="col-lg-3 col-form-label text-right"><strong>{{str_replace('_',' ',$index)}}</strong> :</label>
                                        <div class="col-lg-9">
                                            <p id="{{$index}}-new" class="col-form-label">{{is_bool($key) ? ($key ? 'true' : 'false') : (is_string($key) ? $key : json_encode($key)) }}</p>
                                        </div>
                                    </div>
                                    @endforeach
                                    @endif
                                </form>
                            </div>
                            @if($activity->properties->has('old'))
                            <div class="col-lg-6">
                                <form class="form">
                                    <div class="form-group row mb-0">
                                        <label class="col-lg-12 col-form-label text-right text-center"><strong>Old</strong></label>
                                    </div>
                                    <hr class="mb-1 mt-0">
                                    @if($activity->properties->has('old'))
                                    @foreach($activity->properties['old'] as $index=>$key)
                                    <div class="form-group row mb-0">
                                        <label class="col-lg-3 col-form-label text-right"><strong>{{str_replace('_',' ',$index)}}</strong> :</label>
                                        <div class="col-lg-9">
                                            <p id="{{$index}}-old" class="col-form-label">{{is_bool($key) ? ($key ? 'true' : 'false') : (is_string($key) ? $key : json_encode($key)) }}</p>
                                        </div>
                                    </div>
                                    @endforeach
                                    @endif
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.bold {
    font-weight: 700 !important;
}
</style>
@endpush

@push('scripts')
<script type="text/javascript">
@if($activity->properties->has('old'))
@foreach($activity->properties['old'] as $index => $key)
if ($('#{{$index}}-old').text() != $('#{{$index}}-new').text()) {
    $('#{{$index}}-new').addClass('bold text-warning');
};
@endforeach
@endif
</script>
@endpush