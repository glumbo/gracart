@php
if(!isset($name)){
    $name = 'status';
}
@endphp

<div class="form-group row form-check form-switch form-switch-sm form-check-custom form-check-solid mb-2">
    <label for="{{ $id ?? $name }}" class="col-sm-2 col-form-label">{{ $label ?? $name }}</label>
    <div class="col-sm-8">
        <input class="form-check-input" type="checkbox" id="{{ $id ?? $name }}" name="{{ $name }}"  {{ old($name,(empty($data[$name])?0:1))?'checked':''}}>
        @if(!empty($text))
            <label class="form-check-label">{{ $text ?? '' }}</label>
        @endif
    </div>
</div>
