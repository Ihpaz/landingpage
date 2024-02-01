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
                <a href="{{route('cms.role.index')}}">Roles</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('cms.role.show',Hashids::encode($role->id))}}"><strong>{{$title}}</strong></a>
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
                        <label class="col-lg-2 col-form-label text-right font-bold">Name :</label>
                        <div class="col-lg-10">
                            <p class="col-form-label">{{$role->name}}</p>
                        </div>
                    </div>
                    <div class="form-group row mb-0">
                        <label class="col-lg-2 col-form-label text-right font-bold">{{trans('label.description')}} :</label>
                        <div class="col-lg-10">
                            <p class="col-form-label">{{$role->description}}</p>
                        </div>
                    </div>
                    <div class="form-group row mb-0">
                        <label class="col-lg-2 col-form-label text-right font-bold">Level :</label>
                        <div class="col-lg-10">
                            <p class="col-form-label">{{$role->level}}</p>
                        </div>
                    </div>
                    <div class="form-group row mb-0">
                        <label class="col-lg-2 col-form-label text-right font-bold">Permissions :</label>
                        <div class="col-lg-10">
                            <div class="tabs-container">
                                <ul class="nav nav-tabs customtab" role="tablist">
                                    @foreach($permissions as $data)
                                    <li><a class="nav-link @if($loop->first) active @endif" data-toggle="tab" href="#module-{{$data->module}}">Module {{ucwords(str_replace('-',' ',$data->module))}}</a></li>
                                    @endforeach
                                </ul>
                                <div class="tab-content">
                                    @foreach($permissions as $data)
                                    <div role="tabpanel" id="module-{{$data->module}}" class="tab-pane @if($loop->first) active @endif">
                                        <div class="p-20">
                                            <ul class="mb-0" style="column-count: 2;column-gap: 0;list-style: none;padding-inline-start:0px;">
                                                <li>
                                                    @foreach($data->function as $key => $value)
                                                    @if(auth()->user()->hasAnyPermission(collect($value)->pluck('name')->toArray()) || auth()->user()->hasRole('superadmin'))
                                                    <div class="col-lg-12 pl-0 pb-2" style="display:inline-block">
                                                        <strong>Function {{ucwords(str_replace('-',' ',$key))}}</strong>
                                                        @foreach($value as $action)
                                                        @if(auth()->user()->can($action->name) || auth()->user()->hasRole('superadmin'))
                                                        <div class="i-checks">
                                                            <input type="checkbox" id="permission-{{$action->name}}" name="{{$action->name}}" {{in_array($action->name, $role_permission) ? 'checked' : null}} disabled>
                                                            <label for="permission-{{$action->name}}">
                                                                {{ucwords(str_replace('-',' ',$action->display_name))}}
                                                                <small class="text-navy">{{$action->description}}</small>
                                                            </label>
                                                        </div>
                                                        @endif
                                                        @endforeach
                                                    </div>
                                                    @endif
                                                    @endforeach
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @can('cms role-management update')
                    <div class="form-group row mb-0">
                        <div class="offset-lg-2 col-lg-10">
                            <a href="{{route('cms.role.edit', Hashids::encode($role->id))}}" class="btn btn-secondary btn-sm" title="Ubah"><i class="fa fa-pencil"></i> {{trans('common.edit')}}</a>
                        </div>
                    </div>
                    @endcan
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
$('.i-checks').iCheck({
    checkboxClass: 'icheckbox_square-blue',
});
</script>
@endpush