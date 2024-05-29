@extends($templatePathAdmin.'layout')

@section('main')
   <div class="row">
    <div class="col-md-12">
      <div class="card card-primary card-outline card-outline-tabs">
        <div class="card-header p-0 border-bottom-0">
          <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
            <li class="nav-item">
              <a class="nav-link active" href="#"  aria-controls="custom-tabs-four-home" aria-selected="true">{{ gc_language_render('admin.plugin.local') }}</a>
            </li>
            @if (config('admin.settings.api_plugin'))
            <li class="nav-item">
              <a class="nav-link" href="{{ gc_route_admin('admin_plugin_online', ['code' => strtolower($code)]) }}" >{{ gc_language_render('admin.plugin.online') }}</a>
            </li>
            @endif
            <li class="nav-item">
              <a class="nav-link" target=_new  href="{{ gc_route_admin('admin_plugin.import') }}" ><span><i class="fas fa-save"></i> {{ gc_language_render('admin.plugin.import_data', ['data' => 'plugin']) }}</span></a>
            </li>
            <li class="btn-group float-end m-2">
              {!! gc_language_render('admin.plugin.plugin_more') !!}
            </li>
          </ul>
        </div>

        <div class="card-body" id="pjax-container">
          <div class="tab-content" id="custom-tabs-four-tabContent">
            <div class="table-responsive">
            <table class="table table-hover text-nowrap table-bordered">
              <thead>
                <tr>
                  <th>{{ gc_language_render('admin.plugin.image') }}</th>
                  <th>{{ gc_language_render('admin.plugin.code') }}</th>
                  <th>{{ gc_language_render('admin.plugin.name') }}</th>
                  <th>{{ gc_language_render('admin.plugin.version') }}</th>
                  <th>{{ gc_language_render('admin.plugin.auth') }}</th>
                  <th>{{ gc_language_render('admin.plugin.link') }}</th>
                  <th>{{ gc_language_render('admin.plugin.sort') }}</th>
                  <th>{{ gc_language_render('admin.plugin.action') }}</th>
                </tr>
              </thead>
              <tbody>
                @if (!$plugins)
                  <tr>
                    <td colspan="8" style="text-align: center;color: red;">
                      {{ gc_language_render('admin.plugin.empty') }}
                    </td>
                  </tr>
                @else

                  @foreach ($plugins as $codePlugin => $pluginClassName)

                  @php
                  //Begin try catch error
                  try {
                    $classConfig = $pluginClassName.'\\AppConfig';
                    $pluginClass = new $classConfig;
                    //Check Plugin installed
                    if (!array_key_exists($codePlugin, $pluginsInstalled->toArray())) {
                      $pluginStatusTitle = gc_language_render('admin.plugin.not_install');
                      $pluginAction = '<span onClick="installPlugin($(this),\''.$codePlugin.'\');" title="'.gc_language_render('admin.plugin.install').'" type="button" class="btn btn-flat btn-success"><i class="fa fa-plus-circle"></i></span>';
                    } else {
                      //Check plugin enable
                      if($pluginsInstalled[$codePlugin]['value']){
                        $pluginStatusTitle = gc_language_render('admin.plugin.actived');
                        $pluginAction ='<span onClick="disablePlugin($(this),\''.$codePlugin.'\');" title="'.gc_language_render('admin.plugin.disable').'" type="button" class="btn btn-flat btn-warning btn-flat"><i class="fa fa-power-off"></i></span>&nbsp;';

                          if($pluginClass->config()){
                            $pluginAction .='<a href="'.url()->current().'?action=config&pluginKey='.$codePlugin.'"><span title="'.gc_language_render('admin.plugin.config').'" class="btn btn-flat btn-primary"><i class="fas fa-cog"></i></span>&nbsp;</a>';
                          }
                          //Delete data
                          $pluginAction .='<span onClick="uninstallPlugin($(this),\''.$codePlugin.'\', 1);" title="'.gc_language_render('admin.plugin.only_delete_data').'" class="btn btn-sm btn-light btn-active-danger"><i class="fas fa-times"></i></span>';

                          //You can not remove if plugin is default
                          if(!in_array($codePlugin, $arrDefault)) {
                            $pluginAction .=' <span onClick="uninstallPlugin($(this),\''.$codePlugin.'\');" title="'.gc_language_render('admin.plugin.remove').'" class="btn btn-sm btn-light btn-active-danger"><i class="fa fa-trash"></i></span>';
                          }
                      }else{
                        $pluginStatusTitle = gc_language_render('admin.plugin.disabled');
                        $pluginAction = '<span onClick="enablePlugin($(this),\''.$codePlugin.'\');" title="'.gc_language_render('admin.plugin.enable').'" type="button" class="btn btn-flat btn-primary"><i class="fa fa-paper-plane"></i></span>&nbsp;';
                          if($pluginClass->config()){
                            $pluginAction .='<a href="'.url()->current().'?action=config&pluginKey='.$codePlugin.'"><span title="'.gc_language_render('admin.plugin.config').'" class="btn btn-flat btn-primary"><i class="fas fa-cog"></i></span>&nbsp;</a>';
                          }
                          //Delete data
                          $pluginAction .='<span onClick="uninstallPlugin($(this),\''.$codePlugin.'\', 1);" title="'.gc_language_render('admin.plugin.only_delete_data').'" class="btn btn-sm btn-light btn-active-danger"><i class="fas fa-times"></i></span>';

                          //You can not remove if plugin is default
                          if(!in_array($codePlugin, $arrDefault)) {
                            $pluginAction .=' <span onClick="uninstallPlugin($(this),\''.$codePlugin.'\');" title="'.gc_language_render('admin.plugin.remove').'" class="btn btn-sm btn-light btn-active-danger"><i class="fa fa-trash"></i></span>';
                          }
                      }
                    }
                    @endphp
                    
                    <tr>
                      <td>{!! gc_image_render($pluginClass->image,'50px', '', $pluginClass->title) !!}</td>
                      <td>{{ $codePlugin }}</td>
                      <td>{{ $pluginClass->title }}</td>
                      <td>{{ $pluginClass->version??'' }}</td>
                      <td>{{ $pluginClass->auth??'' }}</td>
                      <td><a href="{{ $pluginClass->link??'' }}" target=_new><i class="fa fa-link" aria-hidden="true"></i>Link</a></td>
                      <td>{{ $pluginsInstalled[$codePlugin]['sort']??'' }}</td>
                      <td>
                        {!! $pluginAction !!}
                      </td>
                    </tr>

                    @php
                    //End try cacth
                    } catch(\Throwable $e) {
                      gc_report(json_encode($pluginClassName)." : ".$e->getMessage());
                      echo json_encode($pluginClassName)." : ".$e->getMessage();
                    }
                    @endphp
                    
                  @endforeach
                @endif
              </tbody>
            </table>
            </div>

          </div>
        </div>
        <!-- /.card -->
      </div>
      </div>
      </div>
@endsection

@push('styles')

@endpush

@push('scripts')
{{-- //Pjax --}}
<script src="{{ gc_file('admin/plugin/jquery.pjax.js')}}"></script>


<script type="text/javascript">
  function enablePlugin(obj,key) {
      $('#loading').show()
      obj.button('loading');
      $.ajax({
        type: 'POST',
        dataType:'json',
        url: '{{ gc_route_admin('admin_plugin.enable') }}',
        data: {
          "_token": "{{ csrf_token() }}",
          "key":key,
          "code":"{{ $code }}"
        },
        success: function (response) {
          console.log(response);
              if(parseInt(response.error) ==0){
                  $.pjax.reload({container:'#pjax-container'});
                  alertMsg('success', '{{ gc_language_render('admin.msg_change_success') }}');
              }else{
                alertMsg('error', response.msg);
              }
              $('#loading').hide();
              obj.button('reset');
        }
      });

  }
  function disablePlugin(obj,key) {
      $('#loading').show()
      obj.button('loading');
      $.ajax({
        type: 'POST',
        dataType:'json',
        url: '{{ gc_route_admin('admin_plugin.disable') }}',
        data: {
          "_token": "{{ csrf_token() }}",
          "key":key,
          "code":"{{ $code }}"
        },
        success: function (response) {
          console.log(response);
              if(parseInt(response.error) ==0){
                  $.pjax.reload({container:'#pjax-container'});
                  alertMsg('success', '{{ gc_language_render('admin.msg_change_success') }}');
              }else{
                alertMsg('error', response.msg);
              }
              $('#loading').hide();
              obj.button('reset');
        }
      });
  }
  function installPlugin(obj,key) {
      $('#loading').show()
      obj.button('loading');
      $.ajax({
        type: 'POST',
        dataType:'json',
        url: '{{ gc_route_admin('admin_plugin.install') }}',
        data: {
          "_token": "{{ csrf_token() }}",
          "key":key,
          "code":"{{ $code }}"
        },
        success: function (response) {
          console.log(response);
              if(parseInt(response.error) ==0){
              location.reload();
              }else{
                alertMsg('error', response.msg);
              }
              $('#loading').hide();
              obj.button('reset');
        }
      });
  }
  function uninstallPlugin(obj,key, onlyRemoveData = null) {

      Swal.fire({
        title: '{{ gc_language_render('action.action_confirm') }}',
        text: '{{ gc_language_render('action.action_confirm_warning') }}',
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '{{ gc_language_render('action.confirm_yes') }}',
      }).then((result) => {
        if (result.value) {
            $('#loading').show()
            obj.button('loading');
            $.ajax({
              type: 'POST',
              dataType:'json',
              url: '{{ gc_route_admin('admin_plugin.uninstall') }}',
              data: {
                "_token": "{{ csrf_token() }}",
                "key":key,
                "code":"{{ $code }}",
                "onlyRemoveData": onlyRemoveData,
              },
              success: function (response) {
                console.log(response);
              if(parseInt(response.error) ==0){
              location.reload();
              }else{
                alertMsg('error', response.msg);
              }
              $('#loading').hide();
              obj.button('reset');
              }
            });
        }
      })
  }

    $(document).ready(function(){
    // does current browser support PJAX
      if ($.support.pjax) {
        $.pjax.defaults.timeout = 2000; // time in milliseconds
      }
    });

</script>

@endpush
