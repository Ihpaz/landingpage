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
                <label class="mb-0">Tools</label>
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
                <h4 class="m-b-0 pull-left">List Activity Log</h4>
                <div class="pull-right">
                    <button onclick="refresh()" type="button" class="btn btn-xs btn-secondary"><i class="fa fa-refresh"></i> Refresh</button>
                    <button type="button" class="btn btn-xs btn-secondary" data-toggle="modal" data-target="#modal_filter"><i class="fa fa-filter"></i> Filter</button>
                </div>
            </div>
            @include('cms.activity.filter')
            <div class="card-body">
                <div class="input-group input-group-sm pb-2">
                    <input id="data-table-search" type="search" class="form-control" aria-controls="data-table" placeholder="Search log by name...">
                    <div class="input-group-append">
                        <button onclick="search()" class="btn btn-white" type="button" style="width:unset;">{{trans('common.search')}}</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="data-table" class="table issue-tracker">
                        <thead>
                            <th>Time access</th>
                            <th>{{trans('label.type')}}</th>
                            <th>User</th>
                            <th>Ip Address</th>
                            <th>{{trans('label.description')}}</th>
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
.w-10 {
    width: 10%;
}

.detail-info {
    white-space: normal !important;
    max-width: 400px;
    min-width: 50px;
    margin-right: 5px;
    margin-left: 5px;
}
</style>
@endpush

@push('scripts')
<script type="text/javascript">
var datatable = $('#data-table').DataTable({
    initComplete: function() {
        $('#data-table_filter').detach().appendTo('#data-table-search');
    },
    pageLength: 10,
    responsive: true,
    serverSide: true,
    scrollX: true,
    searchDelay: 1000,
    dom: 'frtp',
    order: [0, "desc"],
    ajax: {
        url: "{{ route('cms.datatable.activity') }}",
        data: function(data) {
            data.log_name = $('#filter_status').val();
            data.model = $('#filter_model').val();
            data.causer_name = $('#filter_user').val();
            data.date_start = $('#filter_date_start').val();
            data.date_end = $('#filter_date_end').val();
        }
    },
    columns: [
        { data: 'time', name: 'created_at', className: 'w-10', searchable: false},
        { data: 'type', name: 'log_name' },
        { data: 'user', name: 'user', orderable: false },
        { data: 'ip', name: 'ip', orderable: false, searchable: false},
        { data: 'formated_description', name: 'description', orderable: false, className: 'DT_whitespace'},
        { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center'}
    ],
});

$('#data-table-search').keyup(function(event) {
    if (event.keyCode === 13) {
        event.preventDefault();
        datatable.search($(this).val()).draw();
    }
});

$('#modal_filter').on('show.bs.modal', function(e) {
    $('#filter_status.select2').select2({
        width: '100%',
        placeholder: "{{ trans('common.choose') }}"
    });
    $('#filter_model.select2').select2({
        width: '100%',
        placeholder: "{{ trans('common.choose') }}"
    });
    $('.daterange').datepicker({
        toggleActive: true,
        format: 'dd.mm.yyyy',
        todayHighlight: true,
        autoclose: true
    });
});
$('#form-filter').on('submit', function(e) {
    e.preventDefault();
    datatable.draw();
    $('#modal_filter').modal('hide');
}).on('reset', function(e) {
    $('.select2').val(null).trigger('change');
});

function search() {
    datatable.search($('#data-table-search').val()).draw();
}

function refresh() {
    datatable.search('').draw();
}
</script>
@endpush