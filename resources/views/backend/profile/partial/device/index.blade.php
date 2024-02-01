<div class="p-20">
    <div class="table-responsive">
        <table id="table-login" class="table" style="width:100% !important">
            <thead>
                <th>{{trans('label.name')}}</th>
                <th>{{trans('label.type')}}</th>
                <th>Ip Address</th>
                <th>Last Activity</th>
                <th>Status</th>
                <th>{{trans('common.actions')}}</th>
            </thead>
        </table>
    </div>
</div>

@push('scripts')
<script type="text/javascript">
    var datatable = $('#table-login').DataTable({
        pageLength: 10,
        responsive: true,
        serverSide: true,
        scrollX: true,
        searchDelay: 1000,
        ajax: {
            url: "{{ route('backend.ajax.device.user') }}",
        },
        columns: [
            { data: 'name', name: 'name' },
            { data: 'type', name: 'type' },
            { data: 'ip', name: 'ip' },
            { data: 'time', name: 'last_used' },
            { data: 'status', name: 'is_block', searchable: false, className: 'text-center'},
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center'},
        ],
        order: [3, "desc"],
    });
</script>
@endpush