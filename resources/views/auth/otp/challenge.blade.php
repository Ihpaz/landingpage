@extends('auth.layout.app')

@section('content')
<div class="login-box card">
    <div class="card-body">
        <div align="center">
            <img src="{{asset('img/logo_pln.png')}}" style="height: 128px;" alt="" class="text-center" />
        </div>
        <form class="form-horizontal" role="form" method="post" action="{{route('mfa.challenge')}}">
            <h6 class="box-title m-b-20 text-center">One Time Password</h6>
            @include('layout.partial.alert_error')
            @include('layout.partial.alert_status')
            @csrf
            <div class="form-group p-t-1">
                <div class="col-xs-12">
                    <input name="one_time_password" type="text" class="form-control" placeholder="* * * * * *" required>
                </div>
            </div>
            <div class="form-group text-center m-t-20">
                <div class="col-xs-12">
                    <button class="btn btn-sm btn-info btn-block text-uppercase waves-effect waves-light" type="submit">{{trans('common.submit')}}</button>
                </div>
            </div>
            <div class="form-group m-b-0">
                <div class="col-sm-12 text-center p-t-20 font-14">
                    <div>PT PLN (Persero) &copy; 2018 - {{date('Y')}}</div>
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