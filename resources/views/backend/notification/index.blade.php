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
        <div class="card tabs-container">
            <ul class="nav nav-tabs customtab">
                <li><a class="nav-link active" data-toggle="tab" href="#user-notification"><i class="fa fa-envelope-o"></i> List Notification</a></li>
            </ul>
            <div class="tab-content shadow">
                <div id="user-notification" class="tab-pane active">
                    <div class="p-20">
                        <div class="input-group input-group-sm pb-2">
                            <input id="data-table-search" type="search" class="form-control" aria-controls="data-table" placeholder="Search notification by name...">
                            <div class="input-group-append">
                                <button onclick="search()" class="btn btn-white" type="button" style="width:unset;">{{trans('common.search')}}</button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table id="data-table" class="table table-hover" style="cursor:pointer">
                                <thead>
                                    <th>From</th>
                                    <th>Subject</th>
                                    <th>Messages</th>
                                    <th>Time</th>
                                </thead>
                            </table>
                        </div>
                    </div>
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

.w-15 {
    width: 15%;
}

.dataTables_wrapper .dataTables_paginate {
    float: unset;
}
</style>
@endpush

@push('scripts')
<script type="text/javascript">
var table = $('#data-table').DataTable({
    pageLength: 10,
    responsive: true,
    serverSide: true,
    scrollX: true,
    searchDelay: 1000,
    dom: 'frtp',
    order: [
        [3, "desc"]
    ],
    ajax: "{{ route('backend.ajax.notification') }}",
    columns: [
        { data: 'from', name: 'from', className: 'w-10'},
        { data: 'subject', name: 'subject', className: 'w-15'},
        { data: 'data.message', name: 'data.message'},
        { data: 'time', name: 'created_at', className: 'issue-info w-10', searchable: false},
    ],
    rowCallback: function(row, data, index) {
        if (!data.read_at) {
            $('td', row).addClass('font-bold');
        }
    },
    initComplete: function() {
        $('#data-table_filter').detach().appendTo('#data-table-search');
    },
});

$('#data-table-search').keyup(function(event) {
    if (event.keyCode === 13) {
        event.preventDefault();
        table.search($(this).val()).draw();
    };
});

function search() {
    table.search($('#data-table-search').val()).draw();
};

$('#data-table tbody').on('click', 'tr', function() {
    var row = table.row(this).data();
    var url = "{{route('backend.api.notification.redirect',':id')}}";
    window.location.replace(url.replace(':id', row.id));
});
</script>
@endpush