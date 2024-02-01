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
                <h4 class="m-b-0 pull-left">List Roles</h4>
                <div class="pull-right">
                    @can('cms role-management create')
                    <a href="{{route('cms.role.create')}}" class="btn btn-xs btn-secondary"><i class="fa fa-plus"></i> {{trans('common.add')}}</a>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                   <table id="data-table" class="table" width="100%">
                        <thead>
                            <th>Role</th>
                            <th>{{trans('label.description')}}</th>
                            <th>Level</th>
                            <th>Total Users</th>
                            <th>Permissions</th>
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
.detail-info {
    white-space: normal !important;
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
    ajax: "{{ route('cms.datatable.role') }}",
    columns: [
        { data: 'name', name: 'name' },
        { data: 'description', name: 'description' },
        { data: 'level', name: 'level', orderable: false, searchable: false, className: 'text-center'},
        { data: 'count', name: 'users_count', orderable: false, searchable: false, className: 'text-center'},
        { data: 'permissions', name: 'permissions', orderable: false, className: 'DT_whitespace' },
        { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center'}
    ],
});
</script>
@endpush