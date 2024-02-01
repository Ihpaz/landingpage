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
                <strong>{{$title}}</strong>
            </li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="row pt-2">
    <div class="col-lg-3">
        <div class="card shadow">
            <div class="card-body p-0 border-left-right text-center">
                <input id="img_profile" type="image" alt="image" class="img-fluid" style="width:100%" src="{{asset('img/default-user.png')}}">
                <small><i>Click image to change profile picture</i></small>
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
                        <form class="form" action="{{route('cms.user.store')}}" method="post" autocomplete="off" enctype="multipart/form-data">
                            @csrf
                            <input type="file" id="thumbnail_photo" name="thumbnail_photo" style="display: none;" accept="image/*">
                            <div class="form-group row">
                                <label class="col-lg-2 col-form-label text-right font-bold">Role <label class="text-danger">*</label> :</label>
                                <div class="col-lg-10">
                                    <select id="roles" name="roles[]" class="select2 form-control" multiple="multiple" size="1" required>
                                        @foreach ($roles as $data)
                                        <option value="{{$data->name}}">{{strtoupper($data->name)}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 col-form-label text-right font-bold">Fullname <label class="text-danger">*</label> :</label>
                                <div class="col-lg-10">
                                    <input name="fullname" type="text" value="{{old('fullname')}}" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 col-form-label text-right font-bold">Email <label class="text-danger">*</label> :</label>
                                <div class="col-lg-10">
                                    <input name="email" type="email" value="{{old('email')}}" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 col-form-label text-right">Phone :</label>
                                <div class="col-lg-10">
                                    <input name="phonenumber" type="text" value="{{old('phonenumber')}}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 col-form-label text-right">NIP :</label>
                                <div class="col-lg-10">
                                    <input name="nip" type="text" value="{{old('nip')}}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 col-form-label text-right">Company :</label>
                                <div class="col-lg-10">
                                    <input name="company" type="text" value="{{old('company')}}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 col-form-label text-right">Department :</label>
                                <div class="col-lg-10">
                                    <input name="department" type="text" value="{{old('department')}}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 col-form-label text-right">Position :</label>
                                <div class="col-lg-10">
                                    <input name="position" type="text" value="{{old('position')}}" class="form-control">
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <label class="col-lg-2 col-sm-3 col-form-label text-right font-bold">Password :</label>
                                <div class="col-lg-10 col-sm-9">
                                    <input name="password" type="password" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row pt-2">
                                <div class="offset-lg-2 col-lg-10">
                                    <button class="btn btn-secondary btn-sm" type="submit"><i class="fa fa-check"></i> {{trans('common.save')}}</button>
                                    <small class="form-text mb-0">Note: <i>Password will be sent by email.</i> </small>
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
$('.i-checks').iCheck({
    checkboxClass: 'icheckbox_square-blue',
});

$("#roles.select2").select2({
    width: '100%',
    placeholder: "{{ trans('common.choose') }}"
});

$("#img_profile").click(function() {
    $("#thumbnail_photo").click();
});

$("#thumbnail_photo").change(function() {
    readURL(this);
});

$('#start_date').datepicker({
    autoclose: true,
    format: 'dd.mm.yyyy',
    orientation: 'top',
}).on('changeDate', function(env) {
    $('#end_date').datepicker('setDate', null);
    $('#end_date').datepicker('setStartDate', env['date']);
});

$('#end_date').datepicker({
    autoclose: true,
    format: 'dd.mm.yyyy',
    orientation: 'top',
});

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            $('#img_profile').attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush