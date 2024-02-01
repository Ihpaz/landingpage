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
                <a href="{{route('cms.menu.index')}}">Menu Management</a>
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
                <form class="form" action="{{route('cms.menu.update', Hashids::encode($menu->id))}}" method="post" autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    <input name="_method" type="hidden" value="PUT">
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">Type <label class="text-danger">*</label> :</label>
                        <div class="col-lg-8">
                            <input name="type" type="text" value="{{old('name') ?? $menu->type}}" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">{{trans('label.name')}} <label class="text-danger">*</label> :</label>
                        <div class="col-lg-8">
                            <input name="name" type="text" value="{{old('name') ?? $menu->name}}" class="form-control" required>
                        </div>
                    </div>
                    <div id="f-permission" class="form-group row">
                        <label class="col-lg-2 col-form-label text-right">Permission :</label>
                        <div class="col-lg-8">
                            <select id="permission" name="permission" class="select2 form-control">
                                <option></option>
                                @foreach($permission as $data)
                                <option value="{{$data->name}}" {{$menu->permission == $data->name ? 'selected' : ''}}>{{$data->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @if($menu->type != 'separator')
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right">Icon :</label>
                        <div class="col-lg-8">
                            <select id="icon" name="icon" class="select2 form-control">
                                <option></option>
                                @foreach($mdi as $icon)
                                <option value="{{$icon}}" data-icon="{{$icon}}" {{$menu->icon == $icon ? 'selected' : ''}}>{{$icon}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @if($menu->type == 'module')
                    <div id="f-module" class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">Module <label class="text-danger">*</label> :</label>
                        <div class="col-lg-8">
                            <select id="module" name="module" class="select2 form-control" required>
                                @foreach($module as $data)
                                <option value="{{$data->slug}}" {{$menu->url == $data->slug ? 'selected' : ''}}>{{$data->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @else
                    <div id="f-url" class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">Url <label class="text-danger">*</label> :</label>
                        <div class="col-lg-8">
                            <input name="url" type="text" value="{{old('url') ?? $menu->url}}" class="form-control" required>
                        </div>
                    </div>
                    @endif
                    @endif
                    <div class="form-group row pt-2">
                        <div class="offset-lg-2 col-lg-8">
                            <button class="btn btn-secondary btn-sm" type="submit"><i class="fa fa-check"></i> {{trans('common.save')}}</button>
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
    function iformat(icon) {
        var originalOption = icon.element;
        return $('<span><i class="mdi ' + $(originalOption).data('icon') + '"></i> ' + icon.text + '</span>  ');
    }
    $('#module').select2({
        width: '100%',
        placeholder: "{{ trans('common.choose') }}"
    });
    $("#icon").select2({
        width: "100%",
        placeholder: "{{ trans('common.choose') }}",
        templateSelection: iformat,
        templateResult: iformat,
        allowClear: true,
        allowHtml: true
    });
    $('#permission').select2({
        width: '100%',
        allowClear: true,
        placeholder: "{{ trans('common.choose') }}"
    });
</script>
@endpush