@extends($templatePathAdmin.'layout')

@section('main')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header with-border">
                <h2 class="card-title">{{ $title_description??'' }}</h2>

                @if (function_exists('gc_get_list_code_store') && count(gc_get_list_code_store()))
                <div class="m-0">
                    <select name="select_store" data-control="select2" data-hide-search="true" class="select_store form-select form-select-sm bg-body border-body fw-bolder w-125px">
                        <option value="" selected="selected">{{ gc_language_render('admin.select_store') }}</option>
                        @foreach (gc_get_list_code_store() as $id => $code)
                            <option value="{{ gc_route_admin('admin_store_css.index', ['store_id' => $id]) }}" {{ ($storeId == $id) ? 'disabled active selected':'' }}>{{ $code }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-horizontal" id="form-main"
                enctype="multipart/form-data">
                <input type="hidden" name="template" value="{{ $template }}">
                <input type="hidden" name="storeId" value="{{ $storeId }}">
                <div class="card-body">
                    <div class="card">
                        <div class="card-body">
                            <h2>{{ gc_language_render('store.admin.css') }}</h2>
                            @includeIf($templatePathAdmin.'forms.textarea', ['col' => 12, 'name' => 'css', 'id' => 'css', 'data' => $store_css ?? null])
                        </div>
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
<link rel="stylesheet" href="{{ gc_file('admin/plugin/mirror/doc/docs.css')}}">
<link rel="stylesheet" href="{{ gc_file('admin/plugin/mirror/lib/codemirror.css')}}">
@endpush

@push('scripts')
<script src="{{ gc_file('admin/plugin/mirror/lib/codemirror.js')}}"></script>
<script src="{{ gc_file('admin/plugin/mirror/mode/css/css.js')}}"></script>
<script>
    window.onload = function() {
      editor = CodeMirror(document.getElementById("css"), {
        mode: "text/html",
        value: document.documentElement.innerHTML
      });
    };
    var editor = CodeMirror.fromTextArea(document.getElementById("css"), {
      lineNumbers: true,
      styleActiveLine: true,
      matchBrackets: true
    });


    $(document.body).on("change",".select_store",function(){
        if(this.value !== ""){
            window.location.href = this.value;
        }
    })
  </script>
@endpush