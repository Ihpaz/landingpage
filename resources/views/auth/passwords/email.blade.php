@extends('auth.layout.app')

@section('content')
<div class="login-box card">
    <div class="card-body">
        <div align="center">
            <img src="{{asset('img/cms.png')}}" style="height: 128px;" alt="" class="text-center" />
        </div>

        <form class="form-horizontal" role="form" method="post" action="{{route('password.email')}}" autocomplete="off">
            <h6 class="box-title m-b-20 text-center">KIT</h6>
            @csrf
            @include('layout.partial.alert_error')
            @include('layout.partial.alert_status')
            <div class="form-group ">
                <div class="col-xs-12">
                    <h6>Recover Password</h6>
                    <p class="text-muted">Enter your Email and instructions will be sent to you! </p>
                </div>
            </div>
            <div class="form-group ">
                <div class="col-xs-12">
                    <input name="email" type="email" class="form-control" placeholder="Email" value="{{old('email')}}" required>
                </div>
            </div>
            <div class="form-group text-center m-t-20">
                <div class="col-xs-12">
                    <button class="btn btn-sm btn-primary btn-block text-uppercase waves-effect waves-light" type="submit">Reset</button>
                </div>
            </div>
            <div class="col-sm-12 text-center">
                <div>Success reset password ? <a href="{{route('auth.login.index')}}" class="text-info"><b>Sign In</b></a></div>
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

@push('styles')
<style>
.login-register {
    position: fixed;
}
</style>
@endpush