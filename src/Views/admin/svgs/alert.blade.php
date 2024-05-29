<span class="{{ isset($size) && $size == 'x' ? '' : 'symbol symbol-'.($px??50).'px me-2' }}">
    <span class="symbol-label {{ isset($size) && $size == "x" ? '' : 'bg-light-' }}{{ $color ?? 'danger' }}">
        <!--begin::Svg Icon | path: icons/duotune/finance/fin006.svg-->
        <span class="svg-icon svg-icon-{{ $size ?? '3x' }} svg-icon-{{ $color ?? 'danger' }}">
            <svg xmlns="http://www.w3.org/2000/svg"
                 width="24" height="24"
                 viewBox="0 0 24 24" fill="none">
                <rect opacity="0.3" x="2" y="2"
                      width="20" height="20" rx="10"
                      fill="currentColor"/>
                <rect x="11" y="14" width="7" height="2"
                      rx="1"
                      transform="rotate(-90 11 14)"
                      fill="currentColor"/>
                <rect x="11" y="17" width="2" height="2"
                      rx="1"
                      transform="rotate(-90 11 17)"
                      fill="currentColor"/>
            </svg>
        </span>
        <!--end::Svg Icon-->
    </span>
</span>