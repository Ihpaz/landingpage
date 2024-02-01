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
<div class="row pt-2 mb-2">
    <div class="offset-md-6 col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body pt-1 pb-1">
                <div class="row p-t-10 p-b-10">
                    @php
                    $active = $users_status['ACTV'] ?? 0;
                    $user_sumary = $active_user ? round(($active_user / $total_users) * 10) * 10 : 0;
                    @endphp
                    <div class="col p-r-0">
                        <h1 class="font-light">{{number_format($total_users,0,',','.')}}</h1>
                        <h6 class="text-muted">Total Users</h6>
                    </div>
                    <div class="col text-right align-self-center">
                        <div class="css-bar m-b-0 css-bar-primary css-bar-{{$user_sumary}}"><i class="mdi mdi-account-circle"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body pt-1 pb-1">
                <div class="row p-t-10 p-b-10">
                    <div class="col p-r-0">
                        <h1 class="font-light">{{number_format($new_users,0,',','.')}}</h1>
                        <h6 class="text-muted">New Users</h6>
                    </div>
                    <div class="col text-right align-self-center">
                        <div class="css-bar m-b-0 css-bar-info"><i class="mdi mdi-human-greeting"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header">
                <h4 class="m-b-0 pull-left">List User</h4>
                <div class="pull-right">
                    @can('cms user-management create')
                    <a href="{{route('cms.user.create')}}" class="btn btn-xs btn-secondary"><i class="fa fa-plus"></i> {{trans('common.add')}}</a>
                    <button type="button" class="btn btn-xs btn-secondary" data-toggle="modal" data-target="#modal_import"><i class="fa fa-upload"></i> Import</button>
                    @endcan
                    <button type="button" class="btn btn-xs btn-secondary" data-toggle="modal" data-target="#modal_filter"><i class="fa fa-filter"></i> Filter</button>
                </div>
            </div>
            @include('cms.user.filter')
            @include('cms.user.import')
            <div class="card-body">
                <div class="table-responsive">
                    <table id="data-table" class="table" width="100%">
                        <thead>
                            <th>{{trans('label.name')}}</th>
                            <th>Email</th>
                            <th>Company</th>
                            <th>Title</th>
                            <th>Role</th>
                            <th>Status</th>
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
var datatable = $('#data-table').DataTable({
    pageLength: 10,
    responsive: true,
    serverSide: true,
    scrollX: true,
    searchDelay: 1000,
    ajax: {
        url: "{{ route('cms.datatable.user') }}",
        data: function(data) {
            data.status = $('#filter_status').val();
            data.role = $('#filter_role').val();
        }
    },
    columns: [
        { data: 'display_name', name: 'fullname' },
        { data: 'email', name: 'email' },
        { data: 'company', name: 'company' },
        { data: 'position', name: 'position' },
        { data: 'role', name: 'role', className: 'DT_whitespace'},
        { data: 'status', name: 'status' },
        { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center'}
    ],
    dom: 'Bfrtip',
    buttons: ['excel']
});
</script>
@endpush