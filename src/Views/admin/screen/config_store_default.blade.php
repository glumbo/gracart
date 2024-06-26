@extends($templatePathAdmin.'layout')
@section('main')
      <div class="card card-primary">
        <div class="card-header p-0 border-bottom-0">
          <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bolder" id="custom-tabs-four-tab" role="tablist">
            @if ((admin()->user()->isAdministrator() ||  admin()->user()->isViewAll()) && config('gracart.ecommerce_mode'))
            <li class="nav-item mt-2">
              <a class="nav-link text-active-primary ms-0 me-10 py-5 active" id="tab-store-order-tab" data-bs-toggle="tab" href="#tab-store-order" role="tab"><span class="nav-text fw-bold fs-4 mb-3">{{ gc_language_render('store.admin.config_order') }}</span></a>
            </li>
            @endif
            @if ((admin()->user()->isAdministrator() ||  admin()->user()->isViewAll()) && config('gracart.ecommerce_mode'))
            <li class="nav-item mt-2">
              <a class="nav-link text-active-primary ms-0 me-10 py-5" id="tab-store-customer-tab" data-bs-toggle="tab" href="#tab-store-customer"><span class="nav-text fw-bold fs-4 mb-3">{{ gc_language_render('store.admin.config_customer') }}</span></a>
            </li>
            @endif
            @if ((admin()->user()->isAdministrator() ||  admin()->user()->isViewAll()) && config('gracart.ecommerce_mode'))
            <li class="nav-item mt-2">
              <a class="nav-link text-active-primary ms-0 me-10 py-5" id="tab-store-product-tab" data-bs-toggle="tab" href="#tab-store-product" role="tab" aria-controls="tab-store-product" aria-selected="false">{{ gc_language_render('store.admin.config_product') }}</a>
            </li>
            @endif
            @if ((admin()->user()->isAdministrator() ||  admin()->user()->isViewAll()) && config('gracart.ecommerce_mode'))
            <li class="nav-item mt-2">
              <a class="nav-link text-active-primary ms-0 me-10 py-5" id="tab-store-email-tab" data-bs-toggle="pill" href="#tab-store-email" role="tab" aria-controls="tab-store-email" aria-selected="false">{{ gc_language_render('store.admin.config_email') }}</a>
            </li>
            @endif
            <li class="nav-item mt-2">
              <a class="nav-link text-active-primary ms-0 me-10 py-5" id="tab-store-url-tab" data-bs-toggle="tab" href="#tab-store-url" role="tab" aria-controls="tab-store-url" aria-selected="false">{{ gc_language_render('store.admin.config_url') }}</a>
            </li>
            <li class="nav-item mt-2">
              <a class="nav-link text-active-primary ms-0 me-10 py-5" id="tab-store-captcha-tab" data-bs-toggle="tab" href="#tab-store-captcha" role="tab" aria-controls="tab-store-captcha" aria-selected="false">{{ gc_language_render('admin.captcha.captcha_title') }}</a>
            </li>
            <li class="nav-item mt-2">
              <a class="nav-link text-active-primary ms-0 me-10 py-5" id="tab-store-display-tab" data-bs-toggle="tab" href="#tab-store-display" role="tab" aria-controls="tab-store-display" aria-selected="false">{{ gc_language_render('store.admin.config_display') }}</a>
            </li>

            @if (count($configLayout) && config('gracart.ecommerce_mode'))
            <li class="nav-item mt-2">
              <a class="nav-link text-active-primary ms-0 me-10 py-5" id="tab-store-layout-tab"  data-bs-toggle="tab" href="#tab-store-layout" role="tab" aria-controls="tab-store-layout" aria-selected="false">{{ gc_language_render('store.admin.config_layout') }}</a>
            </li>
            @endif
            <li class="nav-item mt-2">
              <a class="nav-link text-active-primary ms-0 me-10 py-5" id="tab-admin-other-tab"  data-bs-toggle="tab" href="#tab-admin-other" role="tab" aria-controls="tab-admin-other" aria-selected="false">{{ gc_language_render('store.admin.config_admin_other') }}</a>
            </li>
            <li class="nav-item mt-2">
              <a class="nav-link text-active-primary ms-0 me-10 py-5" id="tab-admin-customize-tab"  data-bs-toggle="tab" href="#tab-admin-customize" role="tab" aria-controls="tab-admin-customize" aria-selected="false">{{ gc_language_render('store.admin.config_customize') }}</a>
            </li>
          </ul>
        </div>
        
        <div class="card-body">
          <div class="tab-content" id="custom-tabs-four-tabContent">
            {{-- Tab order --}}
            @if ((admin()->user()->isAdministrator() ||  admin()->user()->isViewAll()) && config('gracart.ecommerce_mode'))
            <div class="tab-pane fade active show" id="tab-store-order" role="tabpanel" aria-labelledby="store-order">
              @include($templatePathAdmin.'screen.config_store.config_order')
            </div>
            @endif
            {{-- //End tab order --}}

            {{-- Tab customer --}}
            @if ((admin()->user()->isAdministrator() ||  admin()->user()->isViewAll()) && config('gracart.ecommerce_mode'))
            <div class="tab-pane fade" id="tab-store-customer" role="tabpanel" aria-labelledby="tab-store-customer-tab">
              @include($templatePathAdmin.'screen.config_store.config_customer')
            </div>
            @endif
            {{-- //Tab customer --}}

            {{-- Tab product --}}
            @if ((admin()->user()->isAdministrator() ||  admin()->user()->isViewAll()) && config('gracart.ecommerce_mode'))
            <div class="tab-pane fade" id="tab-store-product" role="tabpanel" aria-labelledby="tab-store-product-tab">
              @include($templatePathAdmin.'screen.config_store.config_product')
            </div>
            @endif
            
            {{-- //Tab product --}}
            @if ((admin()->user()->isAdministrator() ||  admin()->user()->isViewAll()) && config('gracart.ecommerce_mode'))
            {{-- Tab email config --}}
            <div class="tab-pane fade" id="tab-store-email" role="tabpanel" aria-labelledby="tab-store-email-tab">
              @include($templatePathAdmin.'screen.config_store.config_mail')
            </div>
            {{-- // Email config --}}
            @endif

            {{-- Tab url config --}}
            <div class="tab-pane fade" id="tab-store-url" role="tabpanel" aria-labelledby="tab-store-url-tab">
              @include($templatePathAdmin.'screen.config_store.config_url')
            </div>
            {{-- // Url config --}}

            {{-- Tab captcha config --}}
            <div class="tab-pane fade" id="tab-store-captcha" role="tabpanel" aria-labelledby="tab-store-captcha-tab">
              @include($templatePathAdmin.'screen.config_store.config_captcha')
            </div>
            {{-- // captcha config --}}

            @if (count($configLayout) && config('grakan.ecommerce_mode'))
            {{-- Tab layout config --}}
            <div class="tab-pane fade" id="tab-store-layout" role="tabpanel" aria-labelledby="tab-store-layout-tab">
              @include($templatePathAdmin.'screen.config_store.config_layout')
            </div>
            {{-- // layout config --}}
            @endif

            {{-- Tab display config --}}
            <div class="tab-pane fade" id="tab-store-display" role="tabpanel" aria-labelledby="tab-store-display-tab">
              @include($templatePathAdmin.'screen.config_store.config_display')
            </div>
            {{-- // display config --}}

            {{-- Tab admin config --}}
            <div class="tab-pane fade" id="tab-admin-other" role="tabpanel" aria-labelledby="tab-admin-other-tab">
              @include($templatePathAdmin.'screen.config_store.config_admin_other')
            </div>
            {{-- // admin config --}}

            {{-- Tab admin config customize --}}
            <div class="tab-pane fade" id="tab-admin-customize" role="tabpanel" aria-labelledby="tab-admin-customize-tab">
              @include($templatePathAdmin.'screen.config_store.config_admin_customize')
            </div>
            {{-- // admin config customize --}}

          </div>
        </div>
        <!-- /.card -->
</div>

@endsection

@push('styles')
<!-- Ediable -->
<link rel="stylesheet" href="{{ gc_file('admin/plugin/bootstrap5-editable/css/bootstrap-editable.css')}}">
<style type="text/css">
  #maintain_content img{
    max-width: 100%;
  }
</style>
@endpush

@if (empty($dataNotFound))
@push('scripts')
<!-- Ediable -->
<script src="{{ gc_file('admin/plugin/bootstrap5-editable/js/bootstrap-editable.min.js')}}"></script>

<script type="text/javascript">

  // Editable
$(document).ready(function() {

      //  $.fn.editable.defaults.mode = 'inline';
      $.fn.editable.defaults.params = function (params) {
        params._token = "{{ csrf_token() }}";
        params.storeId = "{{ $storeId }}";
        return params;
      };

      $('.editable-required').editable({
        validate: function(value) {
            if (value == '') {
                return '{{  gc_language_render('admin.not_empty') }}';
            }
        },
        success: function(data) {
          if(data.error == 0){
            alertJs('success', '{{ gc_language_render('admin.msg_change_success') }}');
          } else {
            alertJs('error', data.msg);
          }
      }
    });

    $('.editable').editable({
        validate: function(value) {
        },
        success: function(data) {
          console.log(data);
          if(data.error == 0){
            alertJs('success', '{{ gc_language_render('admin.msg_change_success') }}');
          } else {
            alertMsg('error', data.msg);
          }
      }
    });

});


$('input.check-data-config').iCheck({
    checkboxClass: 'icheckbox_square-blue',
    radioClass: 'iradio_square-blue',
    increaseArea: '20%' /* optional */
  }).on('ifChanged', function(e) {
  var isChecked = e.currentTarget.checked;
  isChecked = (isChecked == false)?0:1;
  var name = $(this).attr('name');
    $.ajax({
      url: '{{ $urlUpdateConfig }}',
      type: 'POST',
      dataType: 'JSON',
      data: {
          "_token": "{{ csrf_token() }}",
          "name": $(this).attr('name'),
          "storeId": $(this).data('store'),
          "value": isChecked
        },
    })
    .done(function(data) {
      if(data.error == 0){
        alertJs('success', '{{ gc_language_render('admin.msg_change_success') }}');
      } else {
        alertJs('error', data.msg);
      }
    });

    });

  $('input.check-data-config-global').iCheck({
    checkboxClass: 'icheckbox_square-blue',
    radioClass: 'iradio_square-blue',
    increaseArea: '20%' /* optional */
  }).on('ifChanged', function(e) {
  var isChecked = e.currentTarget.checked;
  isChecked = (isChecked == false)?0:1;
  var name = $(this).attr('name');
    $.ajax({
      url: '{{ $urlUpdateConfigGlobal }}',
      type: 'POST',
      dataType: 'JSON',
      data: {
          "_token": "{{ csrf_token() }}",
          "name": $(this).attr('name'),
          "value": isChecked
        },
    })
    .done(function(data) {
      if(data.error == 0){
        if (isChecked == 0) {
          $('#smtp-config').hide();
        } else {
          $('#smtp-config').show();
        }
        alertJs('success', '{{ gc_language_render('admin.msg_change_success') }}');
      } else {
        alertJs('error', data.msg);
      }
    });

    });


</script>

{{-- //Pjax --}}
<script src="{{ gc_file('admin/plugin/jquery.pjax.js')}}"></script>


<script>
  // Update store_info

//End update store_info
</script>

@endpush
@endif