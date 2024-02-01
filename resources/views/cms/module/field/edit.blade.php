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
            <li class="breadcrumb-item">
                <a href="{{route('cms.module.edit', Hashids::encode($field->module_id))}}">{{$field->module->name}}</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('cms.module.field.index', Hashids::encode($field->module_id))}}">Module</a>
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
                <form id="form-filter" method="post" action="{{route('cms.module.field.update', Hashids::encode($field->id))}}" autocomplete="off">
                    @csrf
                    <input name="_method" type="hidden" value="PUT">
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">Field Label :</label>
                        <div class="col-lg-8">
                            <input type="text" name="label" class="form-control" value="{{old('label') ?? $field->label}}" placeholder="Field Label">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">Column Name :</label>
                        <div class="col-lg-8">
                            <select id="colname" name="colname" class="select2 form-control d-none">
                                @foreach ($column as $data)
                                <option value="{{$data}}" {{$field->colname == $data ? 'selected' : ''}}>{{$data}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">UI Style :</label>
                        <div class="col-lg-8">
                            <input type="text" name="field_ui" class="form-control" value="{{$field->field->name}}" disabled>
                        </div>
                    </div>
                    <div id="f-default" class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">Default Value :</label>
                        <div class="col-lg-8">
                            <input type="text" name="default" class="form-control" value="{{old('default') ?? $field->default}}" placeholder="Default Value">
                        </div>
                    </div>
                    <div id="f-minlength" class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">Minimum :</label>
                        <div class="col-lg-8">
                            <input type="number" name="minlength" class="form-control" value="{{old('minlength') ?? $field->minlength}}" placeholder="Minimum Value">
                        </div>
                    </div>
                    <div id="f-maxlength" class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">Maximum :</label>
                        <div class="col-lg-8">
                            <input type="number" name="maxlength" class="form-control" value="{{old('maxlength') ?? $field->maxlength}}" placeholder="Maximum Value">
                        </div>
                    </div>
                    <div id="f-required" class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">Required :</label>
                        <div class="col-lg-8">
                            <div class="i-checks">
                                <input type="checkbox" name="required" {{$field->required ? 'checked' : ''}}>
                            </div>
                        </div>
                    </div>
                    <div id="f-unique" class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">Unique :</label>
                        <div class="col-lg-8">
                            <div class="i-checks">
                                <input type="checkbox" name="unique" {{$field->unique ? 'checked' : ''}}>
                            </div>
                        </div>
                    </div>
                    <div id="f-listing" class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">Show in Index Listing :</label>
                        <div class="col-lg-8">
                            <div class="i-checks">
                                <input type="checkbox" name="listing_col" {{$field->listing_col ? 'checked' : ''}}>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label text-right font-bold">Field Comment :</label>
                        <div class="col-lg-8">
                            <input type="text" name="comment" class="form-control" value="{{old('comment') ?? $field->comment}}" placeholder="Field Comment">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="offset-lg-2 col-lg-8">
                            <button class="btn btn-secondary btn-sm" type="submit" title="{{trans('common.save')}}"><i class="fa fa-check"></i> {{trans('common.save')}}</button>
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
    $('#colname.select2').select2({
        width: '100%',
        placeholder: "{{ trans('common.choose') }}"
    });
    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-blue',
    });
    showValuesSection();
    function showValuesSection() {
        var ft_val = '{{$field->field_type_id}}';
        $('#f-unique').show();
        $('#f-default').show();
        $('#f-minlength').show();
        $('#f-maxlength').show();

        if ($.inArray(ft_val, ['1', '2', '6', '8', '9', '11', '13', '15', '16']) != -1) {
            $('#f-unique').hide();
        }

        if ($.inArray(ft_val, ['2', '3', '4', '6', '7', '8', '9', '11', '13', '16', '18', '19', '20']) != -1) {
            $('#f-minlength').hide();
            $('#f-maxlength').hide();
        }
    }
</script>
@endpush