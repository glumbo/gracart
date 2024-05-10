@extends($templatePathAdmin.'layout')
@section('main')
   <div class="row">
      <div class="col-12">
         <div class="card">
                <div class="card-header with-border">
                    <h2 class="card-title">{{ $title_description??'' }}</h2>

                    <div class="card-tools">
                        <div class="btn-group float-right mr-5">
                            <a href="{{ gc_route_admin('admin_banner.index') }}" class="btn  btn-flat btn-default" title="List"><i class="fa fa-list"></i><span class="hidden-xs"> {{ gc_language_render('admin.back_list') }}</span></a>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-horizontal" id="form-main"  enctype="multipart/form-data">


                    <div class="card-body">
                        <div class="fields-group">
                            @includeIf($templatePathAdmin.'forms.file', ['name' => 'image', 'data' => $banner, 'label' => gc_language_render('admin.banner.image'),  'text' => gc_language_render('product.admin.choose_image')])
                            @includeIf($templatePathAdmin.'forms.input', ['name' => 'url', 'data' => $banner, 'label' => gc_language_render('admin.banner.url')])
                            @includeIf($templatePathAdmin.'forms.input', ['name' => 'title', 'data' => $banner, 'label' => gc_language_render('admin.banner.title')])
                            @includeIf($templatePathAdmin.'forms.select', ['name' => 'target', 'data' => $banner, 'options' => $arrTarget, 'label' => gc_language_render('admin.banner.select_target')])
                            @includeIf($templatePathAdmin.'forms.textarea', ['name' => 'html', 'data' => $banner, 'label' => gc_language_render('admin.email_template.html')])
                            @includeIf($templatePathAdmin.'forms.select', ['name' => 'type', 'data' => $banner, 'options' => $dataType, 'label' => gc_language_render('admin.banner.type'), 'add_url' => gc_route_admin('admin_banner_type.index')])
                            @includeIf($templatePathAdmin.'forms.input', ['name' => 'sort', 'type' => 'number', 'min' => 0,  'data' => $banner, 'label' => gc_language_render('admin.banner.sort')])

                            @if (gc_check_multi_shop_installed())
                            {{-- select shop_store --}}
                            @php
                            $listStore = [];
                            if (function_exists('gc_get_list_store_of_banner_detail')) {
                                $oldData = gc_get_list_store_of_banner_detail($banner['id'] ?? '');
                            } else {
                                $oldData = null;
                            }
                            $shop_store = old('shop_store', $oldData);

                            if(is_array($shop_store)){
                                foreach($shop_store as $value){
                                    $listStore[] = $value;
                                }
                            }
                            @endphp
    
                            <div class="form-group row {{ $errors->has('shop_store') ? ' text-red' : '' }}">
                                <label for="shop_store"
                                    class="col-sm-2 col-form-label">{{ gc_language_render('admin.select_store') }}</label>
                                <div class="col-sm-8">
                                    <select class="form-control shop_store select2" 
                                    @if (gc_check_multi_store_installed())
                                        multiple="multiple"
                                    @endif
                                    data-placeholder="{{ gc_language_render('admin.select_store') }}" style="width: 100%;"
                                    name="shop_store[]">
                                        <option value=""></option>
                                        @foreach (gc_get_list_code_store() as $k => $v)
                                        <option value="{{ $k }}"
                                            {{ (count($listStore) && in_array($k, $listStore))?'selected':'' }}>{{ $v }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('shop_store'))
                                    <span class="form-text">
                                        <i class="fa fa-info-circle"></i> {{ $errors->first('shop_store') }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                            {{-- //select shop_store --}}
    @endif
                            @includeIf($templatePathAdmin.'forms.checkbox', ['name' => 'status', 'data' => $banner, 'label' => gc_language_render('admin.banner.status')])


                {{-- Custom fields --}}
                @php
                    $customFields = isset($customFields) ? $customFields : [];
                    $fields = !empty($banner) ? $banner->getCustomFields() : [];
                @endphp
                @includeIf($templatePathAdmin.'component.render_form_custom_field', ['customFields' => $customFields, 'fields' => $fields])
                {{-- //Custom fields --}}


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
<link rel="stylesheet" href="{{ gc_file('admin/plugin/mirror/doc/docs.css')}}">
<link rel="stylesheet" href="{{ gc_file('admin/plugin/mirror/lib/codemirror.css')}}">
@endpush

@push('scripts')
<script src="{{ gc_file('admin/plugin/mirror/lib/codemirror.js')}}"></script>
<script src="{{ gc_file('admin/plugin/mirror/mode/javascript/javascript.js')}}"></script>
<script src="{{ gc_file('admin/plugin/mirror/mode/css/css.js')}}"></script>
<script src="{{ gc_file('admin/plugin/mirror/mode/htmlmixed/htmlmixed.js')}}"></script>
<script>
    window.onload = function() {
      editor = CodeMirror(document.getElementById("html"), {
        mode: "text/html",
        value: document.documentElement.innerHTML
      });
    };
    var myModeSpec = {
    name: "htmlmixed",
    tags: {
        style: [["type", /^text\/(x-)?scss$/, "text/x-scss"],
                [null, null, "css"]],
        custom: [[null, null, "customMode"]]
    }
    }
    var editor = CodeMirror.fromTextArea(document.getElementById("html"), {
      lineNumbers: true,
      styleActiveLine: true,
      matchBrackets: true
    });
  </script>

@endpush
