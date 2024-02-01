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
                <label class="mb-0">Administrator</label>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('cms.module.index')}}">Module Management</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>{{$title}}</strong>
            </li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card shadow">
            <div class="card-header bg-blue p-15">
            </div>
            <div class="card-body">
                <form class="form" role="form" method="post" action="{{route('cms.module.store')}}" autocomplete="off">
                    @csrf
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">{{trans('label.name')}} <label class="text-danger">*</label> :</label>
                        <div class="col-lg-8">
                            <input name="name" type="text" value="{{old('name')}}" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">Table <label class="text-danger">*</label> :</label>
                        <div class="col-lg-8">
                            <select id="i-table" name="table" class="select2 form-control">
                                <option></option>
                                @foreach($tables as $data)
                                <option value="{{$data->tablename}}">{{$data->tablename}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">Model <label class="text-danger">*</label> :</label>
                        <div class="col-lg-8">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">App/Models/</span>
                                </div>
                                <input name="model" type="text" value="{{old('model')}}" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="offset-lg-2 col-lg-10">
                            <button class="btn btn-secondary btn-sm" onclick="back()">{{(trans('common.back'))}}</button>
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
    $('#i-table').select2({
        width: '100%',
        allowClear: true,
        placeholder: "{{ trans('common.choose') }}"
    });
</script>
@endpush