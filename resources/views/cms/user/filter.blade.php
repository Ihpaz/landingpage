<div class="modal inmodal" id="modal_filter" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <form id="form-filter">
            <div class="modal-content animated fadeIn">
                <div class="modal-header">
                    <h4 class="modal-title">Filter</h4>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">{{trans('common.close')}}</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 pl-3 pr-3">
                            <div class="form-group">
                                <label class="control-label font-bold">Status :</label>
                                <select id="filter_status" name="status" class="select2 form-control d-none">
                                    <option></option>
                                    <option value="ACTV">ACTIVE</option>
                                    <option value="INAC">IN ACTIVE</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label font-bold">Role :</label>
                                <select id="filter_role" name="role" class="select2 form-control d-none">
                                    <option></option>
                                    @foreach($filter['users_role'] as $data)
                                    <option value="{{$data->name}}">{{strtoupper($data->name)}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{trans('common.close')}}</button>
                    <button type="reset" class="btn btn-secondary btn-sm"><i class="fa fa-refresh"></i> {{trans('common.clear')}}</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> {{trans('common.apply')}}</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script type="text/javascript">
    $('#modal_filter').on('show.bs.modal', function(e) {
        $('#filter_status.select2').select2({
            width: '100%',
            placeholder: "{{ trans('common.choose') }}"
        });
        $('#filter_role.select2').select2({
            width: '100%',
            placeholder: "{{ trans('common.choose') }}"
        });
        $('#filter_external.select2').select2({
            width: '100%',
            placeholder: "{{ trans('common.choose') }}"
        });
    });

    $('#form-filter').on('submit', function(e) {
        e.preventDefault();
        datatable.draw();
        $('#modal_filter').modal('hide');
    }).on('reset', function(e) {
        $('.select2').val(null).trigger('change');
    });
</script>
@endpush