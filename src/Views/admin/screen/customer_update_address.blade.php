@extends($templatePathAdmin.'layout')

@section('main')
   <div class="row">
      <div class="col-sm-12">
         <div class="card">
                <div class="card-header with-border">
                    <h2 class="card-title">{{ $title_description??'' }}</h2>

                    <div class="card-tools">
                        <div class="btn-group float-end mr-5">
                            <a href="{{ gc_route_admin('admin_customer.index') }}" class="btn  btn-flat btn-default" title="List"><i class="fa fa-list"></i><span class="hidden-xs"> {{ gc_language_render('admin.back_list') }}</span></a>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-horizontal" id="form-main"  enctype="multipart/form-data">


                    <div class="card-body">
                        <div class="fields-group">

                            @if (gc_config_admin('customer_lastname'))
                                @includeIf($templatePathAdmin.'forms.input', ['name' => 'first_name', 'data' => $address ?? null, 'label' => gc_language_render('customer.first_name')])
                                @includeIf($templatePathAdmin.'forms.input', ['name' => 'last_name', 'data' => $address ?? null, 'label' => gc_language_render('customer.last_name')])
                            @else
                                @includeIf($templatePathAdmin.'forms.input', ['name' => 'first_name', 'data' => $address ?? null, 'label' => gc_language_render('customer.name')])
                            @endif

                            @if (gc_config_admin('customer_phone'))
                                @includeIf($templatePathAdmin.'forms.input', ['name' => 'phone', 'data' => $address ?? null, 'label' => gc_language_render('customer.phone'), 'prepend' => 'phone'])
                            @endif

                            @if (gc_config_admin('customer_postcode'))
                                @includeIf($templatePathAdmin.'forms.input', ['name' => 'postcode', 'data' => $address ?? null, 'label' => gc_language_render('customer.postcode')])
                            @endif

                            @includeIf($templatePathAdmin.'forms.input', ['name' => 'email', 'data' => $address ?? null, 'label' => gc_language_render('customer.email'), 'prepend' => 'envelope'])
                            @includeIf($templatePathAdmin.'forms.input', ['name' => 'address1', 'data' => $address ?? null, 'label' => gc_language_render('customer.address1')])
                            @if (gc_config_admin('customer_address2'))
                                @includeIf($templatePathAdmin.'forms.input', ['name' => 'address2', 'data' => $address ?? null, 'label' => gc_language_render('customer.address2')])
                            @endif
                            @if (gc_config_admin('customer_address3'))
                                @includeIf($templatePathAdmin.'forms.input', ['name' => 'address3', 'data' => $address ?? null, 'label' => gc_language_render('customer.address3')])
                            @endif

                            @if (gc_config_admin('customer_country'))
                                @includeIf($templatePathAdmin.'forms.select', ['name' => 'country', 'data' => $address ?? null, 'options' => $countries, 'label' => gc_language_render('customer.country'), 'placeholder' => gc_language_render('customer.country') ])
                            @endif




                            @if ($address->id != $customer->address_id)
                            <div class="form-group row">
                                <label for="default"
                                    class="col-md-2 col-form-label">{{ gc_language_render('customer.chose_address_default') }}</label>
                                <div class="col-md-8">
                                    <input id="default" type="checkbox" name="default">
                                </div>
                            </div>
                            @endif


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

@endpush
