<div class="modal-header">
    <h4 class="modal-title">Geo Location IP : {{$ip}}</h4>
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
</div>
<div class="modal-body">
    <div class="p-0">
        <div class="form-group row mb-0">
            <label class="col-sm-3 col-form-label text-right">{{trans('location.country')}} :</label>
            <div class="col-sm-9">
                <p class="col-form-label">{{$countryName}}</p>
            </div>
        </div>
        <div class="form-group row mb-0">
            <label class="col-sm-3 col-form-label text-right">Contry Code :</label>
            <div class="col-sm-9">
                <p class="col-form-label">{{$countryCode}}</p>
            </div>
        </div>
        <div class="form-group row mb-0">
            <label class="col-sm-3 col-form-label text-right">Region Code :</label>
            <div class="col-sm-9">
                <p class="col-form-label">{{$regionCode}}</p>
            </div>
        </div>
        <div class="form-group row mb-0">
            <label class="col-sm-3 col-form-label text-right">Region :</label>
            <div class="col-sm-9">
                <p class="col-form-label">{{$regionName}}</p>
            </div>
        </div>
        <div class="form-group row mb-0">
            <label class="col-sm-3 col-form-label text-right">City :</label>
            <div class="col-sm-9">
                <p class="col-form-label">{{$cityName}}</p>
            </div>
        </div>
        <div class="form-group row mb-0">
            <label class="col-sm-3 col-form-label text-right">ZIP Code :</label>
            <div class="col-sm-9">
                <p class="col-form-label">{{$zipCode}}</p>
            </div>
        </div>
        <div class="form-group row mb-0">
            <label class="col-sm-3 col-form-label text-right">ISO Code :</label>
            <div class="col-sm-9">
                <p class="col-form-label">{{$isoCode}}</p>
            </div>
        </div>
        <div class="form-group row mb-0">
            <label class="col-sm-3 col-form-label text-right">Timezone :</label>
            <div class="col-sm-9">
                <p class="col-form-label">{{$timezone}}</p>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{{trans('common.cancel')}}</button>
</div>