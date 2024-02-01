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
            <li class="breadcrumb-item">
                <a href="{{route('cms.permission.show',Hashids::encode($permission->id))}}"><strong>{{$title}}</strong></a>
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
                <form class="form">
                    <div class="form-group row mb-0">
                        <label class="col-lg-2 col-form-label text-right"><strong>Guard</strong> :</label>
                        <div class="col-lg-10">
                            <p class="col-form-label">{{$permission->guard_name}}</span></p>
                        </div>
                    </div>
                    <div class="form-group row mb-0">
                        <label class="col-lg-2 col-form-label text-right"><strong>System Name</strong> :</label>
                        <div class="col-lg-10">
                            <p class="col-form-label"><span class="label label-info mb-0">{{$permission->name}}</span></p>
                        </div>
                    </div>
                    <div class="form-group row mb-0">
                        <label class="col-lg-2 col-form-label text-right font-bold">Module :</label>
                        <div class="col-lg-4">
                            <p class="col-form-label">{{$permission->module}}</p>
                        </div>
                    </div>
                    <div class="form-group row mb-0">
                        <label class="col-lg-2 col-form-label text-right font-bold">Function :</label>
                        <div class="col-lg-10">
                            <p class="col-form-label">{{$permission->function}}</p>
                        </div>
                    </div>
                    <div class="form-group row mb-0">
                        <label class="col-lg-2 col-form-label text-right"><strong>Action</strong> :</label>
                        <div class="col-lg-10">
                            <p class="col-form-label">{{$permission->action}}</p>
                        </div>
                    </div>
                    <div class="form-group row mb-0">
                        <label class="col-lg-2 col-form-label text-right font-bold">{{trans('label.description')}} :</label>
                        <div class="col-lg-10">
                            <p class="col-form-label">{{$permission->description}}</p>
                        </div>
                    </div>
                    @can('cms permission-management update')
                    <div class="form-group row mb-0">
                        <div class="offset-lg-2 col-lg-10">
                            <a href="{{route('cms.permission.edit', Hashids::encode($permission->id))}}" class="btn btn-secondary btn-sm" title="Ubah"><i class="fa fa-pencil"></i> {{trans('common.edit')}}</a>
                        </div>
                    </div>
                    @endcan
                </form>
            </div>
        </div>
    </div>
</div>
@endsection