@extends($templatePathAdmin.'layout')

@section('main')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bolder" id="custom-tabs-four-tab" role="tablist">
                    <li class="nav-item mt-2">
                        <a class="nav-link text-active-primary ms-0 me-10 py-5" href="{{ gc_route_admin('admin_template.index') }}" >{{ gc_language_render('admin.template.local') }}</a>
                    </li>
                    <li class="nav-item mt-2">
                        <a class="nav-link text-active-primary ms-0 me-10 py-5" href="{{ gc_route_admin('admin_template_online.index') }}" >{{ gc_language_render('admin.template.online') }}</a>
                    </li>
                    <li class="nav-item mt-2">
                        <a class="nav-link text-active-primary ms-0 me-10 py-5 active" target=_new  href="{{ gc_route_admin('admin_template.import') }}" ><span>@includeIf($templatePathAdmin.'svgs.upload', ['size' => 'x', 'color' => 'primary']) {{ gc_language_render('admin.plugin.import_data', ['data' => 'template']) }}</span></a>
                    </li>
                </ul>
            </div>
            <div class="card-header">
                <h3 class="card-title">{!! $title!!}</h3>
            </div>

            <form action="{{ gc_route_admin('admin_template.process_import') }}" method="post" accept-charset="UTF-8" class="form-horizontal" id="import-product" enctype="multipart/form-data">
                @csrf
                <div class="box-body">
                    <div class="fields-group">
                        <div class="form-group {{ $errors->has('file') ? ' text-red' : '' }}">
                            <label for="image" class="col-sm-2 col-form-label">
                            </label>
                            <div class="col-sm-6">
                                <div class="input-group">
                                    <div class="custom-file">
                                      <input type="file" id="input-file" class="form-control custom-file-input mb-2" accept="zip,application/octet-stream,application/zip,application/x-zip,application/x-zip-compressed"  required="required" name="file">
                                      <label class="custom-file-label" for="input-file">Choose file</label>
                                    </div>
                                    <div class="input-group-append">
                                      <span class="btn btn-flat button-upload btn-primary">@includeIf($templatePathAdmin.'svgs.upload', ['size' => 'x', 'color' => 'primary']) {{ gc_language_render('admin.template.import_submit') }}</span>
                                    </div>
                                </div>

                                <div>
                                    @if ($errors->has('file'))
                                    <span class="form-text text-red">
                                        <i class="fa fa-info-circle"></i> {{ $errors->first('file') }}
                                    </span>
                                    @else
                                    <span class="form-text">
                                        <i class="fa fa-info-circle"></i> {!! gc_language_render('admin.template.import_note') !!}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>

                <!-- /.box-footer -->
            </form>  
        </div>
    </div>
</div>


@endsection

@push('styles')
<style>
    .button-upload, .button-upload:hover,
    .button-upload-des, .button-upload-des:hover{
        background: #3c8dbc !important;
        color: #fff;
    }
</style>
@endpush

@push('scripts')
    <script>
        $('.button-upload').click(function(){
            $('#loading').show();
            $('#import-product').submit();
        });
        $('.button-upload-des').click(function(){
            $('#loading').show();
            $('#import-product-des').submit();
        });

    </script>
@endpush