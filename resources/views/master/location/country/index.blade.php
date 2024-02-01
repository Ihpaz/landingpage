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
                <label class="mb-0">Master Data</label>
            </li>
            <li class="breadcrumb-item">
                <label class="mb-0">Location</label>
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
                <h4 class="mb-0 pull-left">List Negara</h4>
                <div class="pull-right">
                    <a href="{{route('master.location.country.create')}}" class="btn btn-xs btn-secondary"><i class="fa fa-plus"></i> {{trans('common.add')}}</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">                
                   <table id="data-table" class="table" width="100%">
                        <thead>
                            <th>#</th>
                            <th>ISO Code</th>
                            <th>Alpha-3</th>
                            <th>{{trans('label.name')}}</th>
                            <th>Currency</th>
                            <th>Latitude</th>
                            <th>Longitude</th>
                            <th>{{trans('common.updated_at')}}</th>
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
    margin-bottom: 5px;
}
</style>
@endpush

@push('scripts')
<script type="text/javascript">
var datatable = $('#data-table').DataTable({
    pageLength: 10,
    responsive: true,
    serverSide: true,
    searchDelay: 1000,
    ajax: {
        url: "{{ route('master.datatable.country') }}",
    },
    columns: [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'code', name: 'code' },
        { data: 'alpha_3', name: 'alpha_3' },
        { data: 'name', name: 'name' },
        { data: 'currencies', name: 'currencies' },
        { data: 'latitude', name: 'latitude', searchable: false},
        { data: 'longitude', name: 'longitude', searchable: false},
        { data: 'diperbarui', name: 'updated_at', searchable: false},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center'}
    ],
    order: [[1, 'asc']]
})
</script>
@endpush