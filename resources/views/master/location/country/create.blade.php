@extends('layout.app')

@section('breadcrumb')
<div class="row page-titles">
    <div class="col-lg-5 align-self-center">
        <h3>{{$title}}</h3>
    </div>
    <div class="col-lg-7 align-self-center">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('backend.dashboard.index')}}">Home</a>
            </li>
            <li class="breadcrumb-item">
                <label class="mb-0">Master Data</label>
            </li>
            <li class="breadcrumb-item">
                <label class="mb-0">Location</label>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('master.location.country.index')}}">Negara</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>{{$title}}</strong>
            </li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="row pt-2">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header bg-blue p-15">
            </div>
            <div class="card-body">
                <form class="form" role="form" method="post" action="{{route('master.location.country.store')}}" autocomplete="off">
                    @csrf
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">ISO Code <label class="text-danger">*</label> :</label>
                        <div class="col-lg-8">
                            <input name="code" type="text" value="{{old('code')}}" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">{{trans('label.name')}} <label class="text-danger">*</label> :</label>
                        <div class="col-lg-8">
                            <input name="name" type="text" value="{{old('name')}}" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">Alpha 2 <label class="text-danger">*</label> :</label>
                        <div class="col-lg-8">
                            <input name="alpha_2" type="text" value="{{old('alpha_2')}}" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">Alpha 3 <label class="text-danger">*</label> :</label>
                        <div class="col-lg-8">
                            <input name="alpha_3" type="text" value="{{old('alpha_3')}}" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">Language <label class="text-danger">*</label> :</label>
                        <div class="col-lg-8">
                            <input name="language" type="text" value="{{old('language')}}" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">Currency <label class="text-danger">*</label> :</label>
                        <div class="col-lg-8">
                            <select id="select-currency" name="currency[]" class="select2 form-control" multiple="multiple">
                                @foreach ($currency as $data)
                                <option value="{{$data->id}}">{{$data->code}} ({{strtoupper($data->name)}})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right">Latitude :</label>
                        <div class="col-lg-8">
                            <input name="latitude" type="text" value="{{old('latitude')}}" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right">Longitude :</label>
                        <div class="col-lg-8">
                            <input name="longitude" type="text" value="{{old('longitude')}}" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="offset-lg-2 col-lg-10">
                            <button class="btn btn-secondary btn-sm" type="submit"><i class="fa fa-check"></i> {{trans('common.save')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
    $("#select-currency").select2({
        widht: '100%',
        placeholder: "{{ trans('common.choose') }}"
    });
</script>
@endpush