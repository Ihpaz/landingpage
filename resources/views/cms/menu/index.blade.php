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
            <li class="breadcrumb-item active">
                <strong>{{$title}}</strong>
            </li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="row pt-2">
    <div class="col-lg-6">
        <div class="card shadow">
            <div class="card-header">
                <h4 class="m-b-0 pull-left">List Menu</h4>
                <div class="pull-right">
                    <button type="button" class="btn btn-xs btn-secondary" onclick="savemenu()"><i class="fa fa-save"></i> Save Order</button>
                </div>
            </div>
            <div class="card-body">
                <div class="myadmin-dd dd" id="nestable">
                    <ol class="dd-list">
                        @foreach ($menus as $menu)
                        <li class="dd-item" data-id="{{$menu->id}}">
                            <div class="dd-handle">
                                @if ($menu->type == 'separator')
                                <strong>/ {{$menu->name}} /</strong>
                                @else
                                <i class="mdi {{$menu->icon}}" style="margin-right:5px"></i>
                                {{$menu->name}}
                                @endif
                                <span class="pull-right">
                                    <a class='fa fa-pencil text-warning' title='{{trans('common.edit')}}' href="{{route('cms.menu.edit', Hashids::encode($menu->id))}}"></a>&nbsp;&nbsp;
                                    <a class='fa fa-trash text-danger' title="{{trans('common.delete')}}" data-toggle="modal" style="cursor:pointer" onclick="showModalDelete('{{$menu->name}}','{{route('cms.menu.destroy', Hashids::encode($menu->id))}}')" title="Hapus"></a>
                                </span>
                            </div>
                            @if(count($menu->children))
                            <ol class="dd-list">
                                @foreach ($menu->children as $child)
                                <li class="dd-item" data-id="{{$child->id}}">
                                    <div class="dd-handle">
                                        {{$child->name}}
                                        <span class="pull-right">
                                            <a class='fa fa-pencil text-warning' title='{{trans('common.edit')}}' href="{{route('cms.menu.edit', Hashids::encode($child->id))}}"></a>&nbsp;&nbsp;
                                            <a class='fa fa-trash text-danger' title="{{trans('common.delete')}}" data-toggle="modal" style="cursor:pointer" onclick="showModalDelete('{{$menu->name}}','{{route('cms.menu.destroy', Hashids::encode($child->id))}}')" title="Hapus"></a>
                                        </span>
                                    </div>
                                    @if(count($child->children))
                                    <ol class="dd-list">
                                        @foreach ($child->children as $grand)
                                        <li class="dd-item" data-id="{{$grand->id}}">
                                            <div class="dd-handle">
                                                {{$grand->name}}
                                                <span class="pull-right">
                                                    <a class='fa fa-pencil text-warning' title='{{trans('common.edit')}}' href="{{route('cms.menu.edit', Hashids::encode($grand->id))}}"></a>&nbsp;&nbsp;
                                                    <a class='fa fa-trash text-danger' title="{{trans('common.delete')}}" data-toggle="modal" style="cursor:pointer" onclick="showModalDelete('{{$menu->name}}','{{route('cms.menu.destroy', Hashids::encode($grand->id))}}')" title="Hapus"></a>
                                                </span>
                                            </div>
                                        </li>
                                        @endforeach
                                    </ol>
                                    @endif
                                </li>
                                @endforeach
                            </ol>
                            @endif
                        </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow">
            <div class="card-header">
                <h4 class="m-b-0 pull-left">Add Menu</h4>

            </div>
            <div class="card-body">
                <form class="form" action="{{route('cms.menu.store')}}" method="post" autocomplete="off" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">Type <label class="text-danger">*</label> :</label>
                        <div class="col-lg-10">
                            <select id="type" name="type" class="select2 form-control" required>
                                <option value="url">Url</option>
                                <option value="module">Module</option>
                                <option value="route">Route</option>
                                <option value="separator">Separator</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">{{trans('label.name')}} <label class="text-danger">*</label> :</label>
                        <div class="col-lg-10">
                            <input name="name" type="text" value="{{old('name')}}" class="form-control" required>
                        </div>
                    </div>
                    <div id="f-icon" class="form-group row">
                        <label class="col-lg-2 col-form-label text-right">Icon :</label>
                        <div class="col-lg-10">
                            <select id="icon" name="icon" class="select2 form-control">
                                <option></option>
                                @foreach($mdi as $icon)
                                    <option value="{{$icon}}" data-icon="{{$icon}}">{{$icon}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div id="f-permission" class="form-group row">
                        <label class="col-lg-2 col-form-label text-right">Permission :</label>
                        <div class="col-lg-10">
                            <select id="permission" name="permission" class="select2 form-control">
                                <option></option>
                                @foreach($permission as $data)
                                    <option value="{{$data->name}}">{{$data->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div id="f-module" class="form-group row">
                        <label id="l-module" class="col-lg-2 col-form-label text-right font-bold">Module <label class="text-danger">*</label> :</label>
                        <div class="col-lg-10">
                            <select id="module" name="module" class="select2 form-control" required>
                                <option></option>
                                @foreach($module as $data)
                                    <option value="{{$data->slug}}">{{$data->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div id="f-url" class="form-group row">
                        <label id="l-url" class="col-lg-2 col-form-label text-right font-bold">Url <label class="text-danger">*</label> :</label>
                        <div class="col-lg-10">
                            <input id="url" name="url" type="text" value="{{old('url')}}" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row pt-2">
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

@push('scripts')
<script type="text/javascript">
    function savemenu() {
        var data = $('.dd').nestable('serialize');
        var jsonString = JSON.stringify(data, null, ' ');
        $.post("{{route('cms.api.menu.update.order')}}", {
                _token: "{{csrf_token()}}",
                menus: jsonString
            },
            function(data, status, xhr) {
                if (xhr.status === 200) {
                    $.toast({
                        heading: 'Success!',
                        text: 'Menu order has been updated!',
                        position: 'top-right',
                        loaderBg: '#ff6849',
                        icon: 'info',
                    });
                } else {
                    $.toast({
                        heading: 'Error',
                        text: 'Error while update menu order!',
                        position: 'top-right',
                        loaderBg: '#ff6849',
                        icon: 'error',
                    });
                }
            }
        );
    }

    $('.dd a').on('mousedown', function(event) {
        event.preventDefault();
        return false;
    });

    $('#nestable').nestable({
        group: 1,
        maxDepth: 3,
    }).nestable('collapseAll');
    $('#permission').select2({
        width: '100%',
        allowClear: true,
        placeholder: "{{ trans('common.choose') }}"
    });
    $('#module').select2({
        width: '100%',
        placeholder: "{{ trans('common.choose') }}"
    });
    $('#f-module').hide();
    $("#type").select2({
        width: '100%',
        placeholder: "{{ trans('common.choose') }}"
    }).on('select2:select', function(e) {
        var type = $(this).val();
        if(type == 'separator') {
            $('#f-url').hide();
            $('#f-icon').hide();
            $('#f-module').hide();
            $('#url').removeAttr('required');
            $('#module').removeAttr('required');
        } else if(type == 'module') {
            $('#f-module').show();
            $('#f-url').hide();
            $('#module').attr('required');
            $('#url').removeAttr('required');
        } else {
            $('#f-url').show();
            $('#f-icon').show();
            $('#f-module').hide();
            $('#module').removeAttr('required');
            $('#url').attr('required');
        }
        if(type == 'route') {
            $('#l-url').text('Route Name ').append('<label class="text-danger">*</label> :');
        } else {
            $('#l-url').text('Url ').append('<label class="text-danger">*</label> :')

        }
    });

    function iformat(icon) {
        var originalOption = icon.element;
        return $('<span><i class="mdi ' + $(originalOption).data('icon') + '"></i> ' + icon.text + '</span>  ');
    }
    $("#icon").select2({
        width: "100%",
        placeholder: "{{ trans('common.choose') }}",
        templateSelection: iformat,
        templateResult: iformat,
        allowHtml: true,
        allowClear: true,
    });

</script>
@endpush