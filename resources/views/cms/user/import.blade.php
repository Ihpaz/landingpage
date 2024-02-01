<div class="modal inmodal" id="modal_import" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <form id="form-import" action="{{route('cms.user.import.excel')}}" role="form" method="post" autocomplete="off" enctype="multipart/form-data">
            @csrf
            <div class="modal-content animated fadeIn">
                <div class="modal-header">
                    <h4 class="modal-title">Import</h4>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">{{trans('common.close')}}</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group row mb-0">
                                <label class="col-sm-3 col-form-label text-right">File </label>
                                <div class="col-sm-9">
                                    <input type="file" name="dokumen" accept=".xlsx">
                                    <span class="form-text m-b-none text-left">
                                        <small>File yang diupload harus memiliki format (.xlsx) kurang dari 5Mb.</small>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{trans('common.close')}}</button>
                    <a href="{{asset('template/import/users.xlsx')}}" class="btn btn-secondary btn-sm"><i class="fa fa-download"></i> Download Template</a>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-upload"></i> Upload</button>
                </div>
            </div>
        </form>
    </div>
</div>