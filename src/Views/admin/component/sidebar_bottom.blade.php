@php
    $totalOrder = \Glumbo\Gracart\Admin\Models\AdminOrder::count();
    $styleStatus = \Glumbo\Gracart\Admin\Models\AdminOrder::$mapStyleStatus;
@endphp
@if ($totalOrder)
@php
    $arrStatus = \Glumbo\Gracart\Front\Models\ShopOrderStatus::pluck('name','id')->all();
    $groupOrder = (new \Glumbo\Gracart\Front\Models\ShopOrder)->all()->groupBy('status');
@endphp
    <li id="summary">
    <ul>
        @foreach ($groupOrder as $status => $element)
        @php
            $style = $styleStatus[$status]??'light';
            $percent = floor($element->count() * 100/$totalOrder);
        @endphp
            <li class="footer-static">
                <div>Orders {{ $arrStatus[$status]??'' }} <span class="float-end">{{ $percent }}%</span></div>
                <div class="progress">
                    <div class="progress-bar bg-{{ $style }}" role="progressbar" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $percent }}%"> <span class="sr-only">{{ $percent }}%</span></div>
                </div>
            </li>
        @endforeach

    </ul>
</li>

@endif
