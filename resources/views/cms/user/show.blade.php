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
                <label class="mb-0">User Management</label>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('cms.user.index')}}">Users</a>
            </li>
            <li class="breadcrumb-item active">
                <a href="{{route('cms.user.show',Hashids::encode($user->id))}}"><strong>{{$title}}</strong></a>
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
                <img alt="image" class="img-fluid" style="width:100%" src="{{$user->user_thumbnail}}">
            </div>
            <div class="card-body profile-content">
                <h3 class="text-center mb-0"><strong>{{$user->fullname}}</strong></h3>
                <p class="mb-1"><i class="fa fa-envelope"></i> &nbsp;<a href="mailto:{{$user->email}}">{{$user->email}}</a></p>
                <p class="mb-1"><i class="fa fa-phone"></i> &nbsp;{{$user->phonenumber ?? '-'}}</p>
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        <div class="card tabs-container">
            <ul class="nav nav-tabs customtab">
                <li><a class="nav-link active" data-toggle="tab" href="#user-information"> <i class="fa fa-laptop"></i> General Information</a></li>
                <li><a class="nav-link" data-toggle="tab" href="#personal"> <i class="fa fa-id-card"></i> Personal Information</a></li>
                @can('cms user-token view')
                <li><a class="nav-link" data-toggle="tab" href="#access_token"> <i class="fa fa-id-badge"></i> Access Tokens</a></li>
                @endcan
                <li><a class="nav-link" data-toggle="tab" href="#mfa"> <i class="fa fa-key"></i> Multi-Factor</a></li>
                <li><a class="nav-link" data-toggle="tab" href="#login"> <i class="fa fa-sign-in"></i> Recent Logins</a></li>
            </ul>
            <div class="tab-content">
                <div id="user-information" class="tab-pane active">
                    <div class="p-20">
                        <form class="form">
                            <div class="form-group row mb-0">
                                <label class="col-lg-2 col-form-label text-right">Role :</label>
                                <div class="col-lg-10 col-form-label">
                                    @forelse ($user->getRoleNames() as $data)
                                    <span class="label label-info">{{strtoupper($data)}}</span>
                                    @empty
                                    <span>-</span>
                                    @endforelse
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <label class="col-lg-2 col-form-label text-right">Status :</label>
                                <div class="col-lg-10">
                                    <p class="col-form-label">{{$user->status}}</p>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <label class="col-lg-2 col-form-label text-right">NIP :</label>
                                <div class="col-lg-10">
                                    <p class="col-form-label">{{$user->nip}}</p>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <label class="col-lg-2 col-form-label text-right">Pernr :</label>
                                <div class="col-lg-10">
                                    <p class="col-form-label">{{$user->pernr}}</p>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <label class="col-lg-2 col-form-label text-right">Company :</label>
                                <div class="col-lg-10">
                                    <p class="col-form-label">{{$user->company}}</p>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <label class="col-lg-2 col-form-label text-right">Department :</label>
                                <div class="col-lg-10">
                                    <p class="col-form-label">{{$user->department}}</p>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <label class="col-lg-2 col-form-label text-right">Position :</label>
                                <div class="col-lg-10">
                                    <p class="col-form-label">{{$user->position}}</p>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <div class="offset-lg-2 col-lg-10">
                                    @can('cms user-management update')
                                    <a href="{{route('cms.user.edit', Hashids::encode($user->id))}}" class="btn btn-secondary btn-sm" title="Ubah"><i class="fa fa-pencil"></i> {{trans('common.edit')}}</a>
                                    @endcan
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                @can('cms user-token view')
                <div id="access_token" class="tab-pane">
                    @include('cms.user.partial.token.index')
                </div>
                @endcan
                <div id="personal" class="tab-pane">
                    @include('cms.user.partial.personal.index')
                </div>
                <div id="login" class="tab-pane">
                    @include('cms.user.partial.login.index')
                </div>
                <div id="mfa" class="tab-pane">
                    @include('cms.user.partial.mfa.index')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .detail-info {
        white-space: normal !important;
        max-width: 3000px;
        min-width: 50px;
        margin-right: 5px;
        margin-left: 5px;
    }
</style>
@endpush

@push('scripts')
<script type="text/javascript">
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });
</script>
@endpush