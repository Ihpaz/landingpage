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
                <a href="{{route('cms.module.index')}}">Module Management</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('cms.module.edit', Hashids::encode($module->id))}}">{{$module->name}}</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>{{$title}}</strong>
            </li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <ul class="nav nav-tabs customtab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#module-fields" role="tab" aria-selected="true">Module Fields</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#sort" role="tab" aria-selected="false">Sort</a>
                </li>
                <li class="nav-item pull-right">
                    <button type="button" class="btn btn-xs btn-secondary" data-toggle="modal" data-target="#modal_create" style="margin: 12px;"><i class="fa fa-plus"></i> Add Field</button>
                </li>
            </ul>
            @include('cms.module.field.create')
            <div class="tab-content">
                <div class="tab-pane active" id="module-fields" role="tabpanel">
                    <div class="p-20 mb-4">
                        <div class="table-responsive">
                            <table id="data-table" class="table" width="100%">
                                <thead>
                                    <th>#</th>
                                    <th>Label</th>
                                    <th>Column</th>
                                    <th>Type</th>
                                    <th>Unique</th>
                                    <th>Default</th>
                                    <th>Min</th>
                                    <th>Max</th>
                                    <th>Required</th>
                                    <th>Listing</th>
                                    <th>Values</th>
                                    <th>Comment</th>
                                    <th>{{trans('common.actions')}}</th>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="sort" role="tabpanel">
                    <div class="p-20 mb-4">
                        <div class="col-lg-6">
                            <div class="myadmin-dd dd" id="nestable">
                                <ol class="dd-list">
                                    @foreach ($field as $menu)
                                    <li class="dd-item" data-id="{{$menu->id}}">
                                        <div class="dd-handle">
                                            <div class="{{$menu->required ? 'font-bold' : ''}}">
                                                @if(in_array($menu->field_type_id, ['2','19']))
                                                <i class="mdi mdi-check-circle-outline"></i>
                                                @elseif(in_array($menu->field_type_id, ['3','4']))
                                                <i class="mdi mdi-calendar"></i>
                                                @elseif(in_array($menu->field_type_id, ['5','10']))
                                                <i class="mdi mdi-numeric"></i>
                                                @elseif(in_array($menu->field_type_id, ['7']))
                                                <i class="mdi mdi-email-outline"></i>
                                                @elseif(in_array($menu->field_type_id, ['8','9','18']))
                                                <i class="mdi mdi-file-outline"></i>
                                                @elseif(in_array($menu->field_type_id, ['13']))
                                                <i class="mdi mdi-radiobox-marked"></i>
                                                @elseif(in_array($menu->field_type_id, ['21']))
                                                <i class="mdi mdi-key-variant"></i>
                                                @else
                                                <i class="mdi mdi-format-text"></i>
                                                @endif
                                                &nbsp; {{$menu->label}}
                                            </div>
                                        </div>
                                    </li>
                                    @endforeach
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    $('#data-table').DataTable({
        pageLength: 10,
        responsive: true,
        serverSide: true,
        scrollX: true,
        searchDelay: 1000,
        ajax: {
            url: "{{ route('cms.datatable.module.field') }}",
            data: function(data) {
                data.module_id = "{{$module->id}}";
            }
        },
        columns: [{
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            {
                data: 'label',
                name: 'label'
            },
            {
                data: 'colname',
                name: 'colname'
            },
            {
                data: 'field.name',
                name: 'field.name'
            },
            {
                data: 'unique',
                name: 'unique'
            },
            {
                data: 'default',
                name: 'default'
            },
            {
                data: 'minlength',
                name: 'minlength'
            },
            {
                data: 'maxlength',
                name: 'maxlength'
            },
            {
                data: 'required',
                name: 'required'
            },
            {
                data: 'listing',
                name: 'listing_col'
            },
            {
                data: 'popup_vals',
                name: 'popup_vals'
            },
            {
                data: 'comment',
                name: 'comment'
            },
            {
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false,
                className: 'text-center'
            }
        ],
        order: [
            [1, 'asc'],
        ]
    });
    function savemenu() {
        var data = $('.dd').nestable('serialize');
        var jsonString = JSON.stringify(data, null, ' ');
        $.post("{{route('cms.api.module.update.order')}}", {
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
    $('.dd').on('change', function() {
        savemenu();
    });
    $('#nestable').nestable({
        group: 1,
        maxDepth: 1,
    }).nestable('collapseAll');
</script>
@endpush