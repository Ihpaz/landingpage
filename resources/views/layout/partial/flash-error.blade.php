@if(session()->has('errors'))
<div class="alert card-danger alert-dismissable p-3 text-left">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4 class="text-white"><i class="fa fa-warning"></i> Gagal!</h4>
    <ul class="mb-0 pl-3 text-white">
        @foreach (session('errors')->all() as $errors )
        <li>{{ $errors }}</li>
        @endforeach
    </ul>
</div>
@endif