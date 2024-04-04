{{-- Use gc_config with storeId, dont use gc_config_admin because will switch the store to the specified store Id
--}}

<div class="row">

  <div class="col-md-5">
    <div class="card">
      <div class="card-header with-border">
        <h3 class="card-title">{{ gc_language_render('product.admin.setting_info') }}</h3>
      </div>

      <div class="card-body table-responsivep-0">
       <table class="table table-hover box-body text-wrap table-bordered">
         <tbody>
           @foreach ($productConfig as $config)
           @if ($config['key'] == 'product_tax')
           <tr>
            <td>{{ gc_language_render('product.config_manager.tax') }}</td>
            <td><a href="#" class="editable-required" data-name="product_tax" data-type="select" data-pk="" data-source="{{ json_encode($taxs) }}" data-url="{{ $urlUpdateConfig }}" data-title="{{ gc_language_render('product.config_manager.tax') }}" data-value="{{ gc_config('product_tax', $storeId) }}" data-original-title="" title="" data-placement="left"></a></td>
          </tr>
           @else
           <tr>
            <td>{{ gc_language_render($config['detail']) }}</td>
            <td><input class="check-data-config" data-store="{{ $storeId }}"  type="checkbox" name="{{ $config['key'] }}"  {{ $config['value']?"checked":"" }}></td>
          </tr>
           @endif

           @endforeach
         </tbody>
       </table>
      </div>
    </div>
  </div>

  <div class="col-md-7">
    <div class="card">
      <div class="card-header with-border">
        <h3 class="card-title">{{ gc_language_render('product.admin.setting_info') }}</h3>
      </div>

      <div class="card-body table-responsivep-0">
       <table class="table table-hover box-body text-wrap table-bordered">
        <thead>
          <tr>
            <th>{{ gc_language_render('product.config_manager.field') }}</th>
            <th>{{ gc_language_render('product.config_manager.value') }}</th>
            <th>{{ gc_language_render('product.config_manager.required') }}</th>
          </tr>
        </thead>
         <tbody>
           @foreach ($productConfigAttribute as $key => $config)
           <tr>
            <td>{{ gc_language_render($config['detail']) }}</td>
            <td><input class="check-data-config" data-store="{{ $storeId }}"  type="checkbox" name="{{ $config['key'] }}"  {{ $config['value']?"checked":"" }}></td>
            <td>
              @if (!empty($productConfigAttributeRequired[$key.'_required']))
              <input class="check-data-config" data-store="{{ $storeId }}"  type="checkbox" name="{{ $productConfigAttributeRequired[$key.'_required']['key'] }}"  {{ $productConfigAttributeRequired[$key.'_required']['value']?"checked":"" }}>
              @endif
            </td>
          </tr>
           @endforeach
         </tbody>
       </table>
      </div>
    </div>
  </div>

</div>