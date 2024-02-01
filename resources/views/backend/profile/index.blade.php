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
                <label class="mb-0">{{$title}}</label>
            </li>
            <li class="breadcrumb-item active">
                <a href="{{route('backend.profile.index')}}"><strong>{{auth()->user()->fullname}}</strong></a>
            </li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="row pt-2">
    <div class="col-lg-3">
        <div class="card shadow">
            <div class="card-body p-0 border-left-right">
                <img alt="image" class="img-fluid" style="width:100%" src="{{auth()->user()->user_thumbnail}}">
            </div>
            <div class="card-body profile-content">
                <h3 class="text-center"><strong>{{auth()->user()->fullname}}</strong></h3>
                <p class="mb-1"><i class="fa fa-envelope"></i> &nbsp;<a href="mailto:{{auth()->user()->email}}">{{auth()->user()->email}}</a></p>
                <p class="mb-1"><i class="fa fa-phone"></i> &nbsp;{{auth()->user()->phonenumber ?? '-'}}</p>
                <hr />
                <h4><strong>Timeline activity</strong></h4>
                <div class="client-detail">
                    <div class="profile-activity-scroll">
                        <div id="vertical-timeline" class="profiletimeline">
                            @foreach($activities as $data)
                            <div class="sl-item">
                                <div class="sl-left"> <img src="{{auth()->user()->user_thumbnail}}" alt="user" class="img-circle" style="width:40px;height:40px;"> </div>
                                <div class="sl-right">
                                    <div>{{auth()->user()->fullname}} <span class="sl-date">{{$data->created_at->diffForHumans()}}</span>
                                        <p>{!!$data->description!!}</p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        <div class="card tabs-container">
            <ul id="tabMenu" class="nav nav-tabs customtab">
                <li><a class="nav-link active" data-toggle="tab" href="#user-information"> <i class="fa fa-laptop"></i> General Information</a></li>
                <li><a class="nav-link" data-toggle="tab" href="#personal"> <i class="fa fa-id-card"></i> Personal Information</a></li>
                <li><a class="nav-link" data-toggle="tab" href="#document"> <i class="fa fa-folder-open"></i> Document</a></li>
                <li><a class="nav-link" data-toggle="tab" href="#mfa"> <i class="fa fa-key"></i> Multi-Factor</a></li>
                <li><a class="nav-link" data-toggle="tab" href="#access_token"> <i class="fa fa-id-badge"></i> Access Tokens</a></li>
            </ul>
            <div class="tab-content shadow">
                <div id="user-information" class="tab-pane active">
                    <div class="p-20">
                        <form class="form">
                            <div class="form-group row mb-0">
                                <label class="col-lg-2 col-sm-3 col-form-label text-right font-bold">Fullname :</label>
                                <div class="col-lg-10 col-sm-9">
                                    <p class="col-form-label">{{auth()->user()->fullname}}</p>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <label class="col-lg-2 col-sm-3 col-form-label text-right"><strong>NIP</strong> :</label>
                                <div class="col-lg-10 col-sm-9">
                                    <p class="col-form-label">{{auth()->user()->nip}}</p>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <label class="col-lg-2 col-sm-3 col-form-label text-right"><strong>Pernr</strong> :</label>
                                <div class="col-lg-10 col-sm-9">
                                    <p class="col-form-label">{{auth()->user()->pernr}}</p>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <label class="col-lg-2 col-sm-3 col-form-label text-right"><strong>Company</strong> :</label>
                                <div class="col-lg-10 col-sm-9">
                                    <p class="col-form-label">{{auth()->user()->company}}</p>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <label class="col-lg-2 col-sm-3 col-form-label text-right"><strong>Department</strong> :</label>
                                <div class="col-lg-10 col-sm-9">
                                    <p class="col-form-label">{{auth()->user()->department}}</p>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <label class="col-lg-2 col-sm-3 col-form-label text-right"><strong>Position</strong> :</label>
                                <div class="col-lg-10 col-sm-9">
                                    <p class="col-form-label">{{auth()->user()->position}}</p>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="offset-lg-2 col-lg-10 offset-sm-3 col-sm-9">
                                    <a href="{{route('backend.profile.edit')}}" class="btn btn-xs btn-secondary"><i class="fa fa-pencil"></i> {{trans('common.edit')}} </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="access_token" class="tab-pane">
                    @include('backend.profile.partial.token.index')
                </div>

                <div id="document" class="tab-pane">
                    @include('backend.profile.partial.document.index')
                </div>

                <div id="personal" class="tab-pane">
                    @include('backend.profile.partial.personal.index')
                </div>

                <div id="mfa" class="tab-pane">
                    @livewire('mfa.totp-authenticator', ['user' => auth()->user()])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.client-detail {
    margin: 10px 0px;
}
</style>
@endpush

@push('scripts')
<script type="text/javascript">
$('.profile-activity-scroll').slimScroll({
    position: 'right',
    height: '200px',
});

$('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
    $($.fn.dataTable.tables(true)).DataTable()
        .columns.adjust();
});
$("#tabMenu a[href='#{{old('tab')}}']").tab('show');
</script>
@endpush