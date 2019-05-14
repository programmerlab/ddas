@foreach($menus as $menu)
    @if($menu->hasChildren('active') && $menu->isRoot() && $menu->user_can_access)
        <li class="{{ \Request::is(explode(',',$menu->active_menu_url))|| $menu->getProperty('always_active',false,'boolean')?'active':'' }} {{ $menu->isRoot()?'':'has-children' }}">
            <a href="#" class="parent">{{ $menu->name }}</a>
            <div class="dropdown-menu">
                <ul>
                    @include('partials.menu.menu_item', ['menus' => $menu->getChildren('active')])
                </ul>
            </div>
        </li>
    @elseif($menu->user_can_access)
        <li class="{{ \Request::is(explode(',',$menu->active_menu_url))|| $menu->getProperty('always_active',false,'boolean')?'active':'' }}">
            <a href="{{ url($menu->url) }}" target="{{ $menu->target??'_self' }}" class="parent">
                <span>@if($menu->icon)<i class="{{ $menu->icon }} fa-fw"></i>@endif</span>
                {{ $menu->name }}
            </a>
        </li>
    @endif
@endforeach

