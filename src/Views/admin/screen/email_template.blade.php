@extends($templatePathAdmin.'layout')

@section('main')
   <div class="row">
      <div class="col-md-12">
         <div class="card">
                <div class="card-header with-border">
                    <h2 class="card-title">{{ $title_description??'' }}</h2>

                    <div class="card-tools">
                        <div class="btn-group float-right mr-5">
                            <a href="{{ gc_route_admin('admin_email_template.index') }}" class="btn  btn-flat btn-default" title="List"><i class="fa fa-list"></i><span class="hidden-xs"> {{ gc_language_render('admin.back_list') }}</span></a>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-horizontal" id="form-main"  enctype="multipart/form-data">


                    <div class="card-body">
                        @includeIf($templatePathAdmin.'forms.input', ['name' => 'name', 'data' => $obj ?? null, 'label' => gc_language_render('admin.email_template.name')])
                        @includeIf($templatePathAdmin.'forms.select', ['name' => 'group', 'data' => $obj ?? null, 'options' => $arrayGroup, 'label' => gc_language_render('admin.email_template.group'), 'placeholder' => '' ])
                        @includeIf($templatePathAdmin.'forms.textarea', ['name' => 'text', 'data' => $obj ?? null, 'label' => gc_language_render('admin.email_template.text')])
                        @includeIf($templatePathAdmin.'forms.checkbox', ['name' => 'status', 'data' => $obj ?? null, 'label' => gc_language_render('admin.email_template.status')])

                        <hr>
                        <div class="form-group row">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-8">
                                <label>{{ gc_language_render('admin.email_template.variable_support') }}</label>
                                <div id="list-variables">
                                </div>
                            </div>
                        </div>
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
      editor = CodeMirror(document.getElementById("text"), {
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
    var editor = CodeMirror.fromTextArea(document.getElementById("text"), {
      lineNumbers: true,
      styleActiveLine: true,
      matchBrackets: true
    });
  </script>


<script type="text/javascript">
    $(document).ready(function(){
        var group = $("[name='group'] option:selected").val();
        loadListVariable(group);
    });
    $("[name='group']").change(function(){
        var group = $("[name='group'] option:selected").val();
        loadListVariable(group);        
    });
    function loadListVariable(group){
    $.ajax({
        type: "get",
        data:{key:group},
        url: "{{route('admin_email_template.list_variable')}}",
        dataType: "json",
        beforeSend: function(){
                $('#loading').show();
            },        
        success: function (data) {
            html = '<ul>';
            $.each(data, function(i, item) {
                html +='<li>'+item+'</li>';
            });   
            html += '</ul>';         
            $('#list-variables').html(html);
            $('#loading').hide();
        }
    })

    }
</script>
@endpush
