@extends($templatePathAdmin.'layout')

@section('main')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header with-border">
                <h2 class="card-title">{{ $title_description??'' }}</h2>

                <div class="card-tools">
                    <div class="btn-group float-right mr-5">
                        <a href="{{ gc_route_admin('admin_category.index') }}" class="btn  btn-flat btn-default" title="List"><i
                                class="fa fa-list"></i><span class="hidden-xs"> {{ gc_language_render('admin.back_list') }}</span></a>
                    </div>
                </div>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-horizontal" id="form-main"
                enctype="multipart/form-data">


                <div class="card-body">
                    @php
                    $descriptions = $category?$category->descriptions->keyBy('lang')->toArray():[];
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
                                    @includeIf($templatePathAdmin.'forms.input', ['str1' => 'descriptions', 'str2' => $code, 'str3' => 'title', 'label' => gc_language_render('admin.category.title'), 'info' => gc_language_render('admin.max_c',['max'=>200]), 'seo' => 1])
                                    @includeIf($templatePathAdmin.'forms.input', ['str1' => 'descriptions', 'str2' => $code, 'str3' => 'keyword', 'label' => gc_language_render('admin.category.keyword'), 'info' => gc_language_render('admin.max_c',['max'=>200]), 'seo' => 1])
                                    @includeIf($templatePathAdmin.'forms.textarea', ['str1' => 'descriptions', 'str2' => $code, 'str3' => 'description', 'label' => gc_language_render('admin.category.description'), 'info' => gc_language_render('admin.max_c',['max'=>300]), 'seo' => 1])
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @php
                        $categories = [0=>'==ROOT==']+ $categories;
                    @endphp
                    @includeIf($templatePathAdmin.'forms.select', ['name' => 'parent', 'options' => $categories, 'label' => gc_language_render('admin.category.select_category'), 'add_url' => gc_route_admin('admin_category.index'), 'placeholder' => '' ])
                    @includeIf($templatePathAdmin.'forms.shop_store')
                    @includeIf($templatePathAdmin.'forms.input', ['name' => 'alias', 'data' => $category ?? null, 'label' => gc_language_render('admin.category.alias'), 'info' => gc_language_render('admin.category.alias_validate')])
                    @includeIf($templatePathAdmin.'forms.file', ['name' => 'image', 'data' => $category ?? null, 'type' => 'category', 'label' => gc_language_render('admin.category.image'),  'text' => gc_language_render('product.admin.choose_image'), 'sub_images' => [], 'multiple' => 0 ])

                    @includeIf($templatePathAdmin.'forms.input', ['name' => 'sort', 'type' => 'number',  'data' => $category ?? null, 'label' => gc_language_render('admin.category.sort'), 'step' => '1', 'prepend' => 'sort-amount-desc'])
                    @includeIf($templatePathAdmin.'forms.checkbox', ['name' => 'top', 'data' => $category ?? null, 'label' => gc_language_render('admin.category.top'), 'info' => gc_language_render('admin.category.top_help')])
                    @includeIf($templatePathAdmin.'forms.checkbox', ['name' => 'status', 'data' => $category ?? null, 'label' => gc_language_render('admin.category.status')])

                        {{-- Custom fields --}}
                        @php
                            $customFields = isset($customFields) ? $customFields : [];
                            $fields = !empty($category) ? $category->getCustomFields() : [];
                        @endphp
                        @includeIf($templatePathAdmin.'component.render_form_custom_field', ['customFields' => $customFields, 'fields' => $fields])
                        {{-- //Custom fields --}}

                </div>



                <!-- /.card-body -->

                <div class="card-footer row" id="card-footer">
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

@endpush

@push('scripts')
@endpush