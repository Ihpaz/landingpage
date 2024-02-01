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
            <li class="breadcrumb-item">
                <a href="{{route('cms.user.show',Hashids::encode($user->id))}}">{{$title}}</a>
            </li>
            <li class="breadcrumb-item active">
                <a href="{{route('cms.user.edit',Hashids::encode($user->id))}}"><strong>{{trans('common.edit')}}</strong></a>
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
                <p class="mb-1"><i class="fa fa-phone"></i> &nbsp; {{$user->phonenumber ?? '-'}}</p>
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        <div class="card tabs-container">
            <ul class="nav nav-tabs customtab">
                <li><a class="nav-link active" data-toggle="tab" href="#user-information"> <i class="fa fa-laptop"></i> General Information</a></li>
            </ul>
            <div class="tab-content">
                <div id="user-information" class="tab-pane active">
                    <div class="p-20">
                        <form class="form" action="{{route('cms.user.update', Hashids::encode($user->id))}}" method="post" autocomplete="off">
                            @csrf
                            <input name="_method" type="hidden" value="PUT">
                            <div class="form-group row">
                                <label class="col-lg-2 col-form-label text-right font-bold">Role <label class="text-danger">*</label> :</label>
                                <div class="col-lg-10">
                                    <select id="select-roles" name="roles[]" class="select2 form-control" multiple="multiple">
                                        @foreach ($roles as $data)
                                        <option value="{{$data->name}}">{{strtoupper($data->name)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <label class="col-lg-2 col-form-label text-right"><strong>Status</strong> <label class="text-danger">*</label> :</label>
                                <div class="col-lg-10">
                                    <label class="radio-inline i-checks">
                                        <input type="radio" value="ACTV" name="status" {{($user->status == 'ACTV') ? "checked" : null}}> Active
                                    </label>
                                    <label class="radio-inline i-checks">
                                        <input type="radio" value="INAC" name="status" {{($user->status != 'ACTV') ? "checked" : null}}> In Active
                                </div>
                            </div>
                            <div class="form-group row mt-1">
                                <label class="col-lg-2 col-form-label text-right"><strong>Active Date</strong> <label class="text-danger">*</label> :</label>
                                <div class="col-lg-6">
                                    <div class="input-daterange input-group daterange">
                                        <input type="text" class="form-control" id="start_date" name="start_date" placeholder="Start Date" value="{{old('start_date')}}" autocomplete="off">
                                        <span class="input-group-addon bg-secondary b-0 pl-2 pr-2 text-white col-form-label">-</span>
                                        <input type="text" class="form-control" id="end_date" name="end_date" placeholder="End Date" value="{{old('end_date')}}" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <label class="col-lg-2 col-form-label text-right font-bold">Fullname :</label>
                                <div class="col-lg-10">
                                    <input type="text" name="fullname" value="{{$user->fullname}}" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 col-form-label text-right">Nickname :</label>
                                <div class="col-lg-10">
                                    <input type="text" name="nickname" value="{{$user->nickname}}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 col-form-label text-right">Phonenumber :</label>
                                <div class="col-lg-10">
                                    <input type="text" name="phonenumber" value="{{$user->phonenumber}}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 col-form-label text-right">NIP :</label>
                                <div class="col-lg-10">
                                    <input type="text" name="nip" value="{{$user->nip}}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 col-form-label text-right">Pernr :</label>
                                <div class="col-lg-10">
                                    <input type="text" name="pernr" value="{{$user->pernr}}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 col-form-label text-right">Company :</label>
                                <div class="col-lg-10">
                                    <input type="text" name="company" value="{{$user->company}}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 col-form-label text-right">Department :</label>
                                <div class="col-lg-10">
                                    <input type="text" name="department" value="{{$user->department}}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 col-form-label text-right">Position :</label>
                                <div class="col-lg-10">
                                    <input type="text" name="position" value="{{$user->position}}" class="form-control">
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <label class="col-lg-2 col-sm-3 col-form-label text-right font-bold">New Password :</label>
                                <div class="col-lg-10 col-sm-9">
                                    <input name="password" type="password" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="offset-lg-2 col-lg-10">
                                    <button class="btn btn-secondary btn-sm" type="submit" title="{{trans('common.save')}}"><i class="fa fa-check"></i> {{trans('common.save')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    $("#select-roles").select2().val([@foreach($user->getRoleNames()->toArray() as $role)
        "{{$role}}", @endforeach
    ]).change();

    $('.i-checks').iCheck({
        radioClass: 'iradio_square-blue',
        checkboxClass: 'icheckbox_square-blue'
    });

    $('#start_date').datepicker({
        autoclose: true,
        format: 'dd.mm.yyyy',
        orientation: 'bottom',
    }).on('changeDate', function(env) {
        $('#end_date').datepicker('setDate', null);
        $('#end_date').datepicker('setStartDate', env['date']);
    });

    $('#end_date').datepicker({
        autoclose: true,
        format: 'dd.mm.yyyy',
        orientation: 'bottom',
    });
</script>
@endpush