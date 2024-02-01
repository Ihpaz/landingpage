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
                <h4 class="m-b-0 pull-left">List {{$title}}</h4>
                <div class="pull-right">
                    <button onclick="showModalEditLg('{{route('module.create', $module->slug)}}')" type="button" class="btn btn-xs btn-secondary"><i class="fa fa-plus"></i> {{trans('common.add')}}</button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="data-table" class="table" width="100%">
                        <thead>
                            <th>#</th>
                            @foreach ($module->fields->where('listing_col', true)->sortBy('sort') as $data)
                            <th>{{$data->label}}</th>
                            @endforeach
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
    var datatable = $('#data-table').DataTable({
        pageLength: 10,
        responsive: true,
        serverSide: true,
        scrollX: true,
        searchDelay: 1000,
        ajax: {
            url: "{{ route('module.datatable', $module->slug) }}",
            data: function(data) {

            }                
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            @foreach ($module->fields->where('listing_col', true)->sortBy('sort') as $data)
            { data: '{{$data->colname}}', name: '{{$data->colname}}' },
            @endforeach
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center'}
        ],
        dom: 'Bfrtip',
        buttons: ['excel']
    });
    $('.modal').on('shown.bs.modal', function() {
        $('.i-checks').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
        });
        $('.select2').select2({
            width: '100%',
            placeholder: "{{ trans('common.choose') }}"
        });
        $('.datepicker').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: 'dd.mm.yyyy',
        });
    });
</script>
@endpush