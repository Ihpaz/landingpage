<div class="p-20 mb-4">
    <div class="table-responsive">
        <table id="table-email" class="table" style="width:100% !important">
            <thead>
                <th>{{trans('label.type')}}</th>
                <th>Email</th>
                <th>Status</th>
            </thead>
        </table>
    </div>
</div>
<hr class="mb-0">
<div class="p-20 mb-4">
    <div class="table-responsive">
        <table id="table-address" class="table" style="width:100% !important">
            <thead>
                <th>{{trans('location.country')}}</th>
                <th>{{trans('location.province')}}</th>
                <th>{{trans('location.regency')}}</th>
                <th>{{trans('location.address')}}</th>
            </thead>
        </table>
    </div>
</div>

@push('scripts')
<script type="text/javascript">
    var datatable = $('#table-email').DataTable({
        pageLength: 10,
        responsive: true,
        serverSide: true,
        scrollX: true,
        searchDelay: 1000,
        ajax: {
            url: "{{ route('backend.ajax.personal.email') }}",
            data: function(data) {
                data.user_id = "{{$user->id}}";
            }
        },
        columns: [
            { data: 'type', name: 'type' },
            { data: 'email', name: 'email' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
        ]
    });

    var datatable = $('#table-address').DataTable({
        pageLength: 10,
        responsive: true,
        serverSide: true,
        scrollX: true,
        searchDelay: 1000,
        ajax: {
            url: "{{ route('backend.ajax.personal.address') }}",
            data: function(data) {
                data.user_id = "{{$user->id}}";
            }
        },
        columns: [
            { data: 'country.name', name: 'country_id' },
            { data: 'province.name', name: 'province_id' },
            { data: 'regency.name', name: 'regency_id' },
            { data: 'address', name: 'address', orderable: false, searchable: false },
        ]
    });
</script>
@endpush