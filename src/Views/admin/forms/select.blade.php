@php
if(!isset($name)){
    $name = 'name';
}
$rnd = rand(0,9999999999);
@endphp

@if (!empty($options))
    <div class="form-group row mb-3 {{ $errors->has($name) ? ' text-red' : '' }} border-0"  data-kt-menu="true" id="kt_menu_{{ $rnd }}">
        <label for="{{ $id ?? $name.$rnd }}" class="col-sm-2 col-form-label">{{ $label ?? $name }}</label>
        <div class="col-sm-8">
            <select class="form-control form-select form-select-solid" id="{{ $id ?? $name.$rnd }}" name="{{ $name }}"  data-kt-select2="true" data-placeholder="{{ $label ?? $name }}" data-dropdown-parent="#kt_menu_{{ $rnd }}" data-allow-clear="true">
                @foreach ($options as $k => $v)
                    <option {{ (old($name, $data[$name]??'') ==  $k)?'selected':'' }} value="{{ $k }}">{{ $v }}</option>
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
