@php
if(!isset($name)){
    $name = 'name';
}
@endphp

<div class="form-group row mb-3 {{ $errors->has($name) ? ' text-red' : '' }}">
    <label for="{{ $id ?? $name }}" class="col-sm-2 col-form-label">{{ $label }}</label>
    <div class="col-sm-8">
        <textarea class="form-control form-control-solid" rows="5" id="{{ $id ?? $name }}" name="{{ $name }}">{{ old($name,$banner[$name]??'') }}</textarea>
        @if ($errors->has($name))
            <span class="form-text">
                <i class="fa fa-info-circle"></i> {{ $errors->first($name) }}
            </span>
        @endif
    </div>
</div>
