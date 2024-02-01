<div class="modal-header">
    <h4 class="modal-title">{{$title}}</h4>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">{{trans('common.close')}}</span></button>
</div>
<form id="form-show">
    @csrf
    <div class="modal-body">
        <div class="row">
            <div class="col-lg-12 pl-3 pr-3">
                @foreach ($module->fields->sortBy('sort') as $data)
                <div class="form-group">
                    <label class="control-label {{$data->required ? 'font-bold' : ''}}">{{$data->label}} {!! $data->required ? '<label class="text-danger">*</label>' : '' !!}:</label>
                    {!! $data->html($field->{$data->colname}, true) !!}
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{trans('common.close')}}</button>
    </div>
</form>