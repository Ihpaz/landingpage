<div class="modal inmodal" id="modalBackup" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;overflow:hidden;">
    <div class="modal-dialog">
        <form class="form" action="{{route('cms.backup.store')}}" method="post">
            @csrf
            <div class="modal-content animated fadeIn">
                <div class="modal-header">
                    <h4 class="modal-title">Backup</h4>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">{{trans('common.close')}}</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label text-right"><strong>Option</strong> :</label>
                                <div class="col-sm-9">
                                    <div class="mb-0">
                                        <div class="i-checks"><label> <input type="radio" value="only-db" name="option" checked> <i></i> Database backup </label></div>
                                        <div class="i-checks"><label> <input type="radio" value="only-storage" name="option"> <i></i> Storage backup </label></div>
                                        <div class="i-checks"><label> <input type="radio" value="only-files" name="option"> <i></i> Files backup </label></div>
                                        <div class="i-checks"><label> <input type="radio" value="" name="option"> <i></i> Full backup </label></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{trans('common.close')}}</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-cloud-upload"></i> Backup</button>
                </div>
            </div>
        </form>
    </div>
</div>