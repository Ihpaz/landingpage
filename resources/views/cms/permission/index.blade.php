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
            <div class="card-header">
                <h4 class="m-b-0 pull-left">List Permission</h4>
                <div class="pull-right">
                    @can('cms permission-management create')
                    <a href="{{route('cms.permission.create')}}" class="btn btn-xs btn-secondary"><i class="fa fa-plus"></i> {{trans('common.add')}}</a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                   <table id="data-table" class="table" width="100%">
                        <thead>
                            <th>Module</th>
                            <th>Function</th>
                            <th>System Name</th>
                            <th>{{trans('label.description')}}</th>
                            <th>Guard</th>
                            <th>{{trans('common.actions')}}</th>
                        </thead>
                    </table>
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
    ajax: "{{ route('cms.datatable.permission') }}",
    columns: [
        { data: 'module', name: 'module'},
        { data: 'function', name: 'function'},
        { data: 'name', name: 'name', orderable: false},
        { data: 'description', name: 'description', orderable: false},
        { data: 'guard_name', name: 'guard_name', orderable: false},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center'}
    ],
    rowsGroup: [
        'module:name',
        'function:name',
    ],
    order: [
        [0, 'asc'],
        [1, 'asc'],
    ]
});
</script>
@endpush