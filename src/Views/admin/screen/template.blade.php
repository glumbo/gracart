@extends($templatePathAdmin.'layout')

@section('main')
   <div class="row">
      <div class="col-md-12">
        <div class="card card-primary">
          <div class="card-header p-0 border-bottom-0">
            <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-5 fw-bolder" id="custom-tabs-four-tab" role="tablist">
              <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5 active" href="{{ gc_route_admin('admin_template.index') }}"  aria-controls="custom-tabs-four-home" aria-selected="true">{{ gc_language_render('admin.template.local') }}</a>
              </li>
              @if (config('admin.settings.api_template'))
              <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5" href="{{ gc_route_admin('admin_template_online.index') }}" >{{ gc_language_render('admin.template.online') }}</a>
              </li>
              @endif
              <li class="nav-item mt-2">
                <a class="nav-link text-active-primary ms-0 me-10 py-5" target=_new  href="{{ gc_route_admin('admin_template.import') }}" ><span>@includeIf($templatePathAdmin.'svgs.upload', ['size' => 'x', 'color' => 'primary']) {{ gc_language_render('admin.plugin.import_data', ['data' => 'template']) }}</span></a>
              </li>
            </ul>
          </div>

          <div class="card-body" id="pjax-container">
            <div class="tab-content" id="custom-tabs-four-tabContent">
              <div class="table-responsive">
              <table class="table table-hover text-nowrap table-bordered">
                <thead>
                  <tr>
                    <th>{{ gc_language_render('admin.template.image') }}</th>
                    <th>{{ gc_language_render('admin.template.name') }}</th>
                    <th>{{ gc_language_render('admin.template.code') }}</th>
                    <th>{{ gc_language_render('admin.template.image_demo') }}</th>
                    <th>{{ gc_language_render('admin.template.auth') }}</th>
                    <th>{{ gc_language_render('admin.template.website') }}</th>
                    <th>{{ gc_language_render('admin.template.version') }}</th>
                    <th>{{ gc_language_render('admin.template.action') }}</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($templates as $key => $template)
                  <tr>
                    @php
                        $pathImage = $template['config']['image'] ?? '';
                    @endphp
                   <td>{!!gc_image_render(gc_file('templates/'.$key.'/'.$pathImage)??'','50px','', $template['config']['name']??'')!!}</td>
                   <td>{{ $template['config']['name']??'' }}</td>
                   <td>{{ $key??'' }}</td>
                   <td class="pointer" onclick="imagedemo('{{ gc_file('templates/'.$key.'/images/demo.jpg') }}')"><a>{{ gc_language_render('admin.template.click_here') }}</a></td>
                   <td>{{ $template['config']['auth']??'' }}</td>
                   <td><a href="{{ $template['config']['website']??'' }}" target=_new><i class="fa fa-link" aria-hidden="true"></i>Link</a></td>
                   <td>{{ $template['config']['version']??'' }}</td>
                    <td>
                      @if (!in_array($key, $templatesUsed))
                        @if (!key_exists($key, $templatesInstalled))
                          <span onClick="processTemplate($(this), '{{ $key }}', 'install');" class="btn btn-sm btn-light btn-active-primary" title="{{ gc_language_render('action.install') }}"><i class="fa fa-plus" aria-hidden="true"></i></span>
                        @else
                          {{-- <span onClick="processTemplate($(this), '{{ $key }}', 'refresh');" class="btn btn-flat btn-info btn-sm" title="{{ gc_language_render('action.refresh') }}"><i class="fa fa-recycle" aria-hidden="true"></i></span> --}}
                          @if (!key_exists($key, $templatesActive))
                          <span onClick="processTemplate($(this), '{{ $key }}', 'enable');" class="btn btn-sm btn-light btn-active-primary" title="{{ gc_language_render('action.enable') }}"><i class="fa fa-paper-plane" aria-hidden="true"></i></span>
                          @else
                            <span onClick="processTemplate($(this), '{{ $key }}', 'disable');" class="btn btn-flat btn-warning btn-sm" title="{{ gc_language_render('action.disable') }}"><i class="fa fa-power-off" aria-hidden="true"></i></span>
                          @endif
                        @endif
                        <span onClick="processTemplate($(this), '{{ $key }}', 'remove');" title="{{ gc_language_render('action.remove') }}" class="btn btn-sm btn-light btn-active-danger btn-sm"><i class="fa fa-trash"></i></span>
                      @else
                      <span class="btn btn-flat btn-success btn-sm" title="{{ gc_language_render('admin.template.used') }}"><i class="fa fa-check" aria-hidden="true"></i></span>
                      {{-- <span onClick="processTemplate($(this), '{{ $key }}', 'refresh');" class="btn btn-flat btn-info btn-sm" title="{{ gc_language_render('action.refresh') }}"><i class="fa fa-recycle" aria-hidden="true"></i></span> --}}
                      @endif
                    </td>
                  </tr>
                @endforeach
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


<script type="text/javascript">
  
function processTemplate(obj,key, action = 'refresh') {

if (action == 'refresh' || action == 'install') {
  var urlAction = '{{ gc_route_admin('admin_template.refresh') }}';
}
if (action == 'remove') {
  var urlAction = '{{ gc_route_admin('admin_template.remove') }}';
}
if (action == 'disable') {
  var urlAction = '{{ gc_route_admin('admin_template.disable') }}';
}
if (action == 'enable') {
  var urlAction = '{{ gc_route_admin('admin_template.enable') }}';
}

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
        url: urlAction,
        data: {
          "_token": "{{ csrf_token() }}",
          "key":key,
        },
        success: function (response) {
          console.log(response);
        if(parseInt(response.error) ==0){
          alertMsg('success', response.msg);
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

function imagedemo(image) {
  Swal.fire({
    title: '{{  gc_language_render('admin.template.image_demo') }}',
    text: '',
    imageUrl: image,
    imageWidth: 800,
    imageHeight: 800,
    imageAlt: 'Image demo',
  })
}
    
  </script>
@endpush
