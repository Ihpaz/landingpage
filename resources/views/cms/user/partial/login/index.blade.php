<div class="p-20 mb-4">
    <div class="table-responsive">
        <table id="table-login" class="table" style="width:100% !important">
            <thead>
                <th>{{trans('label.name')}}</th>
                <th>{{trans('label.type')}}</th>
                <th>Ip Address</th>
                <th>Created</th>
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
            url: "{{ route('backend.ajax.recent.login') }}",
            data: function(data) {
                data.user_id = "{{$user->id}}"
            }
        },
        columns: [
            { data: 'display', name: 'display' },
            { data: 'type', name: 'type' },
            { data: 'ip', name: 'ip' },
            { data: 'time', name: 'created_at' },
        ],
        order: [3, "desc"],
    });
</script>
@endpush