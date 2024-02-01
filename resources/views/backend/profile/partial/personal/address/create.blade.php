<div class="modal inmodal" id="modal_address" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="form-email" method="POST" action="{{route('backend.personal.address.store')}}" autocomplete="off">
            <div class="modal-content animated fadeIn">
                <div class="modal-header">
                    <h4 class="modal-title">Add Address</h4>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                </div>
                <div class="modal-body">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12 pl-3 pr-3">
                            <div class="form-group">
                                <label class="control-label font-bold">{{trans('location.country')}} :</label>
                                <select id="filter_country" name="country_id" class="select2 form-control d-none" required>
                                    <option></option>
                                    @foreach ($country as $data)
                                    <option value="{{$data->id}}">{{$data->name}} - ({{$data->code}})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label font-bold">{{trans('location.province')}} :</label>
                                <select id="filter_province" name="province_id" class="select2 form-control d-none">
                                    <option></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label font-bold">{{trans('location.regency')}} :</label>
                                <select id="filter_regency" name="regency_id" class="select2 form-control d-none">
                                    <option></option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label font-bold">{{trans('location.address')}} :</label>
                                <textarea id="address" name="address" class="form-control" style="max-height:unset" rows="3" required></textarea>
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
    $('#modal_address').on('shown.bs.modal', function(e) {
        $('#filter_country').select2({
            width: '100%',
            placeholder: 'Select Country'
        }).on('select2:select', function() {
            $('#filter_province').val(null);
            $.ajax({
                type: "GET",
                url: "{{route('master.api.province')}}",
                data: {
                    country_id: this.value
                },
                dataType: "json",
                success: function(response) {
                    $('#filter_province').empty().append('<option></option>');
                    $.each(response.data, function(i, value) {
                        $('#filter_province').append('<option value="' + value.id + '"> ' + value.name + '</option>')
                    });
                },
                async: true
            });
        });
        $('#filter_province').select2({
            width: '100%',
            placeholder: 'Select Province',
        }).on('select2:select', function() {
            $('#filter_regency').val(null);
            $.ajax({
                type: "GET",
                url: "{{route('master.api.regency')}}",
                data: {
                    province_id: this.value
                },
                dataType: "json",
                success: function(response) {
                    $('#filter_regency').empty().append('<option></option>');
                    $.each(response.data, function(i, value) {
                        $('#filter_regency').append('<option value="' + value.id + '"> ' + value.name + '</option>')
                    });
                },
                async: true
            });
        });

        $('#filter_regency').select2({
            width: '100%',
            placeholder: 'Select Regency',
        });
    });
</script>
@endpush