@extends('auth.layout.app')

@section('content')
<div class="login-box card">
    <div class="card-body">
        <div align="center">
            <img src="{{asset('img/cms.png')}}" style="height: 52px;" alt="" class="text-center" />
        </div>
        <form class="form-horizontal" role="form" method="post" action="{{route('auth.register')}}" autocomplete="off">
            <h6 class="box-title m-b-20 text-center"></h6>
            @include('layout.partial.alert_error')
            @include('layout.partial.alert_status')
            @csrf
            <div class="form-group p-t-1">
                <div class="col-xs-12">
                    <input name="fullname" type="text" class="form-control" placeholder="Nama Lengkap" value="{{old('fullname')}}" required>
                </div>
            </div>
            <div class="form-group p-t-1">
                <div class="col-xs-12">
                    <input name="company" type="text" class="form-control" placeholder="Instansi" value="{{old('company')}}" required>
                </div>
            </div>
            <div class="form-group p-t-1">
                <div class="col-xs-12">
                    <input name="title" type="text" class="form-control" placeholder="Jabatan" value="{{old('title')}}" required>
                </div>
            </div>
            <div class="form-group p-t-1">
                <div class="col-xs-12">
                    <input name="phonenumber" type="text" class="form-control" placeholder="No Telp" value="{{old('phonenumber')}}" required>
                </div>
            </div>
            <div class="form-group p-t-1">
                <div class="col-xs-12">
                    <input name="email" type="email" class="form-control" placeholder="Email" value="{{old('email')}}" required>
                </div>
            </div>
            <div class="form-group p-t-1">
                <div class="col-xs-12">
                    <input name="password" type="password" class="form-control" placeholder="Password" required>
                </div>
            </div>
            <div class="form-group p-t-1 row m-0">
                <div class="col-sm-6 pl-0 pb-2 w-50">
                    <input name="captcha" type="text" class="form-control" placeholder="Input Captcha" required>
                </div>
                <div class="col-sm-6 pl-0 pr-0 text-center w-50">
                    @captcha
                </div>
            </div>
            <div class="form-group p-t-1">
                <div class="col-xs-12">
                    <div class="i-checks">
                        <label>
                            <input type="checkbox" name="terms" required>
                            Dengan ini saya menyatakan bahwa data yang saya isi adalah benar.
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group text-center m-t-20">
                <div class="col-xs-12">
                    <button class="btn btn-sm btn-info btn-block text-uppercase waves-effect waves-light" type="submit">Submit</button>
                </div>
            </div>
            <div class="col-sm-12 text-center">
                <div>Have an account ? <a href="{{route('auth.login.index')}}" class="text-info m-l-5"><b>Sign In</b></a></div>
            </div>
            <div class="form-group m-b-0">
                <div class="col-sm-12 text-center p-t-20 font-14">
                    <div>PT Indonesia Comnets Plus &copy; 2018 - {{date('Y')}}</div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('css')
<link href="{{asset('assets/plugins/icheck/skins/all.css')}}" rel="stylesheet">
@endsection

@push('styles')
<style>
.login-register {
    position: fixed;
}
</style>
@endpush

@section('js')
<script src="{{asset('assets/plugins/icheck/icheck.min.js')}}"></script>
@endsection

@push('scripts')
<script type="text/javascript">
$('.i-checks').iCheck({
    checkboxClass: 'icheckbox_square-blue',
});
</script>
@endpush