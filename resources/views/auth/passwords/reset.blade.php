@extends('auth.layout.app')

@section('content')
<div class="login-box card">
    <div class="card-body">
        <div align="center">
            <img src="{{asset('img/cms.png')}}" style="height: 128px;" alt="" class="text-center" />
        </div>
        <form class="form-horizontal" role="form" method="post" action="{{route('password.update')}}" autocomplete="off">
            <h6 class="box-title m-b-20 text-center">CMS</h6>
            @include('layout.partial.alert_error')
            @csrf
            <input type="hidden" name="token" value="{{$token ?? ''}}">
            <div class="form-group p-t-10">
                <div class="col-xs-12">
                    <input name="email" type="email" class="form-control" placeholder="Email" value="{{ $email ?? old('email') }}" required autocomplete="email" readonly>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-12">
                    <input name="password" type="password" class="form-control" placeholder="Password" required>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-12">
                    <input name="password_confirmation" type="password" class="form-control" placeholder="Password Confirmation" required autocomplete="new-password">
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-12">
                    <ul style="padding-inline-start:15px">
                        <li>Password harus mengandung <b>huruf besar dan huruf kecil</b></li>
                        <li>Password harus mengandung <b>angka</b></li>
                        <li>Password harus mengandung <b>karakter spesial</b></li>
                        <li>Password minimal <b>8 karakter</b></li>
                    </ul>
                </div>
            </div>
            <div class="form-group text-center m-t-20">
                <div class="col-xs-12">
                    <button class="btn btn-sm btn-primary btn-block text-uppercase waves-effect waves-light" type="submit">Reset</button>
                </div>
            </div>
            <div class="form-group m-b-0">
                <div class="col-sm-12 text-center p-t-20">
                    <div>PT Indonesia Comnets Plus &copy; 2018 - {{date('Y')}}</div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
.login-register {
    position: fixed;
}
</style>
@endpush