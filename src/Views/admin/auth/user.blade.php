@extends($templatePathAdmin.'layout')

@section('main')
   <div class="row">
      <div class="col-md-12">
         <div class="card">
                <div class="card-header with-border">
                    <h2 class="card-title">{{ $title_description??'' }}</h2>

                    <div class="card-tools">
                        <div class="btn-group float-right mr-5">
                            <a href="{{ gc_route_admin('admin_user.index') }}" class="btn  btn-flat btn-default" title="List"><i class="fa fa-list"></i><span class="hidden-xs"> {{ gc_language_render('admin.back_list') }}</span></a>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ $url_action }}" method="post" accept-charset="UTF-8" class="form-horizontal" id="form-main"  enctype="multipart/form-data">


                    <div class="card-body">
                        @includeIf($templatePathAdmin.'forms.input', ['name' => 'name', 'data' => $user ?? null, 'label' => gc_language_render('admin.user.name')])
                        @includeIf($templatePathAdmin.'forms.input', ['name' => 'username', 'data' => $user ?? null, 'label' => gc_language_render('admin.user.username')])
                        @includeIf($templatePathAdmin.'forms.input', ['name' => 'email', 'type'=> 'email', 'data' => $user ?? null, 'label' => gc_language_render('admin.user.email')])
                        @includeIf($templatePathAdmin.'forms.file', ['name' => 'avatar', 'data' => $user ?? null, 'type' => 'avatar', 'label' => gc_language_render('admin.user.avatar'),  'text' => gc_language_render('admin.page.choose_image'), 'sub_images' => [], 'multiple' => 0 ])
                        @includeIf($templatePathAdmin.'forms.input', ['name' => 'password', 'type'=> 'password', 'data' => $user ?? null, 'label' => gc_language_render('admin.user.password'), 'info' => $user ? gc_language_render('admin.user.keep_password') : null])
                        @includeIf($templatePathAdmin.'forms.input', ['name' => 'password_confirmation', 'type'=> 'password', 'data' => $user ?? null, 'label' => gc_language_render('admin.user.password_confirmation'), 'info' => $user ? gc_language_render('admin.user.keep_password') : null])
                        @php
                            $listRoles = [];
                                $old_roles = old('roles',($user)?$user->roles->pluck('id')->toArray():'');
                                if(is_array($old_roles)){
                                    foreach($old_roles as $value){
                                        $listRoles[] = (int)$value;
                                    }
                                }
                        @endphp
                            {{-- select roles --}}
                            <div class="form-group row {{ $errors->has('roles') ? ' text-red' : '' }}">

                                <label for="roles" class="col-sm-2  control-label">{{ gc_language_render('admin.user.select_roles') }}</label>
                                <div class="col-sm-8">

                            @if (isset($user['id']) && in_array($user['id'], GC_GUARD_ADMIN))
                                @if (count($listRoles))
                                @foreach ($listRoles as $role)
                                    {!! '<span class="badge badge-primary">'.($roles[$role]??'').'</span>' !!}
                                @endforeach
                                @endif
                            @else
                                <select class="form-control roles select2"  multiple="multiple" data-placeholder="{{ gc_language_render('admin.user.select_roles') }}" style="width: 100%;" name="roles[]" >
                                    <option value=""></option>
                                    @foreach ($roles as $k => $v)
                                        <option value="{{ $k }}"  {{ (count($listRoles) && in_array($k, $listRoles))?'selected':'' }}>{{ $v }}</option>
                                    @endforeach
                                </select>
                                    @if ($errors->has('roles'))
                                        <span class="form-text">
                                            {{ $errors->first('roles') }}
                                        </span>
                                    @endif
                            @endif
                                </div>
                            </div>
                            {{-- //select roles --}}

                            {{-- select permission --}}
                            <div class="form-group row {{ $errors->has('permission') ? ' text-red' : '' }}">
                                @php
                                $listPermission = [];
                                $old_permission = old('permission',($user?$user->permissions->pluck('id')->toArray():''));
                                    if(is_array($old_permission)){
                                        foreach($old_permission as $value){
                                            $listPermission[] = (int)$value;
                                        }
                                    }
                                @endphp
                                <label for="permission" class="col-sm-2  control-label">{{ gc_language_render('admin.user.add_permission') }}</label>
                                <div class="col-sm-8">
                                    @if (isset($user['id']) && in_array($user['id'], GC_GUARD_ADMIN))
                                        @if (count($listPermission))
                                            @foreach ($listPermission as $p)
                                                {!! '<span class="badge badge-primary">'.($permissions[$p]??'').'</span>' !!}
                                            @endforeach
                                        @endif
                                    @else
                                        <select class="form-control permission select2"  multiple="multiple" data-placeholder="{{ gc_language_render('admin.user.add_permission') }}" style="width: 100%;" name="permission[]" >
                                            <option value=""></option>
                                            @foreach ($permissions as $k => $v)
                                                <option value="{{ $k }}"  {{ (count($listPermission) && in_array($k, $listPermission))?'selected':'' }}>{{ $v }}</option>
                                            @endforeach
                                        </select>
                                            @if ($errors->has('permission'))
                                                <span class="form-text">
                                                    {{ $errors->first('permission') }}
                                                </span>
                                            @endif
                                    @endif

                                </div>
                            </div>
                        {{-- //select permission --}}
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
</script>

@endpush
