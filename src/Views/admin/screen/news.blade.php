@extends($templatePathAdmin.'layout')

@section('main')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header with-border">
                <h2 class="card-title">{{ $title_description??'' }}</h2>

                <div class="card-tools">
                    <div class="btn-group float-right mr-5">
                        <a href="{{ gc_route_admin('admin_news.index') }}" class="btn  btn-flat btn-default" title="List"><i
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
                        $descriptions = $news?$news->descriptions->keyBy('lang')->toArray():[];
                        @endphp

                        @foreach ($languages as $code => $language)

                        <div class="card">
                            <div class="card-header with-border">
                                <h3 class="card-title">{{ $language->name }} {!! gc_image_render($language->icon,'20px','20px', $language->name) !!}</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                      <i class="fas fa-minus"></i>
                                    </button>
                                  </div>
                            </div>
                    
                            <div class="card-body">
                                @includeIf($templatePathAdmin.'forms.input', ['str1' => 'descriptions', 'str2' => $code, 'str3' => 'title', 'label' => gc_language_render('admin.news.title'), 'info' => gc_language_render('admin.max_c',['max'=>200]), 'seo' => 1])
                                @includeIf($templatePathAdmin.'forms.input', ['str1' => 'descriptions', 'str2' => $code, 'str3' => 'keyword', 'label' => gc_language_render('admin.news.keyword'), 'info' => gc_language_render('admin.max_c',['max'=>200]), 'seo' => 1])
                                @includeIf($templatePathAdmin.'forms.textarea', ['str1' => 'descriptions', 'str2' => $code, 'str3' => 'description', 'label' => gc_language_render('admin.news.description'), 'info' => gc_language_render('admin.max_c',['max'=>300]), 'seo' => 1])
                                @includeIf($templatePathAdmin.'forms.textarea', ['str1' => 'descriptions', 'str2' => $code, 'str3' => 'content', 'class' => 'editor', 'label' => gc_language_render('admin.news.content')])
                            </div>
                        </div>
                        @endforeach

                        @includeIf($templatePathAdmin.'forms.file', ['name' => 'image', 'data' => $news ?? null, 'type' => 'news', 'label' => gc_language_render('admin.news.image'),  'text' => gc_language_render('admin.page.choose_image'), 'sub_images' => [], 'multiple' => 0 ])
                        @includeIf($templatePathAdmin.'forms.input', ['name' => 'alias', 'data' => $news ?? null, 'label' => gc_language_render('admin.news.alias'), 'info' => gc_language_render('admin.page.alias_validate')])
                        @includeIf($templatePathAdmin.'forms.shop_store')
                        @includeIf($templatePathAdmin.'forms.input', ['name' => 'sort', 'type' => 'number',  'data' => $news ?? null, 'label' => gc_language_render('admin.news.sort'), 'step' => '1', 'prepend' => 'sort-amount-desc'])
                        @includeIf($templatePathAdmin.'forms.checkbox', ['name' => 'status', 'data' => $news ?? null, 'label' => gc_language_render('admin.news.status')])

                    {{-- Custom fields --}}
                        @php
                            $customFields = isset($customFields) ? $customFields : [];
                            $fields = !empty($news) ? $news->getCustomFields() : [];
                        @endphp
                        @includeIf($templatePathAdmin.'component.render_form_custom_field', ['customFields' => $customFields, 'fields' => $fields])
                        {{-- //Custom fields --}}


                </div>



                <!-- /.card-body -->

                <div class="card-footer row">
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
@include($templatePathAdmin.'component.ckeditor_js')

<script type="text/javascript">
    $('textarea.editor').ckeditor(
    {
        filebrowserImageBrowseUrl: '{{ gc_route_admin('admin.home').'/'.config('lfm.url_prefix') }}?type=content',
        filebrowserImageUploadUrl: '{{ gc_route_admin('admin.home').'/'.config('lfm.url_prefix') }}/upload?type=content&_token={{csrf_token()}}',
        filebrowserBrowseUrl: '{{ gc_route_admin('admin.home').'/'.config('lfm.url_prefix') }}?type=Files',
        filebrowserUploadUrl: '{{ gc_route_admin('admin.home').'/'.config('lfm.url_prefix') }}/upload?type=file&_token={{csrf_token()}}',
        filebrowserWindowWidth: '900',
        filebrowserWindowHeight: '500'
    }
);
</script>

@endpush