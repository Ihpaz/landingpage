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
    <div class="col-lg-3">
        <div class="card">
            <div class="card-body pt-1 pb-1">
                <div class="row p-t-10 p-b-10">
                    @php
                    $queue_summary = $queue ? round(($queue_attempts / $queue) * 10) * 10 : 0;
                    @endphp
                    <div class="col p-r-0">
                        <h1 class="font-light">{{number_format($queue,0,',','.')}}</h1>
                        <h6 class="text-muted">Total Jobs Queue</h6>
                    </div>
                    <div class="col text-right align-self-center">
                        <div class="css-bar m-b-0 css-bar-info css-bar-{{$queue_summary}}"><i class="mdi mdi-access-point-network"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body pt-1 pb-1">
                <div class="row p-t-10 p-b-10">
                    <div class="col p-r-0">
                        <h1 class="font-light">{{number_format($failed,0,',','.')}}</h1>
                        <h6 class="text-muted">Total Jobs Failed</h6>
                    </div>
                    <div class="col text-right align-self-center">
                        <div class="css-bar m-b-0 css-bar-danger css-bar-{{$failed ? '100' : '0'}}"><i class="mdi mdi-close-network"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        <div class="card tabs-container">
            <ul class="nav nav-tabs customtab">
                <li><a class="nav-link active" data-toggle="tab" href="#task-scheduler"> <i class="fa fa-tasks"></i> Task Scheduler @if(config('queue.scheduler'))<span class="label label-info">Scheduller Active</span> @else<span class="label label-warning">Scheduller Inactive </span>@endif</a></li>
                <li><a class="nav-link" data-toggle="tab" href="#jobs-failed"> <i class="fa fa-warning"></i> Jobs Failed</a></li>
            </ul>
            <div class="tab-content">
                <div id="task-scheduler" class="tab-pane active">
                    <div class="p-20 mb-4">
                        <div class="table-responsive">
                           <table id="data-table" class="table" width="100%">
                                <thead>
                                    <th>{{trans('label.type')}}</th>
                                    <th>{{trans('label.description')}}</th>
                                    <th>Interval</th>
                                    <th>Next Execution</th>
                                    <th>{{trans('common.actions')}}</th>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="jobs-failed" class="tab-pane">
                    <div class="p-20 mb-4">
                        <div class="table-responsive">
                            <table id="jobs-failed-table" class="table">
                                <thead>
                                    <th>Connection</th>
                                    <th>Payload</th>
                                    <th>Exceptions</th>
                                    <th>Failed At</th>
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
.detail-info {
    white-space: normal !important;
    max-width: 250px;
    min-width: 50px;
    margin-right: 5px;
    margin-left: 5px;
}
</style>
@endpush

@push('scripts')
<script type="text/javascript">
$('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
    $($.fn.dataTable.tables(true)).DataTable()
        .columns.adjust();
});

$('#data-table').DataTable({
    pageLength: 10,
    responsive: true,
    serverSide: true,
    scrollX: true,
    searchDelay: 1000,
    ajax: "{{ route('cms.datatable.scheduler') }}",
    columns: [
        { data: 'formated_task', name: 'task.name' },
        { data: 'formated_description', name: 'description' },
        { data: 'interval', name: 'interval', orderable: false, searchable: false },
        { data: 'nextRunDate', name: 'nextRunDate' },
        { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center'}
    ],
});

function runTask(id, name) {
    Swal.fire({
        title: 'Run Scheduler!',
        text: 'Do you want to continue run ' + name + ' manualy.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'No'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("{{route('cms.api.scheduler.run')}}", {
                    _token: "{{csrf_token()}}",
                    id: id,
                    name: name
                })
                .done(function(data) {
                    $.toast({
                        heading: 'Task scheduling success running!',
                        text: data.response,
                        position: 'top-right',
                        loaderBg: '#ff6849',
                        icon: 'info',
                    });
                })
                .fail(function(data) {
                    $.toast({
                        heading: 'Error',
                        text: 'Error while update run task scheduling!' + data.response,
                        position: 'top-right',
                        loaderBg: '#ff6849',
                        icon: 'error',
                    });
                });
        }
    });
};

$('#jobs-failed-table').DataTable({
    pageLength: 10,
    responsive: true,
    serverSide: true,
    scrollX: true,
    searchDelay: 1000,
    ajax: "{{ route('cms.datatable.jobs.failed') }}",
    columns: [
        { data: 'connection', name: 'connection' },
        { data: 'formated_payload', name: 'payload', className: 'DT_whitespace', orderable: false, searchable: false },
        { data: 'formated_exception', name: 'exception', className: 'DT_whitespace', orderable: false, searchable: false },
        { data: 'failed_at', name: 'failed_at' },
    ],
});
</script>
@endpush