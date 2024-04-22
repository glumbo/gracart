@php
    if(isset($str1) && isset($str2) && isset($str3)){
        $name = $str1.'['.$str2.']'.'['.$str3.']';
        $str = $str1.'.'.$str2.'.'.$str3;
        $data = ${$str1}[$str2] ?? null;
        $val = $str3;
    }
    if(!isset($name)){
        $name = 'name';
    }
    if(!isset($str)){
        $str = $name;
    }
@endphp

<div class="form-group row mb-3 {{ $errors->has($str ?? $name) ? ' text-red' : '' }}">
    <label for="{{ $id ?? $name }}" class="col-sm-2 col-form-label">{{ $label }}</label>
    <div class="col-sm-8">
        <textarea class="form-control form-control-solid {{ $class ?? '' }}" rows="5" id="{{ $id ?? $str }}" name="{{ $name }}">{{ old($name,$data[$val ?? $name]??'') }}</textarea>
        @if ($errors->has($str ?? $name))
            <span class="form-text">
                <i class="fa fa-info-circle"></i> {{ $errors->first($str ?? $name) }}
            </span>
        @endif
    </div>
</div>
