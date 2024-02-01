<div class="row page-titles">
    <div class="col-lg-5 align-self-center">
        <h3>{{$title}}</h3>
    </div>
    <div class="col-lg-7 align-self-center">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{route('backend.dashboard.index')}}">Home</a>
            </li>
            @foreach ($href as $a)
            @if ($loop->last)
            <li class="breadcrumb-item active">
                <strong>{{$title}}</strong>
            </li>
            @else
            <li class="breadcrumb-item">
                @if($a->has('name'))
                <label class="mb-0">{{$a->name}}</label>
                @else
                @endif
            </li>
            @endif
            @endforeach
        </ol>
    </div>
</div>
<!-- <li class="breadcrumb-item">
    <label class="mb-0">User Management</label>
</li>
<li class="breadcrumb-item">
    <a href="{{route('cms.user.index')}}">Users</a>
</li> -->