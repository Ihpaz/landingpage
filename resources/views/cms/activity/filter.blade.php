<div class="modal inmodal" id="modal_filter" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <form id="form-filter" autocomplete="off">
            <div class="modal-content animated fadeIn">
                <div class="modal-header">
                    <h4 class="modal-title">Filter</h4>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">{{trans('common.close')}}</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 pl-3 pr-3">
                            <div class="form-group">
                                <label class="control-label font-bold">Type :</label>
                                <select id="filter_status" name="status" class="select2 form-control d-none">
                                    <option value="">-- ALL TYPE --</option>
                                    @foreach($filter['type'] as $data)
                                    <option value="{{$data->log_name}}">{{$data->log_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label font-bold">Model :</label>
                                <select id="filter_model" name="model" class="select2 form-control d-none">
                                    <option value="">-- ALL MODEL --</option>
                                    @foreach($filter['model'] as $data)
                                    <option value="{{$data->subject_type}}">{{$data->subject_type}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label font-bold">User :</label>
                                <input id="filter_user" type="text" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="control-label font-bold">Date :</label>
                                <div class="input-group m-b-0">
                                    <div class="input-daterange input-group daterange">
                                        <input type="text" class="form-control" id="filter_date_start" autocomplete="off">
                                        <span class="input-group-addon bg-secondary b-0 pl-2 pr-2 text-white col-form-label">-</span>
                                        <input type="text" class="form-control" id="filter_date_end" autocomplete="off">
                                    </div>
                                </div>
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