<div @if(isset($menus[$item->id])) data-kt-menu-trigger="click"@endif class="menu-item @if(isset($menus[$item->id])) menu-accordion @endif">
    @if($item->parent_id === 0)
        <div class="menu-content pt-8 pb-2">
            <span class="menu-section text-muted text-uppercase fs-8 ls-1">{!! gc_language_render($item->title) !!}</span>
        </div>
        @if(isset($menus[$item->id]))
            @foreach ($menus[$item->id] as $sitem)
                @includeIf($templatePathAdmin.'component.tools.menu-item', ['item' => $sitem])
            @endforeach
        @endif
    @elseif(isset($menus[$item->id]))
        <span class="menu-link">
            <span class="menu-icon">
                    <span class="menu-bullet">
                        <span class="{{ $item->icon }}"></span>
                    </span>
            </span>
            <span class="menu-title">{!! gc_language_render($item->title) !!}</span>
            <span class="menu-arrow"></span>
        </span>
        <div class="menu-sub menu-sub-accordion menu-active-bg">
            @foreach ($menus[$item->id] as $sitem)
                @includeIf($templatePathAdmin.'component.tools.menu-item', ['item' => $sitem])
            @endforeach
        </div>
    @else
        <a class="menu-link" href="{{ $item->uri?gc_url_render($item->uri):'#' }}">
												<span class="menu-bullet">
													<span class="{{ $item->icon }}"></span>
												</span>
            <span class="menu-title">{!! gc_language_render($item->title) !!}</span>
        </a>
    @endif
</div>

