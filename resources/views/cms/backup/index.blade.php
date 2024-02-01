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
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header">
                <h4 class="m-b-0 pull-left">List Backup</h4>
                <div class="pull-right">
                    @can('cms backup-management create')
                    <button type="button" class="btn btn-xs btn-secondary" data-toggle="modal" data-target="#modalBackup"><i class="fa fa-database"></i> Backup</button>
                    @endcan
                </div>
                @include('cms.backup.create')
            </div>
            <div class="card-body">
                <div class="table-responsive">
                   <table id="data-table" class="table" width="100%">
                        <thead>
                            <th>Path</th>
                            <th>{{trans('common.created_at')}}</th>
                            <th>Size</th>
                            <th>{{trans('common.actions')}}</th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card tabs-container">
            <ul class="nav nav-tabs customtab">
                <li><a class="nav-link active" data-toggle="tab" href="#backup-local"> <i class="fa fa-database"></i> Local Backup</a></li>
            </ul>
            <div class="tab-content shadow">
                <div id="user-information" class="tab-pane active">
                    <div class="p-20">
                        <form class="form">
                            <div class="form-group row mb-0">
                                <label class="col-lg-5 col-form-label text-right">Disk Area :</label>
                                <div class="col-lg-7">
                                    <p class="col-form-label">{{$monitor['disk']}}</p>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <label class="col-lg-5 col-form-label text-right">Healthy :</label>
                                <div class="col-lg-7">
                                    <p class="col-form-label">
                                        <span class="fa fa-{{$monitor['healthy'] ? 'check-square' : 'exclamation-circle'}}"></span>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <label class="col-lg-5 col-form-label text-right">Ammount :</label>
                                <div class="col-lg-7">
                                    <p class="col-form-label">{{$monitor['amount']}}</p>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <label class="col-lg-5 col-form-label text-right">Newest Backup :</label>
                                <div class="col-lg-7">
                                    <p class="col-form-label">{{$monitor['newest']}}</p>
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <label class="col-lg-5 col-form-label text-right">Used Storage :</label>
                                <div class="col-lg-7">
                                    <p class="col-form-label">{{$monitor['usedStorage']}}</p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
$('.i-checks').iCheck({
    radioClass: 'iradio_square-blue',
});
$('#data-table').DataTable({
    pageLength: 10,
    responsive: true,
    serverSide: true,
    scrollX: true,
    searchDelay: 1000,
    ajax: "{{ route('cms.datatable.backup') }}",
    columns: [
        { data: 'path', name: 'path' },
        { data: 'date', name: 'date' },
        { data: 'size', name: 'size' },
        { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center'}
    ],
    order: [[1, "desc"]],
});

function deleteRecord(date, path) {
    $("#modal_delete_title").text("Remove Backup at '" + date + "'");
    $("#modal_delete_body").append('<input type="hidden" name="disk" value="local">');
    $("#modal_delete_body").append('<input type="hidden" name="path" value="' + path + '">');
    $("#modal_delete_form").attr("action", "{{ route('cms.backup.destroy') }}");
    $("#modal_delete").modal("show");
};
</script>
@endpush