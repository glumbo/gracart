@php
if(!isset($name)){
    $name = 'image';
}
@endphp

<div class="form-group  row mb-2 {{ $errors->has($name) ? ' text-red' : '' }}">
    <label for="image" class="col-sm-2 col-form-label">{{ $label ?? $name }}</label>
    <div class="col-sm-8">
        <div class="input-group">
            <input type="text" id="{{ $id ?? $name }}" name="{{ $name ?? '' }}" value="{{ old($name,$data[$name]??'') }}" class="form-control form-control-solid image" placeholder=""  />
            <div class="input-group-append">
                <a data-input="image" data-preview="preview_image" data-type="banner" class="btn btn-primary me-2 lfm">
                    <i class="la la-image icon-lg la-2x"></i> {{ $text ?? '' }}
                </a>
            </div>
        </div>
        @if ($errors->has($name))
            <span class="form-text">
                <i class="fa fa-info-circle"></i> {{ $errors->first($name) }}
            </span>
        @endif
        <div id="preview_image" class="img_holder">
            @if (old($name,$data[$name]??''))
{{--                <img src="{{ gc_file(old($name,$data[$name]??'')) }}">--}}
                <div class="image-input-wrapper h-100px mb-1" style="background-image: url('{{ gc_file(old($name,$data[$name]??'')) }}'); background-size: contain; background-repeat: no-repeat"></div>
            @endif
            @if(!empty($image_alert))
                <div class="text-muted fs-7 mb-2"> {{ $image_alert }}</div>
            @endif
        </div>
    </div>
</div>
