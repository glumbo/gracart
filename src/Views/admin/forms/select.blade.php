@php
if(!isset($name)){
    $name = 'name';
}
if(isset($multiple)){
    $name = $name."[]";
}
$rnd = rand(0,9999999999);
if(!isset($data) || empty($data)){
    if(isset($multiple)){
        $data = [];
    }else{
        $data = null;
    }
}
@endphp

@if (!empty($options))
    <div class="form-group row mb-3 {{ $errors->has($name) ? ' text-red' : '' }} border-0"  data-kt-menu="true" id="kt_menu_{{ $rnd }}">
        @if(!empty($label))
        <label for="{{ $id ?? $name.$rnd }}" class="col-sm-2 col-form-label">{{ $label ?? $name }}</label>
        @endif
        <div class="col-sm-6">
            <select class="form-select form-select-solid {{$id ?? ''}}" id="{{ $id ?? $name.$rnd }}" name="{{ $name }}" {{ isset($multiple) ? 'multiple="multiple"' : '' }}>
                @if( isset($placeholder) || isset($text))
                <option value="">{{ $placeholder ?? ($text ?? '')  }}</option>
                @endif
                @foreach ($options as $k => $v)
                    <option {{ isset($multiple) && in_array($k, $data) ? 'selected' : (( old($name, $data[$name] ?? $data->{$name} ?? '') ==  $k)?'selected':'') }} value="{{ $v->code ?? ($v->id ?? $k) }}">{{ isset($v->email) && isset($v->name) ? ($v->name.' <'.$v->email.'>') : ($v->name ?? gc_language_render($v) ) }}</option>
                @endforeach
            </select>
            @if ($errors->has($name))
                <span class="form-text">
                    {{ $errors->first($name) }}
                </span>
            @endif
        </div>
        @if(isset($add_url))
            <div class="col-sm-1">
                <a href="{{ $add_url }}" class="btn btn-icon btn-icon-muted btn-light btn-active-color-primary me-3 border-2 border" title="{{ gc_language_render('action.add') }}">
                    @includeIf($templatePathAdmin.'svgs.plus-circle')
                </a>
            </div>
        @endif
    </div>
@endif
