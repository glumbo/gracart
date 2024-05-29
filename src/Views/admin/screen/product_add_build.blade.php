@extends($templatePathAdmin.'layout')

@section('main')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header with-border">
                <h2 class="card-title">{{ $title_description??'' }}</h2>
                <div class="card-tools">
                    <div class="btn-group float-right mr-5">
                        <a href="{{ gc_route_admin('admin_product.index') }}" class="btn  btn-flat btn-default" title="List">
                            <i class="fa fa-list"></i><span class="hidden-xs"> {{ gc_language_render('admin.back_list') }}</span>
                        </a>
                    </div>
                </div>
            </div>
            <!-- /.card-header -->


            <!-- form start -->
            <form action="{{ gc_route_admin('admin_product.create') }}" method="post" name="form_name" accept-charset="UTF-8"
                class="form-horizontal" id="form-main" enctype="multipart/form-data">
                <input type="hidden" name="kind" value="{{ GC_PRODUCT_BUILD }}">
                
                <div id="main-add" class="card-body">
                    {{-- descriptions --}}
                    <div class="accordion accordion-flush" id="accordionDescriptions">
                        @foreach ($languages as $code => $language)
                            <div class="card accordion-item shadow-sm p-1 mb-5 bg-body rounded">
                                <h2 class="card-title accordion-header" id="descriptions">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#descriptions_{{$code}}" aria-expanded="false" aria-controls="descriptions">
                                        {{ $language->name }} &nbsp; {!! gc_image_render($language->icon,'20px','20px', $language->name) !!}
                                    </button>
                                </h2>
                                <div id="descriptions_{{$code}}" class="card-body accordion-collapse collapse show" aria-labelledby="descriptions" data-bs-parent="#accordionDescriptions">
                                    @includeIf($templatePathAdmin.'forms.input', ['str1' => 'descriptions', 'str2' => $code, 'str3' => 'name', 'label' => gc_language_render('product.name'), 'info' => gc_language_render('admin.max_c',['max'=>200])])
                                    @includeIf($templatePathAdmin.'forms.input', ['str1' => 'descriptions', 'str2' => $code, 'str3' => 'keyword', 'label' => gc_language_render('product.keyword'), 'info' => gc_language_render('admin.max_c',['max'=>200]), 'seo' => 1])
                                    @includeIf($templatePathAdmin.'forms.textarea', ['str1' => 'descriptions', 'str2' => $code, 'str3' => 'description', 'label' => gc_language_render('product.description'), 'info' => gc_language_render('admin.max_c',['max'=>300]), 'seo' => 1])
                                    @includeIf($templatePathAdmin.'forms.textarea', ['str1' => 'descriptions', 'str2' => $code, 'str3' => 'content', 'class' => 'editor', 'label' => gc_language_render('product.content')])
                                </div>
                            </div>
                        @endforeach
                    </div>
                    {{-- //descriptions --}}


                        {{-- select category --}}
                        @includeIf($templatePathAdmin.'forms.select', ['name' => 'category[]', 'options' => $categories, 'label' => gc_language_render('product.admin.select_category'), 'add_url' => gc_route_admin('admin_category.index'), 'multiple' => 1 ])
                        {{-- //select category --}}

                        @if (gc_check_multi_shop_installed())
                            {{-- select shop_store --}}
                            <div class="form-group row kind  {{ $errors->has('shop_store') ? ' text-red' : '' }}">
                                @php
                                $listStore = [];
                                if (is_array(old('shop_store'))) {
                                    foreach(old('shop_store') as $value){
                                        $listStore[] = $value;
                                    }
                                }
                                @endphp
                                @includeIf($templatePathAdmin.'forms.select', ['name' => 'shop_store[]', 'options' => gc_get_list_code_store(), 'label' => gc_language_render('admin.select_store'), 'multiple' => 1 ])
                            </div>
                            {{-- //select shop_store --}}
                        @endif
                    @includeIf($templatePathAdmin.'forms.file', ['name' => 'image', 'data' => null, 'type' => 'product', 'label' => gc_language_render('product.image'),  'text' => gc_language_render('product.admin.choose_image'), 'sub_images' => [], 'multiple' => 1 ])
                    @includeIf($templatePathAdmin.'forms.input', ['name' => 'sku', 'data' => null, 'label' => gc_language_render('product.sku')])
                    @includeIf($templatePathAdmin.'forms.input', ['name' => 'alias', 'data' => null, 'label' => gc_language_render('product.alias'), 'info' => gc_language_render('product.alias_validate')])


                    @if (gc_config_admin('product_brand'))
                        @includeIf($templatePathAdmin.'forms.select', ['name' => 'brand_id', 'data' => null, 'options' => $brands, 'label' => gc_language_render('product.brand'), 'placeholder' => '', 'add_url' => gc_route_admin('admin_brand.index') ])
                    @endif

                    @if (gc_config_admin('product_supplier'))
                        @includeIf($templatePathAdmin.'forms.select', ['name' => 'supplier_id', 'data' => null, 'options' => $suppliers, 'label' => gc_language_render('product.supplier'), 'placeholder' => '', 'add_url' => gc_route_admin('admin_supplier.index') ])
                    @endif

                    @if (gc_config_admin('product_cost'))
                        @includeIf($templatePathAdmin.'forms.input', ['name' => 'cost', 'type' => 'number',  'data' => null, 'label' => gc_language_render('product.cost'), 'step' => '0.01'])
                    @endif

                    @if (gc_config_admin('product_price'))
                        @includeIf($templatePathAdmin.'forms.input', ['name' => 'price', 'type' => 'number',  'data' => null, 'label' => gc_language_render('product.price'), 'step' => '0.01'])
                    @endif

                    @if (gc_config_admin('product_tax'))
                        @includeIf($templatePathAdmin.'forms.select', ['name' => 'tax_id', 'type' => 'number',  'data' => null, 'options' => $taxs_options, 'label' => gc_language_render('product.tax'), 'add_url' => gc_route_admin('admin_tax.index')])
                    @endif

                    @if (gc_config_admin('product_promotion'))
                        @includeIf($templatePathAdmin.'forms.group', ['label' => gc_language_render('product.price_promotion'), 'names' => ['price_promotion', 'price_promotion_start', 'price_promotion_end'], 'types' => [ ['input', 'number'], ['date', 'text'], ['date', 'text']], 'labels' => [gc_language_render('product.price'), gc_language_render('product.price_promotion_start'), gc_language_render('product.price_promotion_end')], 'data' => null, 'steps' => [0.01], 'removes' => ['removePromotion'], 'add_button' => 'add_product_promotion', 'add_button_label' => gc_language_render('product.admin.add_product_promotion') ])
                    @endif

                    @if (gc_config_admin('product_stock'))
                        @includeIf($templatePathAdmin.'forms.input', ['name' => 'stock', 'type' => 'number',  'data' => null, 'label' => gc_language_render('product.stock'), 'step' => '1'])
                    @endif

                    @if (gc_config_admin('product_weight'))
                        @includeIf($templatePathAdmin.'forms.select', ['name' => 'weight_class',  'data' => null, 'options' => $listWeight, 'label' => gc_language_render('product.admin.weight_class'), 'placeholder' => gc_language_render('product.admin.select_weight'), 'add_url' => gc_route_admin('admin_weight_unit.index')])
                        @includeIf($templatePathAdmin.'forms.input', ['name' => 'weight', 'type' => 'number',  'data' => null, 'label' => gc_language_render('product.weight'), 'step' => '0.01'])
                    @endif

                    @if (gc_config_admin('product_length'))
                        @includeIf($templatePathAdmin.'forms.select', ['name' => 'length_class',  'data' => null, 'options' => $listLength, 'label' => gc_language_render('product.admin.length_class'), 'placeholder' => gc_language_render('product.admin.select_length'), 'add_url' => gc_route_admin('admin_length_unit.index')])
                        @includeIf($templatePathAdmin.'forms.input', ['name' => 'length', 'type' => 'number',  'data' => null, 'label' => gc_language_render('product.length'), 'step' => '0.01'])
                        @includeIf($templatePathAdmin.'forms.input', ['name' => 'height', 'type' => 'number',  'data' => null, 'label' => gc_language_render('product.height'), 'step' => '0.01'])
                        @includeIf($templatePathAdmin.'forms.input', ['name' => 'width', 'type' => 'number',  'data' => null, 'label' => gc_language_render('product.width'), 'step' => '0.01'])
                    @endif

                    @if (gc_config_admin('product_available'))
                        @includeIf($templatePathAdmin.'forms.date', ['name' => 'date_available', 'data' => null, 'label' => gc_language_render('product.date_available')])
                    @endif

                    @includeIf($templatePathAdmin.'forms.input', ['name' => 'minimum', 'type' => 'number',  'data' => null, 'label' => gc_language_render('product.minimum'), 'step' => '1', 'info' => gc_language_render('product.minimum_help')])
                    @includeIf($templatePathAdmin.'forms.input', ['name' => 'sort', 'type' => 'number',  'data' => null, 'label' => gc_language_render('product.admin.sort'), 'step' => '1', 'prepend' => 'sort-amount-desc'])
                    @includeIf($templatePathAdmin.'forms.checkbox', ['name' => 'status', 'data' => null, 'label' => gc_language_render('product.status')])
                    @includeIf($templatePathAdmin.'forms.checkbox', ['name' => 'approve', 'data' => null, 'label' => gc_language_render('product.approve')])


                    <hr class="kind kind2">
                        {{-- List product build --}}
                        <div class="form-group row kind kind1 {{ $errors->has('productBuild') ? ' text-red' : '' }}">
                            <label class="col-sm-2 col-form-label"></label>
                            <div class="col-sm-8">
                                <label>{{ gc_language_render('product.admin.select_product_in_build') }}</label>
                            </div>
                        </div>

                        <div
                            class="form-group row kind kind1 {{ ($errors->has('productBuild') || $errors->has('productBuildQty'))? ' text-red' : '' }}">
                            <div class="col-sm-2">
                            </div>
                            <div class="col-sm-8">

                                @if (old('productBuild'))
                                @foreach (old('productBuild') as $key => $pID)
                                @if ( $pID && old('productBuildQty')[$key])
                                @php
                                $newHtml = str_replace('value="'.$pID.'"', 'value="'.$pID.'" selected',
                                $htmlSelectBuild);
                                $newHtml = str_replace('name="productBuildQty[]" value="1" min=1',
                                'name="productBuildQty[]" value="'.old('productBuildQty')[$key].'"', $newHtml);
                                @endphp
                                {!! $newHtml !!}
                                @endif
                                @endforeach
                                @endif
                                <button type="button" id="add_product_in_build" class="btn btn-light btn-active-color-primary mt-2 mb-3 me-3 border-2 border">
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                    {{ gc_language_render('product.admin.add_product') }}
                                </button>
                                @if ($errors->has('productBuild') || $errors->has('productBuildQty'))
                                <span class="form-text">
                                    <i class="fa fa-info-circle"></i> {{ $errors->first('productBuild') }}
                                </span>
                                @endif

                            </div>
                        </div>
                        {{-- //end List product build --}}


                </div>



                <!-- /.card-body -->


                <div class="card-footer kind row" id="card-footer">
                    @csrf
                    <div class="col-md-2">
                    </div>

                    <div class="col-md-8">
                        <div class="btn-group float-right">
                            <button type="submit" class="btn btn-primary">{{ gc_language_render('action.submit') }}</button>
                        </div>

                        <div class="btn-group float-left">
                            <button type="reset" class="btn btn-warning">{{ gc_language_render('action.reset') }}</button>
                        </div>
                    </div>
                </div>

                <!-- /.card-footer -->
            </form>
        </div>
    </div>
</div>


@endsection

@push('styles')
<style>
    #start-add {
        margin: 20px;
    }
</style>

@endpush

@push('scripts')
@include($templatePathAdmin.'component.ckeditor_js')

<script type="text/javascript">
    // Promotion
    $('#add_product_promotion').click(function(event) {
    $(this).before(
        '<div class="price_promotion">'
        +'<div class="input-group"><div class="input-group-prepend"><span class="input-group-text"><i class="fas fa-pencil-alt"></i></span></div>'
        +'  <input type="number"  step="0.01" id="price_promotion" name="price_promotion" value="0" class="form-control input-sm price" placeholder="" />'
        +'  <span title="Remove" class="btn btn-sm btn-light btn-active-danger removePromotion"><i class="fa fa-times"></i></span>'
        +'</div>'
        +'<div class="form-group">'
        +'      <label>{{ gc_language_render('product.price_promotion_start') }}</label>'
        +'      <div class="input-group">'
        +'          <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar fa-fw"></i></span></div>'
        +'          <input type="text" style="width: 150px;"  id="price_promotion_start" name="price_promotion_start" value="" class="form-control input-sm price_promotion_start date_time" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" />'
        +'      </div>'
        +'      <label>{{ gc_language_render('product.price_promotion_end') }}</label>'
        +'      <div class="input-group">'
        +'          <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-calendar fa-fw"></i></span></div>'
        +'          <input type="text" style="width: 150px;"  id="price_promotion_end" name="price_promotion_end" value="" class="form-control input-sm price_promotion_end date_time" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" />'
        +'      </div>'
        +'  </div>'
        +'</div>');
    $(this).hide();
    $('.removePromotion').click(function(event) {
        $(this).closest('.price_promotion').remove();
        $('#add_product_promotion').show();
    });
    $('.date_time').datepicker({
      format: 'yy-mm-dd'
    }) 
});
$('.removePromotion').click(function(event) {
    $('#add_product_promotion').show();
    $(this).closest('.price_promotion').remove();
});
//End promotion

// Add sub images
var id_sub_image = {{ old('sub_image')?count(old('sub_image')):0 }};
$('#add_sub_image').click(function(event) {
    id_sub_image +=1;
    $(this).before(
    '<div class="group-image">'
    +'<div class="input-group">'
    +'  <input type="text" id="sub_image_'+id_sub_image+'" name="sub_image[]" value="" class="form-control input-sm sub_image" placeholder=""  />'
    +'  <div class="input-group-append">'
    +'  <span data-input="sub_image_'+id_sub_image+'" data-preview="preview_sub_image_'+id_sub_image+'" data-type="product" class="btn btn-flat btn-primary lfm">'
    +'      <i class="fa fa-image"></i> {{gc_language_render('product.admin.choose_image')}}'
    +'  </span>'
    +' </div>'
    +'<span title="Remove" class="btn btn-sm btn-light btn-active-danger removeImage"><i class="fa fa-times"></i></span>'
    +'</div>'
    +'<div id="preview_sub_image_'+id_sub_image+'" class="img_holder"></div>'
    +'</div>');
    $('.removeImage').click(function(event) {
        $(this).closest('div').remove();
    });
    $('.lfm').filemanager();
});
    $('.removeImage').click(function(event) {
        $(this).closest('.group-image').remove();
    });
//end sub images


// Select product in build
$('#add_product_in_build').click(function(event) {
    var htmlSelectBuild = '{!! str_replace("\n", "", $htmlSelectBuild) !!}';
    $(this).before(htmlSelectBuild);
    $('.select2').select2();
    $('.removeproductBuild').click(function(event) {
        $(this).closest('table').remove();
    });
});
$('.removeproductBuild').click(function(event) {
    $(this).closest('table').remove();
});
//end select in build

// image
// with plugin options
// $("input.image").fileinput({"browseLabel":"Browse","cancelLabel":"Cancel","showRemove":true,"showUpload":false,"dropZoneEnabled":false});

/* process_form(); */

$('textarea.editor').ckeditor(
    {
        filebrowserImageBrowseUrl: '{{ gc_route_admin('admin.home').'/'.config('lfm.url_prefix') }}?type=product',
        filebrowserImageUploadUrl: '{{ gc_route_admin('admin.home').'/'.config('lfm.url_prefix') }}/upload?type=product&_token={{csrf_token()}}',
        filebrowserBrowseUrl: '{{ gc_route_admin('admin.home').'/'.config('lfm.url_prefix') }}?type=Files',
        filebrowserUploadUrl: '{{ gc_route_admin('admin.home').'/'.config('lfm.url_prefix') }}/upload?type=file&_token={{csrf_token()}}',
        filebrowserWindowWidth: '900',
        filebrowserWindowHeight: '500'
    }
);

</script>

@endpush