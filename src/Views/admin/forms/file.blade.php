@php
if(!isset($name)){
    $name = 'image';
}
@endphp

<div class="form-group row mb-2 {{ $errors->has($name) ? ' text-red' : '' }}">
    <label for="image" class="col-sm-2 col-form-label">{{ $label ?? $name }}</label>
    <div class="col-sm-{{ $col ?? 8 }}">
        <div class="input-group">
            <input type="text" id="{{ $id ?? $name }}" name="{{ $name ?? '' }}" value="{{ old($name,$data[$name]??'') }}" class="form-control form-control-solid image" placeholder=""  />
            <div class="input-group-append">
                <a data-input="image" data-preview="preview_image" data-type="{{ $type ?? 'banner' }}" class="btn btn-light btn-active-color-primary me-3 border-4 border me-2 lfm">
                    <i class="la la-image icon-lg la-2x"></i> {{ $text ?? '' }}
                </a>
            </div>
        </div>
        @if ($errors->has($name))
            <span class="form-text">
                <i class="fa fa-info-circle"></i> {{ $errors->first($name) }}
            </span>
        @endif
        <div id="preview_image" class="img_holder mb-3">
            @if (old($name,$data[$name]??''))
{{--                <img src="{{ gc_file(old($name,$data[$name]??'')) }}">--}}
                <div class="image-input-wrapper h-100px mb-1" style="background-image: url('{{ gc_file(old($name,$data[$name]??'')) }}'); background-size: contain; background-repeat: no-repeat"></div>
            @endif
            @if(!empty($image_alert))
                <div class="text-muted fs-7 mb-2"> {{ $image_alert }}</div>
            @endif
        </div>
        @if (!empty($sub_images))
            @foreach ($sub_images as $key => $sub_image)
                @if ($sub_image)
                    <div class="group-image mb-3">
                        <div class="input-group">
                            <input type="text" id="sub_image_{{ $key }}" name="sub_image[]" value="{!! $sub_image !!}" class="form-control form-control-solid sub_image" placeholder="" />
                            <span class="input-group-btn">
                                <span>
                                    <a data-input="sub_image_{{ $key }}" data-preview="preview_sub_image_{{ $key }}" data-type="product" class="btn btn-light btn-active-color-primary me-3 border-2 border lfm"><i class="fa fa-image"></i>
                                                    {{ $text ?? gc_language_render('product.admin.choose_image')}}</a>
                                </span>
                                <span title="Remove" class="btn btn-icon btn-icon-muted btn-light btn-active-color-primary me-3 border-2 border">
                                    <i class="fa fa-times"></i>
                                </span>
                            </span>
                        </div>
                        <div id="preview_sub_image_{{ $key }}" class="img_holder">
                            <img src="{{ gc_file($sub_image) }}">
                        </div>
                    </div>

                @endif
            @endforeach
        @endif
        @if (!empty($multiple))
        <button type="button" id="add_sub_image" class="btn btn-light btn-active-color-primary me-3 border-2 border">
            <i class="fa fa-plus-circle" aria-hidden="true"></i>
            {{ gc_language_render('product.admin.add_sub_image') }}
        </button>
        @endif
    </div>
</div>
