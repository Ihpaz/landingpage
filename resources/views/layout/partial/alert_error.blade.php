@if (count($errors) > 0)
<div class="alert card-danger alert-dismissable p-3 text-left">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4 class="text-white"><i class="fa fa-warning"></i>  Gagal!</h4>
    <ul class="mb-0 pl-3 text-white">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif