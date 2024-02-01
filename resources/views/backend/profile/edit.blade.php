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
            <li class="breadcrumb-item">
                <a href="{{route('backend.profile.index')}}">{{auth()->user()->fullname}}</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('backend.profile.edit')}}"><strong>{{trans('common.edit')}}</strong></a>
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
                <input id="img_profile" type="image" alt="image" class="img-fluid" style="width:100%" src="{{auth()->user()->user_thumbnail}}">
                <small><i>Click image to change profile picture</i></small>
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        <div class="card tabs-container">
            <ul id="tabMenu" class="nav nav-tabs customtab">
                <li><a class="nav-link active" data-toggle="tab" href="#user-information"> <i class="fa fa-laptop"></i> General Information</a></li>
            </ul>
            <div class="tab-content">
                <div id="user-information" class="tab-pane active">
                    <div class="p-20">
                        <form class="form" action="{{route('backend.profile.update')}}" method="post" autocomplete="off" enctype="multipart/form-data">
                            @csrf
                            <input type="file" id="thumbnail_photo" name="thumbnail_photo" style="display: none;" accept="image/*">
                            <div class="form-group row">
                                <label class="col-lg-2 col-sm-3 col-form-label text-right font-bold">Fullname :</label>
                                <div class="col-lg-10 col-sm-9">
                                    <input name="fullname" type="text" value="{{auth()->user()->fullname}}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 col-sm-3 col-form-label text-right font-bold">Nickname :</label>
                                <div class="col-lg-10 col-sm-9">
                                    <input name="nickname" type="text" value="{{auth()->user()->nickname}}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 col-sm-3 col-form-label text-right font-bold">Email :</label>
                                <div class="col-lg-10 col-sm-9">
                                    <p class="col-form-label">{{auth()->user()->email}}</p>

                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 col-sm-3 col-form-label text-right">Phone :</label>
                                <div class="col-lg-10 col-sm-9">
                                    <input name="phonenumber" type="text" value="{{auth()->user()->phonenumber ?? old('phonenumber')}}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 col-sm-3 col-form-label text-right">NIP :</label>
                                <div class="col-lg-10 col-sm-9">
                                    <input name="nip" type="text" value="{{auth()->user()->nip ?? old('nip')}}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 col-sm-3 col-form-label text-right">Company :</label>
                                <div class="col-lg-10 col-sm-9">
                                    <input name="company" type="text" value="{{auth()->user()->company ?? old('company')}}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 col-sm-3 col-form-label text-right">Department :</label>
                                <div class="col-lg-10 col-sm-9">
                                    <input name="department" type="text" value="{{auth()->user()->department ?? old('department')}}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 col-sm-3 col-form-label text-right">Position :</label>
                                <div class="col-lg-10 col-sm-9">
                                    <input name="position" type="text" value="{{auth()->user()->position ?? old('position')}}" class="form-control">
                                </div>
                            </div>
                            <hr>
                            <div class="form-group row">
                                <label class="col-lg-2 col-sm-3 col-form-label text-right font-bold">Current Password :</label>
                                <div class="col-lg-10 col-sm-9">
                                    <input name="old_password" type="password" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 col-sm-3 col-form-label text-right font-bold">New Password :</label>
                                <div class="col-lg-10 col-sm-9">
                                    <input name="password" type="password" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-lg-2 col-sm-3 col-form-label text-right">Confirmation :</label>
                                <div class="col-lg-10 col-sm-9">
                                    <input name="password_confirmation" type="password" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="offset-lg-2 col-lg-10 offset-sm-3 col-sm-9">
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
    $("#tabMenu a[href='#{{old('tab')}}']").tab('show');

    $("#select-roles").select2();

    $('.i-checks').iCheck({
        radioClass: 'iradio_square-blue',
    });

    $("#img_profile").click(function() {
        $("#thumbnail_photo").click();
    });

    $("#thumbnail_photo").change(function() {
        readURL(this);
    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#img_profile').attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        };
    };
</script>
@endpush