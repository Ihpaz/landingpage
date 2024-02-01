<div class="modal inmodal" id="modal_document" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="form-document" method="POST" action="{{route('backend.personal.document.store')}}" enctype="multipart/form-data" autocomplete="off">
            <div class="modal-content animated fadeIn">
                <div class="modal-header">
                    <h4 class="modal-title">Add Document</h4>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                </div>
                <div class="modal-body">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12 pl-3 pr-3">
                            <div class="form-group">
                                <label class="control-label font-bold">Type :</label>
                                <select id="document_type" name="type" class="select2 form-control d-none">
                                    <option value="personal">Personal</option>
                                    <option value="work">Work</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label font-bold">File :</label>
                                <div class="custom-file mb-3">
                                    <input name="file" type="file" class="custom-file-input">
                                    <label class="custom-file-label form-control">Choose file</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{trans('common.close')}}</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-check"></i> {{trans('common.save')}}</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script type="text/javascript">
    $('#modal_document').on('shown.bs.modal', function(e) {
        $('#document_type').select2({
            width: '100%'
        });
    });
</script>
@endpush