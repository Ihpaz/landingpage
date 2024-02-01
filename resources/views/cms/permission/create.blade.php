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
                <a href="{{route('cms.permission.index')}}">Permissions</a>
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
                <form class="form" role="form" method="post" action="{{route('cms.permission.store')}}" autocomplete="off">
                    @csrf
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">Module <label class="text-danger">*</label> :</label>
                        <div class="col-lg-4">
                            <input name="module" type="text" value="{{old('module')}}" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">Function <label class="text-danger">*</label> :</label>
                        <div class="col-lg-6">
                            <input name="function" type="text" value="{{old('function')}}" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">Action Name <label class="text-danger">*</label> :</label>
                        <div class="col-lg-6">
                            <input name="action" type="text" value="{{old('action')}}" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right">{{trans('label.description')}} <label class="text-danger">*</label> :</label>
                        <div class="col-lg-10">
                            <input name="description" type="text" value="{{old('description')}}" class="form-control" required>
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