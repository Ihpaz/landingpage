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
                <a href="{{route('cms.role.show',Hashids::encode($role->id))}}">{{$title}}</a>
            </li>
            <li class="breadcrumb-item active">
                <a href="{{route('cms.role.edit',Hashids::encode($role->id))}}"><strong>{{trans('common.edit')}}</strong></a>
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
                <form class="form" role="form" method="post" action="{{route('cms.role.update', Hashids::encode($role->id))}}" autocomplete="off">
                    @csrf
                    <input name="_method" type="hidden" value="PUT">
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">{{trans('label.name')}} <label class="text-danger">*</label> :</label>
                        <div class="col-lg-10">
                            <input name="name" type="text" value="{{old('name') ?? $role->name}}" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">{{trans('label.description')}} <label class="text-danger">*</label> :</label>
                        <div class="col-lg-10">
                            <input name="description" type="text" value="{{old('description') ?? $role->description}}" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">Level <label class="text-danger">*</label> :</label>
                        <div class="col-lg-4">
                            <input name="level" type="number" value="{{old('level') ?? $role->level}}" class="form-control" min="1" max="9" required>
                            <small class="text-navy">[1] Superadmin; [2] Pusat; [3] Unit; ext</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">Permissions <label class="text-danger">*</label> :</label>
                        <div class="col-lg-10 pt-2">
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
                                                            <input type="checkbox" id="permission-{{$action->name}}" name="{{$action->name}}" {{in_array($action->name, $role_permission) ? 'checked' : null}}>
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

@push('scripts')
<script type="text/javascript">
$('.i-checks').iCheck({
    checkboxClass: 'icheckbox_square-blue',
});
</script>
@endpush