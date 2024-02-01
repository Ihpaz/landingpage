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
                <label class="mb-0">Master Data</label>
            </li>
            <li class="breadcrumb-item">
                <label class="mb-0">Location</label>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('master.location.regency.index')}}">Kota</a>
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
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header bg-blue p-15">
            </div>
            <div class="card-body">
                <form class="form" role="form" method="post" action="{{route('master.location.regency.update', Hashids::encode($regency->id))}}" autocomplete="off">
                    @csrf
                    <input name="_method" type="hidden" value="PUT">
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right"><strong>Province</strong> <label class="text-danger">*</label> :</label>
                        <div class="col-lg-8">
                            <input type="text" value="{{$regency->province->name}}" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">{{trans('label.name')}} <label class="text-danger">*</label> :</label>
                        <div class="col-lg-8">
                            <input name="name" type="text" value="{{old('name') ?? $regency->name}}" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right">Kode :</label>
                        <div class="col-lg-8">
                            <input name="code" type="text" value="{{old('code') ?? $regency->code}}" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right">Latitude :</label>
                        <div class="col-lg-8">
                            <input name="latitude" type="text" value="{{old('latitude') ?? $regency->latitude}}" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right">Longitude :</label>
                        <div class="col-lg-8">
                            <input name="longitude" type="text" value="{{old('longitude') ?? $regency->longitude}}" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="offset-lg-2 col-lg-10">
                            <button class="btn btn-secondary btn-sm" type="submit"><i class="fa fa-check"></i> {{trans('common.save')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection