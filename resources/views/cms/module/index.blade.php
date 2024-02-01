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
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header">
                <h4 class="m-b-0 pull-left">List Module</h4>
                <div class="pull-right">
                    <a href="{{route('cms.module.create')}}" class="btn btn-xs btn-secondary"><i class="fa fa-plus"></i> {{trans('common.add')}}</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="data-table" class="table" width="100%">
                        <thead>
                            <th>Id</th>
                            <th>{{trans('label.name')}}</th>
                            <th>Table</th>
                            <th>Model</th>
                            <th>Item</th>
                            <th>{{trans('common.actions')}}</th>                            
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.select2-container { 
    z-index: 9999;
}
.detail-info {
    white-space: normal !important;
    width: 150px;
    margin-right: 5px;
    margin-left: 5px;
}
</style>
@endpush

@push('scripts')
<script type="text/javascript">
    $('#data-table').DataTable({
        pageLength: 10,
        responsive: true,
        serverSide: true,
        scrollX: true,
        searchDelay: 1000,
        ajax: "{{ route('cms.datatable.module') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name'},
            { data: 'table', name: 'table', orderable: false},
            { data: 'full_path_model', name: 'model', orderable: false},
            { data: 'count', name: 'count', orderable: false},
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center'}
        ],
        order: [
            [1, 'asc'],
        ]
    });
</script>
@endpush