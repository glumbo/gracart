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

<div class="form-group  row mb-3 {{ $errors->has($str ?? $name) ? ' text-red' : '' }} {{ $class_form ?? '' }}" id="{{ $name ?? '' }}_form">
    @if(!empty($label))
        <label for="{{ $name ?? '' }}" class="col-sm-2 col-form-label">
            {!! $label ?? '' !!}
            @if(!empty($seo))
                <span class="seo" title="SEO">
                    <i class="fa fa-coffee" aria-hidden="true"></i>
                </span>
            @endif
        </label>
    @endif
    <div class="col-sm-8">
        <div class="input-group {{ $class_group ?? '' }}" id="{{ $name ?? '' }}_group">
            @if(!empty($prepend))
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        @if($prepend == "calendar")
                            @includeIf($templatePathAdmin.'svgs.calendar')
                        @else
                        <i class="la la-{{ $prepend }} icon-lg la-2x"></i>
                        @endif
                    </span>
                </div>
            @endif
            <input type="{{ $type ?? 'text' }}" {{ isset($min) ? 'min="'.$min.'"': '' }} {{ isset($step) ? 'step='.$step: '' }}   style="width: 100px;" id="{{ $id ?? $str }}" name="{{ $name }}" value="{{ old($name) ? old($name):$data[$val ?? $name] ?? '' }}" class="form-control form-control-solid {{ $class ?? '' }}" placeholder="{{ $placeholder ?? '' }}" />
            @if(!empty($append))
                <div class="input-group-append">
                    <span class="input-group-text"><i class="la la-{{ $append }} icon-lg la-2x"></i></span>
                </div>
            @endif

        </div>
        @if ($errors->has($str ?? $name))
            <span class="form-text">
                <i class="fa fa-info-circle"></i> {{ $errors->first($str ?? $name) }}
            </span>
        @elseif(!empty($info))
            <span class="form-text">
                <i class="fa fa-info-circle"></i> {{ $info ?? '' }}
            </span>
        @endif
    </div>
    @if(isset($remove))
        <div class="col-sm-1">
{{--            <a href="{{ $add_url }}" class="btn btn-icon btn-icon-muted btn-light btn-active-color-primary me-3 border-2 border" title="{{ gc_language_render('action.add') }}">--}}
{{--                @includeIf($templatePathAdmin.'svgs.plus-circle')--}}
{{--            </a>--}}
            <span title="{{ gc_language_render('action.remove') }}" class="btn btn-icon btn-icon-muted btn-light btn-active-color-primary me-3 border-2 border {{ $remove }}" ><i class="fa fa-times-circle"></i></span>
        </div>
    @endif
</div>
