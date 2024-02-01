<div class="modal inmodal" id="modal_create" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="form-filter" method="post" action="{{route('cms.module.field.store', Hashids::encode($module->id))}}" autocomplete="off">
            @csrf
            <div class="modal-content animated fadeIn">
                <div class="modal-header">
                    <h4 class="modal-title">Add {{$module->name}} Field</h4>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">{{trans('common.close')}}</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 pl-3 pr-3">
                            <div class="form-group">
                                <label class="control-label font-bold">Field Label :</label>
                                <input type="text" name="label" class="form-control" value="{{old('label')}}" placeholder="Field Label" required>
                            </div>
                            <div class="form-group">
                                <label class="control-label font-bold">Column Name :</label>
                                <div class="input-group m-b-0">
                                    <select id="colname" name="colname" class="select2 form-control d-none">
                                        @foreach ($column as $data)
                                        <option value="{{$data}}">{{$data}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label font-bold">UI Style :</label>
                                <div class="input-group m-b-0">
                                    <select id="field_ui" name="field_type_id" class="select2 form-control d-none">
                                        @foreach ($type as $data)
                                        <option value="{{$data->id}}">{{$data->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div id="f-default" class="form-group">
                                <label class="control-label font-bold">Default Value :</label>
                                <input type="text" name="default" class="form-control" value="{{old('default')}}" placeholder="Default Value">
                            </div>
                            <div id="f-minlength" class="form-group">
                                <label class="control-label font-bold">Minimum :</label>
                                <input type="number" name="minlength" class="form-control" value="{{old('minlength')}}" placeholder="Minimum Value">
                            </div>
                            <div id="f-maxlength" class="form-group">
                                <label class="control-label font-bold">Maximum :</label>
                                <input type="number" name="maxlength" class="form-control" value="{{old('maxlength')}}" placeholder="Maximum Value">
                            </div>
                            <div id="f-required" class="form-group" style="display: flex;">
                                <label class="control-label font-bold" style="margin-top: 0.1rem;">Required :</label>
                                <div class="i-checks" style="margin-left: 10px;">
                                    <input type="checkbox" name="required">
                                </div>
                            </div>
                            <div id="f-unique" class="form-group" style="display: flex;">
                                <label class="control-label font-bold">Unique :</label>
                                <div class="i-checks" style="margin-left: 10px;">
                                    <input type="checkbox" name="unique">
                                </div>
                            </div>
                            <div id="f-listing" class="form-group" style="display: flex;">
                                <label class="control-label font-bold">Show in Index Listing : </label>
                                <div class="i-checks" style="margin-left: 10px;">
                                    <input type="checkbox" name="listing_col">
                                </div>
                            </div>
                            <div id="f-values">
                                <div class="form-group">
                                    <label class="control-label font-bold">Values :</label>
                                    <label class="radio-inline i-checks">
                                        <input type="radio" value="table" name="from" checked> From Table
                                        <input type="radio" value="list" name="from"> From List
                                    </label>
                                </div>
                                <div id="i-list" class="form-group">
                                    <input multiple id="values_list" type="text" name="values_list" class="form-control mt-1" data-role="tagsinput" placeholder="Add Multiple Values">
                                </div>
                                <div id="i-table" class="form-group">
                                    <select id="values_table" name="values_table" class="select2 form-control d-none">
                                        <option></option>
                                        @foreach($tables as $data)
                                        <option value="{{$data->tablename}}">{{$data->tablename}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label font-bold">Field Comment :</label>
                                <input type="text" name="comment" class="form-control" value="{{old('comment')}}" placeholder="Field Comment">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{trans('common.close')}}</button>
                    <button type="submit" class="btn btn-secondary btn-sm"><i class="fa fa-check"></i> {{trans('common.save')}}</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script type="text/javascript">
    $('#modal_create').on('show.bs.modal', function(e) {
        $('#colname.select2').select2({
            width: '100%',
            placeholder: "{{ trans('common.choose') }}"
        });
        $('#field_ui.select2').select2({
            width: '100%',
            placeholder: "{{ trans('common.choose') }}"
        }).on('select2:select', function() {
            showValuesSection();
        });

        $('#values_table').select2({
            width: '100%',
            allowClear: true,
            placeholder: "{{ trans('common.choose') }}"
        });
        $('.i-checks').iCheck({
            radioClass: 'iradio_square-blue',
            checkboxClass: 'icheckbox_square-blue'
        });

        $('input[name="from"]').on('ifClicked', function(event) {
            if (this.value == 'list') {
                $('#i-list').show();
                $('#i-table').hide();
            }
            if (this.value == 'table') {
                $('#i-list').hide();
                $('#i-table').show();
            }
        });
    });
    showValuesSection();

    function showValuesSection() {
        var ft_val = $('#field_ui.select2').val();
        $('#f-unique').show();
        $('#f-default').show();
        $('#f-minlength').show();
        $('#f-maxlength').show();
        $('#f-values').hide();
        $('#i-list').hide();

        if ($.inArray(ft_val, ['1', '2', '6', '8', '9', '11', '13', '15', '16']) != -1) {
            $('#f-unique').hide();
        }

        if ($.inArray(ft_val, ['2', '3', '4', '6', '7', '8', '9', '11', '13', '16', '18', '19', '20']) != -1) {
            $('#f-minlength').hide();
            $('#f-maxlength').hide();
        }

        if ($.inArray(ft_val, ['6', '11', '13', '19', '20']) != -1) {
            $('#f-values').show();
        }
    }
</script>
@endpush