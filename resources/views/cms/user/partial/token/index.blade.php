<div class="p-20 mb-4">
    <div class="table-responsive">
        <table id="table-token" class="table" style="width:100% !important">
            <thead>
                <th>Client</th>
                <th>Key</th>
                <th>{{trans('common.created_at')}}</th>
                <th>{{trans('common.expired_at')}}</th>
                <th>Status</th>
            </thead>
        </table>
    </div>
</div>

@push('scripts')
<script type="text/javascript">
    var datatable = $('#table-token').DataTable({
        pageLength: 10,
        responsive: true,
        serverSide: true,
        scrollX: true,
        searchDelay: 1000,
        ajax: {
            url: "{{ route('backend.ajax.access.token') }}",
            data: function(data) {
                data.user_id = "{{$user->id}}";
            }
        },
        columns: [
            { data: 'client', name: 'client_id' },
            { data: 'id', name: 'id' },
            { data: 'created_at', name: 'created_at' },
            { data: 'expires_at', name: 'expires_at' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
        ],
        order: [2, "desc"],
    });
</script>
@endpush