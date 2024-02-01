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
                <a href="{{route('cms.permission.show',Hashids::encode($permission->id))}}">{{$title}}</a>
            </li>
            <li class="breadcrumb-item active">
                <a href="{{route('cms.permission.edit',Hashids::encode($permission->id))}}"><strong>{{trans('common.edit')}}</strong></a>
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
                <form class="form" role="form" method="post" action="{{route('cms.permission.update', Hashids::encode($permission->id))}}" autocomplete="off">
                    @csrf
                    <input name="_method" type="hidden" value="PUT">
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">Module <label class="text-danger">*</label> :</label>
                        <div class="col-lg-4">
                            <input name="module" type="text" value="{{$permission->module}}" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">Function <label class="text-danger">*</label> :</label>
                        <div class="col-lg-4">
                            <input name="function" type="text" value="{{$permission->function}}" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">Action Name <label class="text-danger">*</label> :</label>
                        <div class="col-lg-10">
                            <input name="action" type="text" value="{{old('action') ?? $permission->action}}" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">{{trans('label.description')}} <label class="text-danger">*</label> :</label>
                        <div class="col-lg-10">
                            <input name="description" type="text" value="{{old('description') ?? $permission->description}}" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="offset-lg-2 col-lg-10">
                            <button class="btn btn-secondary btn-sm" type="submit" title="{{trans('common.save')}}"><i class="fa fa-check"></i> {{trans('common.save')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection