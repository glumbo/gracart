@extends($templatePathAdmin.'layout')

@section('main')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header with-border">
                <h2 class="card-title">{{ $title_description??'' }}</h2>
                <div class="card-tools">
                    <div class="btn-group float-end mr-5">
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
                <input type="hidden" name="kind" value="{{ GC_PRODUCT_GROUP }}">
                
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

                    @includeIf($templatePathAdmin.'forms.input', ['name' => 'sku', 'data' => null, 'label' => gc_language_render('product.sku')])
                    @includeIf($templatePathAdmin.'forms.input', ['name' => 'alias', 'data' => null, 'label' => gc_language_render('product.alias'), 'info' => gc_language_render('product.alias_validate')])



                    @includeIf($templatePathAdmin.'forms.input', ['name' => 'sort', 'type' => 'number',  'data' => null, 'label' => gc_language_render('product.admin.sort'), 'step' => '1', 'prepend' => 'sort-amount-desc'])
                    @includeIf($templatePathAdmin.'forms.checkbox', ['name' => 'status', 'data' => null, 'label' => gc_language_render('product.status')])
                    @includeIf($templatePathAdmin.'forms.checkbox', ['name' => 'approve', 'data' => null, 'label' => gc_language_render('product.approve')])


                    <hr class="kind">
                        {{-- List product in groups --}}
                        <div class="form-group row kind kind2 {{ $errors->has('productInGroup') ? ' text-red' : '' }}">
                            
                            <label class="col-sm-2 col-form-label"></label>
                            <div class="col-sm-8"><label>{{ gc_language_render('product.admin.select_product_in_group') }}</label>
                            </div>
                        </div>
                        <div class="form-group row kind kind2 {{ $errors->has('productInGroup') ? ' text-red' : '' }}">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-8">
                                @if (old('productInGroup'))
                                @foreach (old('productInGroup') as $pID)
                                @if ($pID)
                                @php
                                $newHtml = str_replace('value="'.$pID.'"', 'value="'.$pID.'" selected',
                                $htmlSelectGroup);
                                @endphp
                                {!! $newHtml !!}
                                @endif
                                @endforeach
                                @endif
                                <button type="button" id="add_product_in_group" class="btn btn-light btn-active-color-primary mt-2 mb-3 me-3 border-2 border">
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
                </div>



                <!-- /.card-body -->


                <div class="card-footer kind kind0  kind1 kind2 row" id="card-footer">
                    @csrf
                    <div class="col-md-2">
                    </div>

                    <div class="col-md-8">
                        <div class="btn-group float-end">
                            <button type="submit" class="btn btn-primary">{{ gc_language_render('action.submit') }}</button>
                        </div>

                        <div class="btn-group float-start">
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


// Select product in group
$('#add_product_in_group').click(function(event) {
    var htmlSelectGroup = '{!! str_replace("\n", "", $htmlSelectGroup) !!}';
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