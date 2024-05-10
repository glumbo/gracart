@extends($templatePathAdmin.'layout')

@section('main')
   <div class="row">
      <div class="col-sm-12">
         <div class="card">
                <div class="card-header with-border">
                    <h2 class="card-title">{{ $title_description??'' }}</h2>

                    <div class="card-tools">
                        <div class="btn-group float-right mr_5">
                            <a href="{{ gc_route_admin('admin_customer.index') }}" class="btn  btn-flat btn-default" title="List"><i class="fa fa-list"></i><span class="hidden-xs"> {{ gc_language_render('admin.back_list') }}</span></a>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-horizontal" id="form-main"  enctype="multipart/form-data">


                    <div class="card-body">
                            @if (gc_config_admin('customer_lastname'))
                                @includeIf($templatePathAdmin.'forms.input', ['name' => 'first_name', 'data' => $customer ?? null, 'label' => gc_language_render('customer.first_name')])
                                @includeIf($templatePathAdmin.'forms.input', ['name' => 'last_name', 'data' => $customer ?? null, 'label' => gc_language_render('customer.last_name')])
                            @else
                                @includeIf($templatePathAdmin.'forms.input', ['name' => 'first_name', 'data' => $customer ?? null, 'label' => gc_language_render('customer.name')])
                            @endif
    
                            @if (gc_config_admin('customer_name_kana'))
                                @includeIf($templatePathAdmin.'forms.input', ['name' => 'first_name_kana', 'data' => $customer ?? null, 'label' => gc_language_render('customer.first_name_kana')])
                                @includeIf($templatePathAdmin.'forms.input', ['name' => 'last_name_kana', 'data' => $customer ?? null, 'label' => gc_language_render('customer.last_name_kana')])
                            @endif


                            @if (gc_config_admin('customer_phone'))
                                @includeIf($templatePathAdmin.'forms.input', ['name' => 'phone', 'data' => $customer ?? null, 'label' => gc_language_render('customer.phone'), 'prepend' => 'phone'])
                            @endif
    
                            @if (gc_config_admin('customer_postcode'))
                                @includeIf($templatePathAdmin.'forms.input', ['name' => 'postcode', 'data' => $customer ?? null, 'label' => gc_language_render('customer.postcode')])
                            @endif

                            @includeIf($templatePathAdmin.'forms.input', ['name' => 'email', 'data' => $customer ?? null, 'label' => gc_language_render('customer.email'), 'prepend' => 'envelope'])
                            @includeIf($templatePathAdmin.'forms.input', ['name' => 'address1', 'data' => $customer ?? null, 'label' => gc_language_render('customer.address1')])
                            @if (gc_config_admin('customer_address2'))
                                @includeIf($templatePathAdmin.'forms.input', ['name' => 'address2', 'data' => $customer ?? null, 'label' => gc_language_render('customer.address2')])
                            @endif
                            @if (gc_config_admin('customer_address3'))
                                @includeIf($templatePathAdmin.'forms.input', ['name' => 'address3', 'data' => $customer ?? null, 'label' => gc_language_render('customer.address3')])
                            @endif

                            @if (gc_config_admin('customer_country'))
                                @includeIf($templatePathAdmin.'forms.select', ['name' => 'country', 'data' => $customer ?? null, 'options' => $countries, 'label' => gc_language_render('customer.country'), 'placeholder' => gc_language_render('customer.country') ])
                            @endif
    
                            @if (gc_config_admin('customer_sex'))
                                @php
                                $sex = old('sex', $customer['sex'] ?? 0);
                                $sexes = [0 => gc_language_render('customer.sex_women'), 1 => gc_language_render('customer.sex_men')]
                                @endphp
                                @includeIf($templatePathAdmin.'forms.select', ['name' => 'sex', 'data' => $customer ?? null, 'options' => $sexes, 'label' => gc_language_render('customer.sex'), 'placeholder' => '' ])
                            @endif
    
                            @if (gc_config_admin('customer_birthday'))
                                @includeIf($templatePathAdmin.'forms.date', ['name' => 'birthday', 'data' => $customer ?? null, 'label' => gc_language_render('customer.birthday')])
                            @endif

                            @if (gc_config_admin('customer_group'))
                                @includeIf($templatePathAdmin.'forms.input', ['name' => 'group', 'type'=> 'number', 'data' => $customer ?? null, 'label' => gc_language_render('customer.group')])
                            @endif

                            @includeIf($templatePathAdmin.'forms.input', ['name' => 'password', 'data' => $customer ?? null, 'label' => gc_language_render('customer.password'), 'info' => $customer ? gc_language_render('customer.admin.keep_password') : ''])
                            @includeIf($templatePathAdmin.'forms.input', ['name' => 'password_confirmation', 'data' => $customer ?? null, 'label' => gc_language_render('customer.password_confirm')])
                            @includeIf($templatePathAdmin.'forms.checkbox', ['name' => 'status', 'data' => $customer ?? null, 'label' => gc_language_render('customer.status')])

                            {{-- Custom fields --}}
                            @php
                                $customFields = isset($customFields) ? $customFields : [];
                                $fields = !empty($customer) ? $customer->getCustomFields() : [];
                            @endphp
                            @includeIf($templatePathAdmin.'component.render_form_custom_field', ['customFields' => $customFields, 'fields' => $fields])
                            {{-- //Custom fields --}}

                    </div>



                    <!-- /.card-body -->

                    <div class="card-footer row">
                            @csrf
                        <div class="col-sm-2">
                        </div>

                        <div class="col-sm-8">
                            <div class="btn-group float-right">
                                <button type="submit" class="btn btn-primary">{{ gc_language_render('action.submit') }}</button>
                            </div>

                            <div class="btn-group pull-left">
                                <button type="reset" class="btn btn-warning">{{ gc_language_render('action.reset') }}</button>
                            </div>
                        </div>
                    </div>

                    <!-- /.card-footer -->
                </form>

            </div>

            <div class="card">
                @if (!empty($addresses))
                    <div class="card-header with-border">
                        <h2 class="card-title">{{ gc_language_render('customer.address_list') }}</h2>
                    </div>
                    @foreach($addresses as $address)
                        <div class="list">
                        @if (gc_config_admin('customer_lastname'))
                        <b>{{ gc_language_render('customer.first_name') }}:</b> {{ $address['first_name'] }}<br>
                        <b>{{ gc_language_render('customer.last_name') }}:</b> {{ $address['last_name'] }}<br>
                        @else
                        <b>{{ gc_language_render('customer.name') }}:</b> {{ $address['first_name'] }}<br>
                        @endif
                        
                        @if (gc_config_admin('customer_phone'))
                        <b>{{ gc_language_render('customer.phone') }}:</b> {{ $address['phone'] }}<br>
                        @endif
            
                        @if (gc_config_admin('customer_postcode'))
                        <b>{{ gc_language_render('customer.postcode') }}:</b> {{ $address['postcode'] }}<br>
                        @endif
            
                        <b>{{ gc_language_render('customer.address1') }}:</b> {{ $address['address1'] }}<br>

                        @if (gc_config_admin('customer_address2'))
                        <b>{{ gc_language_render('customer.address2') }}:</b> {{ $address['address2'] }}<br>
                        @endif

                        @if (gc_config_admin('customer_address3'))
                        <b>{{ gc_language_render('customer.address3') }}:</b> {{ $address['address3'] }}<br>
                        @endif
            
                        @if (gc_config_admin('customer_country'))
                        <b>{{ gc_language_render('customer.country') }}:</b> {{ $countries[$address['country']] ?? $address['country'] }}<br>
                        @endif
            
                        <span class="btn">
                            <a title="{{ gc_language_render('customer.addresses.edit') }}" href="{{ gc_route_admin('admin_customer.update_address', ['id' => $address->id]) }}"><i class="fa fa-edit"></i></a>
                        </span>
                        <span class="btn">
                            <a href="#" title="{{ gc_language_render('customer.addresses.delete') }}" class="delete-address" data-id="{{ $address->id }}"><i class="fa fa-trash"></i></a>
                        </span>
                        @if ($address->id == $customer['address_id'])
                        <span class="btn" title="{{ gc_language_render('customer.addresses.default') }}"><i class="fa fa-university" aria-hidden="true"></i></span>
                        @endif
                        </div>
                    @endforeach
                @endif
            </div>


        </div>
    </div>
@endsection

@push('styles')
<style>
    .list{
        padding: 5px;
        margin: 5px;
        border-bottom: 1px solid #dcc1c1;
    }
</style>
@endpush

@push('scripts')
<script>
    $('.delete-address').click(function(){
      var r = confirm("{{ gc_language_render('customer.confirm_delete') }}");
      if(!r) {
        return;
      }
      var id = $(this).data('id');
      $.ajax({
              url:'{{ route("admin_customer.delete_address") }}',
              type:'POST',
              dataType:'json',
              data:{id:id,"_token": "{{ csrf_token() }}"},
                  beforeSend: function(){
                  $('#loading').show();
              },
              success: function(data){
                if(data.error == 0) {
                  location.reload();
                }
              }
          });
    });
  </script>

@endpush
