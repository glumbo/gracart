@extends($templatePathAdmin.'layout')

@section('main')
   <div class="row">
      <div class="col-md-12">
         <div class="card">
                <div class="card-header with-border">
                    <h2 class="card-title">{{ $title_description??'' }}</h2>

                    <div class="card-tools">
                        <div class="btn-group float-right mr-5">
                            <a href="{{ gc_route_admin('admin_store_block.index') }}" class="btn  btn-flat btn-default" title="List"><i class="fa fa-list"></i><span class="hidden-xs"> {{ gc_language_render('admin.back_list') }}</span></a>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-horizontal" id="form-main"  enctype="multipart/form-data">


                    <div class="card-body">
                        @includeIf($templatePathAdmin.'forms.input', ['name' => 'name', 'data' => $layout ?? null, 'label' => gc_language_render('admin.store_block.name')])
                        @includeIf($templatePathAdmin.'forms.select', ['name' => 'position',  'data' => $layout ?? null, 'options' => $layoutPosition, 'label' => gc_language_render('admin.store_block.select_position'), 'placeholder' => ''])
{{--                                <span style="cursor: pointer;" onclick="imagedemo('https://static.grakan.org/file/block-template.jpg');"><i class="fa fa-question-circle" aria-hidden="true"></i></span>--}}
                        @php
                            $layoutPage = ['*'=> gc_language_render('admin.position_all')] + $layoutPage;
                            $arrPage = explode(',', $layout['page']??'');
                        @endphp
                        @includeIf($templatePathAdmin.'forms.select', ['name' => 'page',  'data' => $arrPage ?? null, 'options' => $layoutPage, 'label' => gc_language_render('admin.store_block.select_page'), 'placeholder' => '', 'multiple' => 1])
                        @if ($layout)
                            @includeIf($templatePathAdmin.'forms.radio', ['name' => 'type',  'data' => $layout ?? null, 'label' => gc_language_render('admin.store_block.type'), 'options' => [$layout['type'] => $layoutType[$layout['type']]], 'actives' => [1]])
                        @else
                            @includeIf($templatePathAdmin.'forms.radio', ['name' => 'type',  'data' => $layout ?? null, 'label' => gc_language_render('admin.store_block.type'), 'options' => $layoutType, 'actives' => [0, 1]])
                        @endif

                        @php
                            $dataType = old('type',$layout['type']??'')
                        @endphp

                        @if ($dataType =='page')
                            @includeIf($templatePathAdmin.'forms.select', ['name' => 'text',  'data' => $layout ?? null, 'options' => $listViewPage, 'label' => gc_language_render('admin.store_block.text'), 'placeholder' => ''])
                        @elseif ($dataType =='view')
                            @includeIf($templatePathAdmin.'forms.select', ['name' => 'text',  'data' => $layout ?? null, 'options' => $listViewBlock, 'label' => gc_language_render('admin.store_block.text'), 'placeholder' => '', 'info' => gc_language_render('admin.store_block.helper_view',['template' => 'backend/'.gc_store('backend_template', $storeId)])])
                        @else
                            @includeIf($templatePathAdmin.'forms.textarea', ['name' => 'text', 'data' => $layout ?? null, 'label' => gc_language_render('admin.store_block.text'), 'info' => gc_language_render('admin.store_block.helper_html') ])
                        @endif

                        @if (gc_check_multi_shop_installed())
                            @includeIf($templatePathAdmin.'forms.select', ['name' => 'store_id',  'data' => $layout ?? null, 'options' => gc_get_list_code_store(), 'label' => gc_language_render('admin.select_store'), 'placeholder' => ''])
                        @endif
                        @includeIf($templatePathAdmin.'forms.input', ['name' => 'sort', 'type' => 'number',  'data' => $layout ?? null, 'label' => gc_language_render('admin.store_block.sort'), 'step' => '1', 'prepend' => 'sort-amount-desc'])
                        @includeIf($templatePathAdmin.'forms.checkbox', ['name' => 'status', 'data' => $layout ?? null, 'label' => gc_language_render('admin.store_block.status')])
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



<script type="text/javascript">
$(function () {
    $('[name="type"]').change(function(){
    var type = $(this).val();
    var obj = $('[name="text"]');
    obj.next('.form-text').remove();
    if(type =='html'){
       obj.before('<textarea name="text" class="form-control text" rows="5" placeholder="Layout text"></textarea><span class="form-text"><i class="fa fa-info-circle"></i> {{ gc_language_render('admin.store_block.helper_html') }}.</span>');
       obj.remove();
    }else if(type =='view'){

        var storeId = $('[name="store_id"]').val() ? $('[name="store_id"]').val() : {{ session('adminStoreId') }};
        
        $('#loading').show();
        $.ajax({
            method: 'get',
            url: '{{ gc_route_admin('admin_store_block.listblock_view') }}?store_id='+storeId,
            success: function (data) {
                obj.before(data);
                obj.remove();
                $('#loading').hide();
            }
        });
    }else if(type =='page'){

        var storeId = $('[name="store_id"]').val() ? $('[name="store_id"]').val() : {{ session('adminStoreId') }};

        $('#loading').show();
        $.ajax({
            method: 'get',
            url: '{{ gc_route_admin('admin_store_block.listblock_page') }}?store_id='+storeId,
            success: function (data) {
                obj.before(data);
                obj.remove();
                $('#loading').hide();
            }
        });
        }
    });

    $('[name="store_id"]').change(function(){
        var type_checked = $('[name="type"]:checked').val();
        if (type_checked != 'view' || type_checked != 'page') {
            return;
        }
        var storeId = $(this).val();
        var url_type = '';
        if (type_checked == 'view') {
            url_type = '{{ gc_route_admin('admin_store_block.listblock_view') }}?store_id='+storeId;
        }
        if (type_checked == 'page') {
            url_type = '{{ gc_route_admin('admin_store_block.listblock_page') }}?store_id='+storeId;
        }
        $('#loading').show();
        $.ajax({
            method: 'get',
            url: url_type,
            success: function (data) {
                var obj = $('[name="text"]');
                obj.next('.form-text').remove();
                obj.replaceWith(data);
                $('#loading').hide();
            }
        });
    });
});

function imagedemo(image) {
      Swal.fire({
        title: '{{  gc_language_render('admin.template.image_demo') }}',
        text: '',
        imageUrl: image,
        imageWidth: 600,
        imageHeight: 600,
        imageAlt: 'Image demo',
      })
}

</script>

@endpush
