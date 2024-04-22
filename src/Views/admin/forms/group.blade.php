@if(isset($names) && count($names) > 0 )
<div class="form-group row kind kind0 kind1 mb-3">
    @if(!empty($label))
        <label for="{{ $names[0] ?? '' }}" class="col-sm-2 col-form-label">
            {!! $label ?? '' !!}
            @if(!empty($seo))
                <span class="seo" title="SEO">
                    <i class="fa fa-coffee" aria-hidden="true"></i>
                </span>
            @endif
        </label>
    @endif
    <div class="col-sm-8">
        @if (0 || old($names[0]) || ($data && $data->{$names[0]}))
            <div class="{{ isset($names) ? $names[0] : '' }}">
                @for($i = 0; $i < count($names); $i++)
                    <label for="{{ $names[$i] ?? '' }}">{{ $labels[$i] ?? ''  }}</label>
                    @includeIf($templatePathAdmin.'forms.'.($types[$i][0] ?? 'input'), ['name' => $names[$i], 'type' => ($types[$i][1] ?? 'text'), 'label' => null,  'data' => $data, 'step' => ($steps[$i] ?? null), 'remove' => ($removes[$i] ?? null)])
                @endfor
            </div>
            @if(!empty($add_button))
            <button type="button" style="display: none;" id="{{ $add_button }}"
                    class="btn btn-light btn-active-color-primary me-3 border-2 border">
                <i class="fa fa-plus-circle" aria-hidden="true"></i>
                {{ $add_button_label ?? '' }}
            </button>
            @endif
        @else
            @if(!empty($add_button))
            <button type="button" id="add_product_promotion" class="btn btn-light btn-active-color-primary me-3 border-2 border" title="{{ $add_button_label ?? '' }}">
                <i class="fa fa-plus-circle" aria-hidden="true"></i>
                {{ $add_button_label ?? '' }}
            </button>
            @endif
        @endif
        @if ($errors->has($names[0]))
            <span class="form-text">
                <i class="fa fa-info-circle"></i> {{ $errors->first($names[0]) }}
            </span>
        @endif

    </div>
</div>
@endif