@extends($templatePathAdmin.'layout')

@section('main')
   <div class="row">
      <div class="col-md-12">
         <div class="card">
                <div class="card-header with-border">
                    <h2 class="card-title">{{ $title_description??'' }}</h2>

                    <div class="card-tools">
                        <div class="btn-group float-right mr-5">
                            <a href="{{ gc_route_admin('admin_store_link.index') }}" class="btn  btn-flat btn-default" title="List"><i class="fa fa-list"></i><span class="hidden-xs"> {{ gc_language_render('admin.back_list') }}</span></a>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-horizontal" id="form-main"  enctype="multipart/form-data">


                    <div class="card-body">
                        @includeIf($templatePathAdmin.'forms.input', ['name' => 'name', 'data' => $link ?? null, 'label' => gc_language_render('admin.link.name')])
                        @if ($layout !='collection')
                            @includeIf($templatePathAdmin.'forms.input', ['name' => 'url', 'data' => $link ?? null, 'label' => gc_language_render('admin.link.url'), 'info' => gc_language_render('admin.link.helper_url')])
                            @includeIf($templatePathAdmin.'forms.select', ['name' => 'target', 'data' => $link ?? null, 'options' => $arrTarget, 'label' => gc_language_render('admin.link.select_target'), 'placeholder' => '----' ])
                            @includeIf($templatePathAdmin.'forms.select', ['name' => 'collection_id', 'data' => $link ?? null, 'options' => $arrCollection, 'label' => gc_language_render('admin.link.select_target'), 'placeholder' => '----' ])
                        @endif

                        @includeIf($templatePathAdmin.'forms.select', ['name' => 'group', 'data' => $link ?? null, 'options' => $arrGroup, 'label' => gc_language_render('admin.link.select_group'), 'placeholder' => '----', 'add_url' => gc_route_admin('admin_store_link_group.index') ])
                        @includeIf($templatePathAdmin.'forms.input', ['name' => 'sort', 'type' => 'number',  'data' => $link ?? null, 'label' => gc_language_render('admin.link.sort'), 'step' => '1', 'prepend' => 'sort-amount-desc'])
                        @includeIf($templatePathAdmin.'forms.shop_store')
                        @includeIf($templatePathAdmin.'forms.checkbox', ['name' => 'status', 'data' => $customer ?? null, 'label' => gc_language_render('customer.status')])
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
@endpush
