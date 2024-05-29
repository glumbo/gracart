@extends($templatePathAdmin.'layout')

@section('main')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header with-border">
                <h2 class="card-title">{{ $title_description??'' }}</h2>

                <div class="card-tools">
                    <div class="btn-group float-end mr-5">
                        <a href="{{ gc_route_admin('admin_page.index') }}" class="btn  btn-flat btn-default" title="List"><i
                                class="fa fa-list"></i><span class="hidden-xs"> {{ gc_language_render('admin.back_list') }}</span></a>
                    </div>
                </div>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-horizontal" id="form-main"
                enctype="multipart/form-data">

                <div class="card-body">
                    <div class="fields-group">

                @php
                $descriptions = $page?$page->descriptions->keyBy('lang')->toArray():[];
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
                                @includeIf($templatePathAdmin.'forms.input', ['str1' => 'descriptions', 'str2' => $code, 'str3' => 'title', 'label' => gc_language_render('admin.page.title'), 'info' => gc_language_render('admin.max_c',['max'=>200]), 'seo' => 1])
                                @includeIf($templatePathAdmin.'forms.input', ['str1' => 'descriptions', 'str2' => $code, 'str3' => 'keyword', 'label' => gc_language_render('admin.page.keyword'), 'info' => gc_language_render('admin.max_c',['max'=>200]), 'seo' => 1])
                                @includeIf($templatePathAdmin.'forms.textarea', ['str1' => 'descriptions', 'str2' => $code, 'str3' => 'description', 'label' => gc_language_render('admin.page.description'), 'info' => gc_language_render('admin.max_c',['max'=>300]), 'seo' => 1])
                                @includeIf($templatePathAdmin.'forms.textarea', ['str1' => 'descriptions', 'str2' => $code, 'str3' => 'content', 'class' => 'editor', 'label' => gc_language_render('admin.page.content')])
                            </div>
                        </div>
                    @endforeach
                </div>
                        @includeIf($templatePathAdmin.'forms.file', ['name' => 'image', 'data' => $page ?? null, 'type' => 'page', 'label' => gc_language_render('admin.page.image'),  'text' => gc_language_render('admin.page.choose_image'), 'sub_images' => [], 'multiple' => 0 ])
                        @includeIf($templatePathAdmin.'forms.input', ['name' => 'alias', 'data' => $page ?? null, 'label' => gc_language_render('admin.page.alias'), 'info' => gc_language_render('admin.page.alias_validate')])
                        @includeIf($templatePathAdmin.'forms.shop_store')
                        @includeIf($templatePathAdmin.'forms.checkbox', ['name' => 'status', 'data' => $page ?? null, 'label' => gc_language_render('admin.page.status')])

                        {{-- Custom fields --}}
                        @php
                            $customFields = isset($customFields) ? $customFields : [];
                            $fields = !empty($page) ? $page->getCustomFields() : [];
                        @endphp
                        @includeIf($templatePathAdmin.'component.render_form_custom_field', ['customFields' => $customFields, 'fields' => $fields])
                        {{-- //Custom fields --}}

                    </div>
                </div>



                <!-- /.card-body -->

                <div class="card-footer row">
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

@endpush

@push('scripts')
@include($templatePathAdmin.'component.ckeditor_js')

<script type="text/javascript">
    $('textarea.editor').ckeditor(
    {
        filebrowserImageBrowseUrl: '{{ gc_route_admin('admin.home').'/'.config('lfm.url_prefix') }}?type=page',
        filebrowserImageUploadUrl: '{{ gc_route_admin('admin.home').'/'.config('lfm.url_prefix') }}/upload?type=page&_token={{csrf_token()}}',
        filebrowserBrowseUrl: '{{ gc_route_admin('admin.home').'/'.config('lfm.url_prefix') }}?type=Files',
        filebrowserUploadUrl: '{{ gc_route_admin('admin.home').'/'.config('lfm.url_prefix') }}/upload?type=file&_token={{csrf_token()}}',
        filebrowserWindowWidth: '900',
        filebrowserWindowHeight: '500'
    }
);
</script>

@endpush