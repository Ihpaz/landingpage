<nav class="sidebar-nav">
    <ul id="sidebarnav">
        @php
            $menus = App\Models\Menu\Menu::with('children','children.children','children.children.children')->where('parent',0)->orderBy('hierarchy')->get();
        @endphp
        @foreach ($menus as $menu)
            {!! $menu->html !!}
        @endforeach
    </ul>
</nav>