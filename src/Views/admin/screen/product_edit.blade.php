@extends($templatePathAdmin.'layout')

@section('main')
<style>
    #start-add {
        margin: 20px;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header with-border">
                <h2 class="card-title">{{ $title_description??'' }}</h2>

                <div class="card-tools">
                    <div class="btn-group float right  mr-5">
                        <a href="{{ gc_route_admin('admin_product.index') }}" class="btn  btn-flat btn-default" title="List">
                            <i class="fa fa-list"></i><span class="hidden-xs"> {{ gc_language_render('admin.back_list') }}</span>
                        </a>
                    </div>
                </div>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="{{ gc_route_admin('admin_product.edit',['id'=>$product['id']]) }}" method="post" accept-charset="UTF-8"
                class="form-horizontal" id="form-main" enctype="multipart/form-data">

                @if (gc_config_admin('product_kind'))
                            <div class="d-flex d-flex justify-content-center mb-3"  id="start-add">
                                <div class="form-group">
                                    <div style="width: 300px;text-align: center; z-index:999">
                                        <b>{{ gc_language_render('product.kind') }}:</b> {{ $kinds[$product->kind]??'' }}
                                    </div>
                                </div>
                            </div>
                @endif

                <div class="card-body">
                    {{-- Descriptions --}}
                    @php
                        $descriptions = $product->descriptions->keyBy('lang')->toArray();
                    @endphp


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
                                    @if ($product->kind == GC_PRODUCT_SINGLE || $product->kind == GC_PRODUCT_BUILD)
                                        @includeIf($templatePathAdmin.'forms.textarea', ['str1' => 'descriptions', 'str2' => $code, 'str3' => 'content', 'class' => 'editor', 'label' => gc_language_render('product.content')])
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                        {{-- //Descriptions --}}

                        {{-- Category --}}
                        @php
                        $listCate = [];
                        $category = old('category', $product->categories->pluck('id')->toArray());
                        if(is_array($category)){
                            foreach($category as $value){
                                $listCate[] = $value;
                            }
                        }
                        @endphp

                        @includeIf($templatePathAdmin.'forms.select', ['name' => 'category[]', 'data' => $listCate, 'options' => $categories, 'label' => gc_language_render('product.admin.select_category'), 'add_url' => gc_route_admin('admin_category.index'), 'multiple' => 1 ])

                    @if (gc_check_multi_shop_installed())
                        {{-- select shop_store --}}
                        @php
                        if (function_exists('gc_get_list_store_of_product_detail')) {
                            $oldData = gc_get_list_store_of_product_detail($product->id);
                        } else {
                            $oldData = null;
                        }
                        $listStore = [];
                        $shop_store = old('shop_store', $oldData);
                        if(is_array($shop_store)){
                            foreach($shop_store as $value){
                                $listStore[] = $value;
                            }
                        }
                        @endphp

                        @includeIf($templatePathAdmin.'forms.select', ['name' => 'shop_store[]', 'data' => $listStore, 'options' => gc_get_list_code_store(), 'label' => gc_language_render('admin.select_store'), 'multiple' => 1 ])

                     @endif
                    @includeIf($templatePathAdmin.'forms.file', ['name' => 'image', 'data' => $product, 'label' => gc_language_render('product.image'),  'text' => gc_language_render('product.admin.choose_image'), 'sub_images' => old('sub_image',$product->images->pluck('image')->all()), 'multiple' => 1 ])
                    @includeIf($templatePathAdmin.'forms.input', ['name' => 'sku', 'data' => $product, 'label' => gc_language_render('product.sku')])
                    @includeIf($templatePathAdmin.'forms.input', ['name' => 'alias', 'data' => $product, 'label' => gc_language_render('product.alias'), 'info' => gc_language_render('product.alias_validate')])


                    @if (gc_config_admin('product_brand') && ($product->kind == GC_PRODUCT_SINGLE || $product->kind == GC_PRODUCT_BUILD))
                        @includeIf($templatePathAdmin.'forms.select', ['name' => 'brand_id', 'data' => $product, 'options' => $brands, 'label' => gc_language_render('product.brand'), 'placeholder' => '', 'add_url' => gc_route_admin('admin_brand.index') ])
                    @endif

                    @if (gc_config_admin('product_supplier') && ($product->kind == GC_PRODUCT_SINGLE || $product->kind == GC_PRODUCT_BUILD))
                        @includeIf($templatePathAdmin.'forms.select', ['name' => 'supplier_id', 'data' => $product, 'options' => $suppliers, 'label' => gc_language_render('product.supplier'), 'placeholder' => '', 'add_url' => gc_route_admin('admin_supplier.index') ])
                    @endif

                    @if (gc_config_admin('product_cost') && $product->kind == GC_PRODUCT_SINGLE)
                        @includeIf($templatePathAdmin.'forms.input', ['name' => 'cost', 'type' => 'number',  'data' => $product, 'label' => gc_language_render('product.cost'), 'step' => '0.01'])
                    @endif

                    @if (gc_config_admin('product_price') && ($product->kind == GC_PRODUCT_SINGLE || $product->kind == GC_PRODUCT_BUILD))
                        @includeIf($templatePathAdmin.'forms.input', ['name' => 'price', 'type' => 'number',  'data' => $product, 'label' => gc_language_render('product.price'), 'step' => '0.01'])
                    @endif

                    @if (gc_config_admin('product_tax') && ($product->kind == GC_PRODUCT_SINGLE || $product->kind == GC_PRODUCT_BUILD))
                        @includeIf($templatePathAdmin.'forms.select', ['name' => 'tax_id', 'type' => 'number',  'data' => $product, 'options' => $taxs_options, 'label' => gc_language_render('product.tax'), 'add_url' => gc_route_admin('admin_tax.index')])
                    @endif

                    @if (gc_config_admin('product_promotion'))
                        @includeIf($templatePathAdmin.'forms.group', ['label' => gc_language_render('product.price_promotion'), 'names' => ['price_promotion', 'price_promotion_start', 'price_promotion_end'], 'types' => [ ['input', 'number'], ['date', 'text'], ['date', 'text']], 'labels' => [gc_language_render('product.price'), gc_language_render('product.price_promotion_start'), gc_language_render('product.price_promotion_end')], 'data' => $product, 'steps' => [0.01], 'removes' => ['removePromotion'], 'add_button' => 'add_product_promotion', 'add_button_label' => gc_language_render('product.admin.add_product_promotion') ])
                    @endif

                    @if (gc_config_admin('product_stock') && ($product->kind == GC_PRODUCT_SINGLE || $product->kind == GC_PRODUCT_BUILD))
                        @includeIf($templatePathAdmin.'forms.input', ['name' => 'stock', 'type' => 'number',  'data' => $product, 'label' => gc_language_render('product.stock'), 'step' => '1'])
                    @endif

                    @if (gc_config_admin('product_weight') && ($product->kind == GC_PRODUCT_SINGLE || $product->kind == GC_PRODUCT_BUILD))
                        @includeIf($templatePathAdmin.'forms.select', ['name' => 'weight_class',  'data' => $product, 'options' => $listWeight, 'label' => gc_language_render('product.admin.weight_class'), 'placeholder' => gc_language_render('product.admin.select_weight'), 'add_url' => gc_route_admin('admin_weight_unit.index')])
                        @includeIf($templatePathAdmin.'forms.input', ['name' => 'weight', 'type' => 'number',  'data' => $product, 'label' => gc_language_render('product.weight'), 'step' => '0.01'])
                    @endif

                    @if (gc_config_admin('product_length') && ($product->kind == GC_PRODUCT_SINGLE || $product->kind == GC_PRODUCT_BUILD))
                        @includeIf($templatePathAdmin.'forms.select', ['name' => 'length_class',  'data' => $product, 'options' => $listLength, 'label' => gc_language_render('product.admin.length_class'), 'placeholder' => gc_language_render('product.admin.select_length'), 'add_url' => gc_route_admin('admin_length_unit.index')])
                        @includeIf($templatePathAdmin.'forms.input', ['name' => 'length', 'type' => 'number',  'data' => $product, 'label' => gc_language_render('product.length'), 'step' => '0.01'])
                        @includeIf($templatePathAdmin.'forms.input', ['name' => 'height', 'type' => 'number',  'data' => $product, 'label' => gc_language_render('product.height'), 'step' => '0.01'])
                        @includeIf($templatePathAdmin.'forms.input', ['name' => 'width', 'type' => 'number',  'data' => $product, 'label' => gc_language_render('product.width'), 'step' => '0.01'])
                    @endif

                    @if (gc_config_admin('product_property') && $product->kind == GC_PRODUCT_SINGLE)
                        @includeIf($templatePathAdmin.'forms.radio', ['name' => 'property',  'data' => $product, 'label' => gc_language_render('product.property'), 'options' => $properties, 'actives' => [0, 1], 'add_url' => gc_route_admin('admin_product_property.index')])
                        @includeIf($templatePathAdmin.'forms.input', ['name' => 'download_path',  'data' => $product, 'label' => '&nbsp;', 'class_form' => (old('property', $product->property) != GC_PROPERTY_DOWNLOAD) ? 'd-none':'', 'prepend' => 'download', 'placeholder' => gc_language_render('product.download_path')])
                    @endif

                    @if (gc_config_admin('product_available') && ($product->kind == GC_PRODUCT_SINGLE || $product->kind == GC_PRODUCT_BUILD))
                        @includeIf($templatePathAdmin.'forms.date', ['name' => 'date_available', 'data' => $product, 'label' => gc_language_render('product.date_available')])
                    @endif

                    @if (($product->kind == GC_PRODUCT_SINGLE || $product->kind == GC_PRODUCT_BUILD))
                        @includeIf($templatePathAdmin.'forms.input', ['name' => 'minimum', 'type' => 'number',  'data' => $product, 'label' => gc_language_render('product.minimum'), 'step' => '1', 'info' => gc_language_render('product.minimum_help')])
                    @endif

                    @includeIf($templatePathAdmin.'forms.input', ['name' => 'sort', 'type' => 'number',  'data' => $product, 'label' => gc_language_render('product.admin.sort'), 'step' => '1', 'prepend' => 'sort-amount-desc'])
                    @includeIf($templatePathAdmin.'forms.checkbox', ['name' => 'status', 'data' => $product, 'label' => gc_language_render('product.status')])
                    @includeIf($templatePathAdmin.'forms.checkbox', ['name' => 'approve', 'data' => $product, 'label' => gc_language_render('product.approve')])


                    @if (gc_config_admin('product_kind'))
                        @if ($product->kind == GC_PRODUCT_GROUP)
                        {{-- List product in groups --}}
                        <hr>
                        <div class="form-group row {{ $errors->has('productInGroup') ? ' text-red' : '' }}">
                            <label class="col-sm-2 col-form-label"></label>
                            <div class="col-sm-8"><label>{{ gc_language_render('product.admin.select_product_in_group') }}</label>
                            </div>
                        </div>
                        <div class="form-group row {{ $errors->has('productInGroup') ? ' text-red' : '' }}">
                            <div class="col-sm-2">
                            </div>
                            <div class="col-sm-8">
                                @php
                                    $listgroups= [];
                                    $groups = old('productInGroup',$product->groups->pluck('product_id')->toArray());
                                    dd($groups);
                                    if(is_array($groups)){
                                        foreach($groups as $value){
                                            $listgroups[] = $value;
                                        }
                                    }
                                    if ($listgroups){
                                        foreach ($listgroups as $pID){
                                            if($pID){
                                                $newHtml = str_replace('value="'.$pID.'"', 'value="'.$pID.'" selected', $htmlSelectGroup);
                                                echo $newHtml;
                                            }
                                        }
                                    }
                                @endphp
{{--                                @if ($listgroups)--}}
{{--                                @foreach ($listgroups as $pID)--}}
{{--                                @if ($pID)--}}
{{--                                @php--}}
{{--                                $newHtml = str_replace('value="'.$pID.'"', 'value="'.$pID.'" selected', $htmlSelectGroup);--}}
{{--                                @endphp--}}
{{--                                {!! $newHtml !!}--}}
{{--                                @endif--}}
{{--                                @endforeach--}}
{{--                                @endif--}}
                                <div id="position_group_flag"></div>
                                @if ($errors->has('productInGroup'))
                                <span class="form-text">
                                    <i class="fa fa-info-circle"></i> {{ $errors->first('productInGroup') }}
                                </span>
                                @endif
                                <button type="button" id="add_product_in_group" class="btn btn-light btn-active-color-primary me-3 border-2 border">
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                    {{ gc_language_render('product.admin.add_product') }}
                                </button>
                                @if ($errors->has('productInGroup'))
                                <span class="form-text">
                                    <i class="fa fa-info-circle"></i> {{ $errors->first('productInGroup') }}
                                </span>
                                @endif
                            </div>
                        </div>
                        {{-- //end List product in groups --}}
                        @endif

                        @if ($product->kind == GC_PRODUCT_BUILD)
                        <hr>
                        {{-- List product build --}}
                        <div class="form-group row {{ $errors->has('productBuild') ? ' text-red' : '' }}">
                            <label class="col-sm-2 col-form-label"></label>
                            <div class="col-sm-8">
                                <label>{{ gc_language_render('product.admin.select_product_in_build') }}</label>
                            </div>
                        </div>

                        <div class="form-group row {{ $errors->has('productBuild') ? ' text-red' : '' }}">
                            <div class="col-sm-2">
                            </div>
                            <div class="col-sm-8">
                                <div class="row"></div>

                                @php
                                $listBuilds= [];
                                $groups = old('productBuild',$product->builds->pluck('product_id')->toArray());
                                $groupsQty = old('productBuildQty',$product->builds->pluck('quantity')->toArray());
                                if(is_array($groups)){
                                foreach($groups as $key => $value){
                                $listBuilds[] = $value;
                                $listBuildsQty[] = $groupsQty[$key];
                                }
                                }
                                @endphp

                                @if ($listBuilds)
                                @foreach ($listBuilds as $key => $pID)
                                @if ($pID && $listBuildsQty[$key])
                                @php
                                $newHtml = str_replace('value="'.$pID.'"', 'value="'.$pID.'" selected',
                                $htmlSelectBuild);
                                $newHtml = str_replace('name="productBuildQty[]" value="1" min=1',
                                'name="productBuildQty[]" value="'.$listBuildsQty[$key].'"', $newHtml);
                                @endphp
                                {!! $newHtml !!}
                                @endif
                                @endforeach
                                @endif
                                <div id="position_build_flag"></div>
                                @if ($errors->has('productBuild'))
                                <span class="form-text">
                                    <i class="fa fa-info-circle"></i> {{ $errors->first('productBuild') }}
                                </span>
                                @endif
                                <button type="button" id="add_product_in_build" class="btn btn-light btn-active-color-primary me-3 border-2 border">
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
                        @endif
                    @endif


                    @if (gc_config_admin('product_attribute'))
                        @if ($product->kind == GC_PRODUCT_SINGLE)
                        {{-- List product attributes --}}
                        <hr>
                        @if (!empty($attributeGroup))
                        <div class="form-group row">
                            <div class="col-sm-2">
                                <label>{{ gc_language_render('product.attribute') }} (<a href="{{ gc_route_admin('admin_attribute_group.index') }}"><i class="fa fa-plus" aria-hidden="true"></i></a>)</label>
                            </div>
                            <div class="col-sm-8">

                                @php
                                $getDataAtt = $product->attributes->groupBy('attribute_group_id')->toArray();
                                $arrAtt = [];
                                foreach ($getDataAtt as $groupId => $row) {
                                    foreach ($row as $key => $value) {
                                        $arrAtt[$groupId]['name'][] = $value['name'];
                                        $arrAtt[$groupId]['add_price'][] = $value['add_price'];
                                    }
                                }
                                $dataAtt = old('attribute', $arrAtt);
                                @endphp

                                @foreach ($attributeGroup as $attGroupId => $attName)
                                    <table width="100%">
                                        <tr>
                                            <td colspan="3"><p><b>{{ $attName }}:</b></p></td>
                                        </tr>
                                        <tr>
                                            <td>{{ gc_language_render('product.admin.add_attribute_place') }}</td>
                                            <td>{{ gc_language_render('product.admin.add_price_place') }}</td>
                                        </tr>
                                    @if (!empty($dataAtt[$attGroupId]['name']))
                                        @foreach ($dataAtt[$attGroupId]['name'] as $key => $attValue)
                                            @php
                                            $newHtml = str_replace('attribute_group', $attGroupId, $htmlProductAtrribute);
                                            $newHtml = str_replace('attribute_value', $attValue, $newHtml);
                                            $newHtml = str_replace('add_price_value', $dataAtt[$attGroupId]['add_price'][$key], $newHtml);
                                            @endphp
                                            {!! $newHtml !!}
                                        @endforeach
                                    @endif
                                        <tr>
                                            <td colspan="3">
                                                <button type="button" class="btn btn-light btn-active-color-primary mt-2 mb-3 me-3 border-2 border add_attribute" data-id="{{ $attGroupId }}">
                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                    {{ gc_language_render('product.admin.add_attribute') }}
                                                </button>
                                            </td>
                                        </tr>
                                    </table>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        {{-- //end List product attributes --}}
                    @endif
                    @endif



                    {{-- Custom fields --}}
                    @php
                        $customFields = isset($customFields) ? $customFields : [];
                        $fields = !empty($product) ? $product->getCustomFields() : [];
                    @endphp
                    @includeIf($templatePathAdmin.'component.render_form_custom_field', ['customFields' => $customFields, 'fields' => $fields])
                    {{-- //Custom fields --}}




                </div>



                <!-- /.card-body -->

                <div class="card-footer kind kind0  kind1 kind2 row" id="card-footer">
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

{{-- input image --}}
{{-- <link rel="stylesheet" href="{{ gc_file('admin/plugin/fileinput.min.css')}}"> --}}

@endpush

@push('scripts')
@include($templatePathAdmin.'component.ckeditor_js')

{{-- input image --}}
{{-- <script src="{{ gc_file('admin/plugin/fileinput.min.js')}}"></script> --}}





<script type="text/javascript">

    $("[name='property']").change(function() {
        if($(this).val() == '{{ GC_PROPERTY_DOWNLOAD }}') {
            $('#download_path_form').removeClass('d-none');
        } else {
            $('#download_path_form').addClass('d-none');
        }
    });

// Promotion
$('#add_product_promotion').click(function(event) {
    $(this).before(
        '<div class="price_promotion">'
        +'<label>{{ gc_language_render('product.price') }}</label>'
        +'<div class="form-group row mb-3">'
        +'   <div class="col-sm-8">'
        +'      <input type="number" step="0.01" id="price_promotion" name="price_promotion" value="0" class="form-control form-control-solid price" placeholder="" />'
        +'   </div>'
        +'   <div class="col-sm-1">'
        +'      <span title="{{ gc_language_render('action.remove') }}" class="btn btn-icon btn-icon-muted btn-light btn-active-color-primary me-3 border-2 border removePromotion" ><i class="fa fa-times-circle"></i></span>'
        +'   </div>'
        +'</div>'
        +'<div class="form-group row mb-3">'
        +'   <div class="col-sm-8">'
        +'      <label>{{ gc_language_render('product.price_promotion_start') }}</label>'
        +'      <div class="input-group">'
        +'          <div class="input-group-prepend"><span class="input-group-text"><i class="la la-calendar icon-lg la-2x"></i></span></div>'
        +'          <input type="text" style="width: 150px;"  id="price_promotion_start" name="price_promotion_start" value="" class="form-control form-control-solid date_time flatpickr-input price_promotion_start" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" />'
        +'      </div>'
        +'   </div>'
        +'</div>'
        +'<div class="form-group row mb-3">'
        +'   <div class="col-sm-8">'
        +'      <label>{{ gc_language_render('product.price_promotion_end') }}</label>'
        +'      <div class="input-group">'
        +'          <div class="input-group-prepend"><span class="input-group-text"><i class="la la-calendar icon-lg la-2x"></i></span></div>'
        +'          <input type="text" style="width: 150px;"  id="price_promotion_end" name="price_promotion_end" value="" class="form-control form-control-solid date_time flatpickr-input price_promotion_end" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" />'
        +'      </div>'
        +'   </div>'
        +'  </div>'
        +'</div>');
    $(this).hide();
    $('.removePromotion').click(function(event) {
        $(this).closest('.price_promotion').remove();
        $('#add_product_promotion').show();
    });
    $(function () {
        $(".date_time").flatpickr({
            dateFormat: "Y-m-d"
        });
  });
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
    +'  <input type="text" id="sub_image_'+id_sub_image+'" name="sub_image[]" value="" class="form-control sub_image" placeholder=""  />'
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


@if (gc_config_admin('product_kind') && $product->kind == GC_PRODUCT_GROUP)
// Select product in group
$('#add_product_in_group').click(function(event) {
    var htmlSelectGroup = '{!! str_replace("\n","", $htmlSelectGroup) !!}';
    $(this).before(htmlSelectGroup);
    $('.select2').select2();
    $('.removeproductInGroup').click(function(event) {
        $(this).closest('table').remove();
    });
});
$('.removeproductInGroup').click(function(event) {
    $(this).closest('table').remove();
});
//end select in group
@endif

@if (gc_config_admin('product_kind') && $product->kind == GC_PRODUCT_BUILD)
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
@endif

// Select product attributes
$('.add_attribute').click(function(event) {
    var htmlProductAtrribute = '{!! $htmlProductAtrribute ?? '' !!}';
    var attGroup = $(this).attr("data-id");
    htmlProductAtrribute = htmlProductAtrribute.replace(/attribute_group/gi, attGroup);
    htmlProductAtrribute = htmlProductAtrribute.replace("attribute_value", "");
    htmlProductAtrribute = htmlProductAtrribute.replace("add_price_value", "0");
    $(this).closest('tr').before(htmlProductAtrribute);
    $('.removeAttribute').click(function(event) {
        $(this).closest('tr').remove();
    });
});
$('.removeAttribute').click(function(event) {
    $(this).closest('tr').remove();
});
//end select attributes

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