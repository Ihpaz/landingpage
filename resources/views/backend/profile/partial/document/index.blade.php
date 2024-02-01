<div class="p-20 mb-4">
    <div class="mb-2">
        <button type="button" class="btn btn-xs btn-secondary" data-toggle="modal" data-target="#modal_document"><i class="fa fa-plus"></i> Add Document</button>
    </div>
    @include('backend.profile.partial.document.create')
    <div class="table-responsive">
        <table id="table-document" class="table" style="width:100% !important">
            <thead>
                <th>Type</th>
                <th>Filename</th>
                <th>Size</th>
                <th>{{trans('common.created_at')}}</th>
                <th>{{trans('common.actions')}}</th>
            </thead>
        </table>
    </div>
</div>

@push('scripts')
<script type="text/javascript">
    var datatable = $('#table-document').DataTable({
        pageLength: 10,
        responsive: true,
        serverSide: true,
        scrollX: true,
        searchDelay: 1000,
        ajax: {
            url: "{{ route('backend.ajax.personal.document') }}",
            data: function(data) {
                data.user_id = "{{auth()->user()->id}}";
            }
        },
        columns: [
            { data: 'module', name: 'module' },
            { data: 'filename', name: 'filename' },
            { data: 'size', name: 'size', orderable: false, searchable: false },
            { data: 'diperbarui', name: 'updated_at', searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-center'},
        ],
        order: [3, "desc"],
    });
</script>
@endpush