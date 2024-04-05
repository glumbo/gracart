@php
if(!isset($name)){
    $name = 'name';
}
@endphp

<div class="form-group  row mb-3 {{ $errors->has($name) ? ' text-red' : '' }}">
    <label for="url" class="col-sm-2 col-form-label">{{ $label ?? '' }}</label>
    <div class="col-sm-8">
        <div class="input-group">
            @if(!empty($prepend))
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="la la-prepend icon-lg la-2x"></i></span>
            </div>
            @endif
            <input type="{{ $type ?? 'text' }}" min="{{ $min ?? '' }}" id="{{ $id ?? $name }}" name="{{ $name }}" value="{{ old()?old($name):$data[$name] ?? '' }}" class="form-control form-control-solid" placeholder="{{ $placeholder ?? '' }}" />
        </div>
        @if ($errors->has($name))
            <span class="form-text">
                <i class="fa fa-info-circle"></i> {{ $errors->first($name) }}
            </span>
        @endif
    </div>
</div>
