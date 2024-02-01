@extends('auth.layout.app')

@section('content')
<div class="login-box card">
    <div class="card-body">
        <div align="center">
            <img src="{{asset('img/data-server.png')}}" style="height: 128px;" alt="" class="text-center" />
        </div>
        <form class="form-horizontal" role="form" method="post" action="{{route('auth.login')}}" autocomplete="off">
            <h6 class="box-title m-b-20 text-center">KIT MANTAP</h6>
            @include('layout.partial.alert_error')
            @include('layout.partial.alert_status')
            @csrf
            <div class="form-group p-t-1 row">
                <div class="col-sm-12">
                    <input name="email" type="text" class="form-control" placeholder="Email" value="{{old('email')}}" required>
                </div>
            </div>
            <div class="form-group p-t-1 row">
                <div class="col-sm-12">
                    <input name="password" type="password" class="form-control" placeholder="Password" required>
                </div>
            </div>
            <div class="form-group p-t-1 row">
                <div class="col-sm-6">
                    <input name="captcha" type="text" class="form-control" placeholder="Input Captcha" required>
                </div>
                <div class="col-sm-6 pl-0 pr-0 text-center">
                    {!! captcha_img('math') !!}
                </div>
            </div>
            <div class="form-group row text-center m-t-20">
                <div class="col-sm-12">
                    <button class="btn btn-sm btn-info btn-block text-uppercase waves-effect waves-light" type="submit">Sign In</button>
                </div>
            </div>
            <div class="form-group row text-center">
                <div class="col-sm-12">
                    <a href="{{route('oauth.google.redirect')}}" class="btn btn-secondary btn-sm btn-block no-border" title="Login via Google"><i class="fa fa-google"></i> Login Via Google</a>
                </div>
            </div>
            <div class="form-group p-t-1 text-center row">
                <div class="col-sm-12">
                    <div><a href="{{route('password.request')}}" class="text-secondary m-l-5"><b>Forgot Password? </b></a></div>
                </div>
            </div>
            <div class="form-group m-b-0">
                <div class="col-sm-12 text-center p-t-20 font-14">
                    <div>PT Indonesia Comnets Plus &copy; 2021</div>
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