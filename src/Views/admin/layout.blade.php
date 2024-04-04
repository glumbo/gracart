@php
    $styleDefine = 'admin.theme_define.'.config('admin.theme_default');
    $gc_templateFile = 'templates/backend/demo1/';
@endphp
        <!DOCTYPE html>
<html class="wide wow-animation" lang="{{ app()->getLocale() }}">
<!--begin::Head-->
<head>
    <base href="">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" type="text/css"
          href="//fonts.googleapis.com/css?family=Roboto+Condensed:300,400,700%7CLato%7CKalam:300,400,700">
    <link rel="canonical" href="{{ request()->url() }}"/>
    <meta name="description" content="{{ $description??gc_store('description') }}">
    <meta name="keywords" content="{{ $keyword??gc_store('keyword') }}">
    <title>{{$title??gc_store('title')}}</title>
    <link rel="icon" href="{{ gc_file(gc_store('icon', null, 'images/icon.png')) }}" type="image/png" sizes="16x16">
    <meta property="og:image" content="{{ !empty($og_image)?gc_file($og_image):gc_file(gc_store('og_image')) }}"/>
    <meta property="og:url" content="{{ \Request::fullUrl() }}"/>
    <meta property="og:type" content="Website"/>
    <meta property="og:title" content="{{ $title??gc_store('title') }}"/>
    <meta property="og:description" content="{{ $description??gc_store('description') }}"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta property="og:locale" content="en_US"/>
    <meta property="og:type" content="article"/>
    <meta property="og:site_name" content="{{$title??gc_store('title')}}"/>
    {{--    <link rel="shortcut icon" href="assets/media/logos/favicon.ico" />--}}
    <!--begin::Fonts-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700"/>
    <!--end::Fonts-->

    <!--begin::Page Vendor Stylesheets(used by this page)-->
    <link href="{{ gc_file($gc_templateFile.'assets/plugins/custom/fullcalendar/fullcalendar.bundle.css')}}"
          rel="stylesheet" type="text/css"/>
    <link href="{{ gc_file($gc_templateFile.'assets/plugins/custom/datatables/datatables.bundle.css')}}"
          rel="stylesheet" type="text/css"/>
    <!--end::Page Vendor Stylesheets-->
    <!--begin::Global Stylesheets Bundle(used by all pages)-->
    <link href="{{ gc_file($gc_templateFile.'assets/plugins/global/plugins.bundle.css')}}" rel="stylesheet" type="text/css"/>
{{--    <link href="{{ gc_file($gc_templateFile.'assets/plugins/global/plugins.dark.bundle.css')}}" rel="stylesheet" type="text/css"/>--}}
    <link href="{{ gc_file($gc_templateFile.'assets/css/style.bundle.css')}}" rel="stylesheet" type="text/css"/>
{{--    <link href="{{ gc_file($gc_templateFile.'assets/css/style.dark.bundle.css')}}" rel="stylesheet" type="text/css"/>--}}
    <!--end::Global Stylesheets Bundle-->

    <style>
        {!! gc_store_css() !!}
    </style>
    <style>.ie-panel{display: none;background: #212121;padding: 10px 0;box-shadow: 3px 3px 5px 0 rgba(0,0,0,.3);clear: both;text-align:center;position: relative;z-index: 1;} html.ie-10 .ie-panel, html.lt-ie-10 .ie-panel {display: block;}</style>
    @stack('styles')
</head>
<!--end::Head-->
<!--begin::Body-->
<body id="kt_body"
      class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed aside-enabled aside-fixed"
      style="--kt-toolbar-height:55px;--kt-toolbar-height-tablet-and-mobile:55px">
<!--begin::Main-->
<div class="ie-panel">
    <a href="javascript:;">
        <img src="{{ gc_file('/images/warning_bar_0000_us.jpg')}}" height="42" width="820" alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today.">
    </a>
</div>


<!--begin::Root-->
<div class="d-flex flex-column flex-root">
    <!--begin::Page-->
    <div class="page d-flex flex-row flex-column-fluid">
        <!--begin::Aside-->
        <div id="kt_aside" class="aside aside-dark aside-hoverable" data-kt-drawer="true" data-kt-drawer-name="aside"
             data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true"
             data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start"
             data-kt-drawer-toggle="#kt_aside_mobile_toggle">
            <!--begin::Brand-->
            <div class="aside-logo flex-column-auto" id="kt_aside_logo">
                <!--begin::Logo-->
                <a href="{{ gc_route_admin('home') }}">
                    <img alt="Logo" src="{{ gc_file($gc_templateFile.'assets/media/logos/logo-1-dark.svg')}}" class="h-25px logo"/>
                </a>
                <!--end::Logo-->
                <!--begin::Aside toggler-->
                @includeIf($templatePathAdmin.'component.tools.aside-toggle')
                <!--end::Aside toggler-->
            </div>
            <!--end::Brand-->
            <!--begin::Aside menu-->
            @section('block_sidebar')
                @includeIf($templatePathAdmin.'component.tools.aside-menu')
            @show
            <!--end::Aside menu-->
            <!--begin::Footer-->
            @includeIf($templatePathAdmin.'component.tools.aside-footer')
            <!--end::Footer-->
        </div>
        <!--end::Aside-->
        <!--begin::Wrapper-->
        <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
            <!--begin::Header-->
            <div id="kt_header" style="" class="header align-items-stretch">
                <!--begin::Container-->
                <div class="container-fluid d-flex align-items-stretch justify-content-between">
                    <!--begin::Aside mobile toggle-->
                    @includeIf($templatePathAdmin.'component.tools.mobile-toggle')
                    <!--end::Aside mobile toggle-->
                    <!--begin::Mobile logo-->
                    <div class="d-flex align-items-center flex-grow-1 flex-lg-grow-0">
                        @includeIf($templatePathAdmin.'component.tools.mobile-logo')
                    </div>
                    <!--end::Mobile logo-->
                    <!--begin::Wrapper-->
                    <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1">
                        <!--begin::Navbar-->
                        <div class="d-flex align-items-stretch" id="kt_header_nav">
                            <!--begin::Menu wrapper-->
{{--                                @includeIf($templatePathAdmin.'component.tools.header-menu')--}}
                            <!--end::Menu wrapper-->
                        </div>
                        <!--end::Navbar-->
                        <!--begin::Toolbar wrapper-->
                        @section('block_header')
                            @includeIf($templatePathAdmin.'component.tools.toolbar')
                        @show
                        <!--end::Toolbar wrapper-->
                    </div>
                    <!--end::Wrapper-->
                </div>
                <!--end::Container-->
            </div>
            <!--end::Header-->
            <!--begin::Content-->
            <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                <!--begin::Toolbar-->
                <div class="toolbar" id="kt_toolbar">
                    <!--begin::Container-->
                    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
                        <!--begin::Page title-->
                        <div data-kt-swapper="true" data-kt-swapper-mode="prepend"
                             data-kt-swapper-parent="{default: '#kt_content_container', 'lg': '#kt_toolbar_container'}"
                             class="page-title d-flex align-items-center flex-wrap me-3 mb-5 mb-lg-0">
                            <!--begin::Title-->
                            <h1 class="d-flex text-dark fw-bolder fs-3 align-items-center my-1">
{{--                                <i class="{{ $icon??'' }}" aria-hidden="true"></i>  --}}
                                {!! $title??'' !!}
                                <!--begin::Separator-->
                                <span class="h-20px border-1 border-gray-200 border-start ms-3 mx-2 me-1"></span>
                                <!--end::Separator-->
                                <!--begin::Description-->
                                @if (!empty($subTitle))
                                <span class="text-muted fs-7 fw-bold mt-2">{!! $subTitle !!}</span>
                                @endif
                                <!--end::Description-->
                            </h1>
                            <!--end::Title-->
                            <span class="h-20px border-gray-300 border-start mx-4"></span>
                            <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                                <!--begin::Item-->
                                <li class="breadcrumb-item text-muted">
                                    <a href="{{ gc_route_admin('admin.home') }}" class="text-muted text-hover-primary"><i class="fa fa-home fa-1x"></i> {{ gc_language_render('admin.home') }}</a>
                                </li>
                                <!--end::Item-->
                                <!--begin::Item-->
                                <li class="breadcrumb-item">
                                    <span class="bullet bg-gray-300 w-5px h-2px"></span>
                                </li>
                                <!--end::Item-->

                                @if (!empty($breadcrumb))
                                    <!--begin::Item-->
                                    <li class="breadcrumb-item text-muted">File Manager</li>
                                    <!--end::Item-->
                                    <!--begin::Item-->
                                    <li class="breadcrumb-item">
                                        <span class="bullet bg-gray-300 w-5px h-2px"></span>
                                    </li>
                                @endif

                                <!--end::Item-->
                                <!--begin::Item-->
                                <li class="breadcrumb-item text-dark">{!! $title??'' !!}</li>
                                <!--end::Item-->
                            </ul>
                        </div>
                        <!--end::Page title-->

                    </div>
                    <!--end::Container-->
                </div>
                <!--end::Toolbar-->
                <!--begin::Post-->
                <div class="post d-flex flex-column-fluid" id="kt_post">
                    <!--begin::Container-->
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla ligula sapien, ullamcorper vel nibh in, rutrum tristique justo. Cras rutrum lectus vitae rhoncus interdum. Nunc luctus neque non convallis efficitur. Curabitur tempus sapien et diam imperdiet pellentesque. Pellentesque gravida, mauris in porttitor tincidunt, lectus orci fermentum felis, in pretium leo massa eu velit. Nullam urna elit, bibendum quis varius vitae, maximus id orci. Morbi mi eros, congue dapibus turpis a, convallis suscipit erat. Aliquam pulvinar turpis tellus, sed interdum diam commodo eu. Etiam tempus lacus at diam auctor pretium. Mauris iaculis non justo vel dignissim. Morbi et arcu vitae lectus porta posuere. Nulla vitae urna ac sem ultrices scelerisque sit amet quis felis. Aliquam ullamcorper finibus sapien, non commodo urna. Morbi mauris est, tristique eget dignissim a, facilisis nec erat.

                    Etiam felis nibh, semper et rhoncus sit amet, hendrerit porta lectus. Nulla id dolor sit amet lorem dapibus maximus sit amet sed dui. Integer mi mauris, euismod non mi a, iaculis pharetra arcu. Nullam semper laoreet neque a fringilla. Cras sollicitudin, ipsum id convallis semper, tellus elit ullamcorper lorem, sed tempus est libero quis urna. Duis sed felis posuere, gravida felis quis, suscipit dui. Nullam vitae arcu non nibh vulputate dapibus. Nam cursus erat euismod, accumsan quam vitae, malesuada velit. Praesent eget nunc blandit, vulputate arcu eu, interdum turpis. Quisque tincidunt porta est, eu tincidunt leo vehicula eu. Vivamus sem metus, blandit at ipsum vitae, facilisis facilisis enim. Cras varius dui metus, id aliquam orci dapibus a. Maecenas malesuada enim non imperdiet volutpat.

                    Maecenas blandit, ex ac ultrices condimentum, magna massa facilisis sem, vel posuere lorem sem sit amet diam. Maecenas mollis nunc dui. Vestibulum semper sagittis turpis. Sed lectus libero, convallis a magna et, pellentesque consectetur ex. Ut auctor suscipit convallis. Nunc condimentum leo vitae sapien pretium pellentesque. Quisque consectetur vel lacus vel aliquam. Sed aliquet dui id porta porttitor. Vestibulum libero ligula, placerat eget malesuada sed, interdum vel erat. Morbi efficitur, metus id congue sagittis, turpis lorem placerat urna, rhoncus lacinia dolor orci ac quam. In in gravida eros, ornare sodales sapien. Donec justo nulla, venenatis sed ligula eu, pellentesque sodales elit. Interdum et malesuada fames ac ante ipsum primis in faucibus. In tincidunt in urna a porttitor. Duis condimentum non nibh vitae gravida.

                    Aliquam erat volutpat. Vivamus feugiat nunc id commodo ultrices. In hac habitasse platea dictumst. Quisque pulvinar lorem nunc, vel gravida nulla ultrices vitae. Vestibulum cursus condimentum est ac molestie. Vivamus tristique rutrum urna, vitae interdum lorem rutrum ac. Pellentesque ipsum dolor, congue eu tempor quis, feugiat id magna. Ut aliquam dui turpis, at pretium eros porttitor quis. Suspendisse et efficitur lacus. Integer viverra ullamcorper nulla id accumsan. Praesent tempus porta nunc vitae semper. Ut rutrum, eros in faucibus mattis, mauris ligula maximus leo, venenatis iaculis dui purus at orci.

                    In ac erat imperdiet, consectetur neque eleifend, rhoncus nisi. Etiam sodales odio ut pharetra lobortis. Ut nec tincidunt libero, ut aliquam purus. Aliquam magna augue, blandit sed placerat et, scelerisque scelerisque nisl. Morbi quis feugiat nunc. Etiam vehicula nisi eu nibh imperdiet pulvinar. Nunc commodo leo a magna gravida euismod. Aliquam erat volutpat. Donec lacinia, massa non tempor convallis, neque arcu condimentum turpis, eu porta velit purus sit amet nisi. Interdum et malesuada fames ac ante ipsum primis in faucibus. Nunc non condimentum libero, sed maximus sem. Quisque arcu leo, dignissim in est eget, interdum scelerisque sem. Donec sit amet tempus urna. Etiam cursus diam ac scelerisque faucibus.
                    <div id="kt_content_container" class="container-xxl">
                        <div class="card mb-5 mb-xxl-8">
                            <div class="card-body pt-9 pb-0">
                                <!--begin::Details-->
                                <div class="d-flex flex-wrap flex-sm-nowrap">
                                    <!--begin::Row-->
                                    <div class="row gy-5 g-xl-8">
                                        @yield('main')
                                    </div>
                                    <!--end::Row-->
                                </div>

                            </div>

                        </div>

                    </div>
                    <!--end::Container-->
                </div>
                <!--end::Post-->
            </div>
            <!--end::Content-->
            @section('block_footer')
                @includeIf($templatePathAdmin.'footer')
            @show
        </div>
        <!--end::Wrapper-->
    </div>
    <!--end::Page-->
</div>
<!--end::Root-->

<!--begin::Scrolltop-->
<div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
    <!--begin::Svg Icon | path: icons/duotune/arrows/arr066.svg-->
    <span class="svg-icon">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
					<rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)"
                          fill="currentColor"/>
					<path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z"
                          fill="currentColor"/>
				</svg>
			</span>
    <!--end::Svg Icon-->
</div>
<!--end::Scrolltop-->

<!--begin::Javascript-->
<script>var hostUrl = "{{ gc_file($gc_templateFile.'assets/')}}";</script>
<!--begin::Global Javascript Bundle(used by all pages)-->
<script src="{{ gc_file($gc_templateFile.'assets/plugins/global/plugins.bundle.js')}}"></script>
<script src="{{ gc_file($gc_templateFile.'assets/js/scripts.bundle.js')}}"></script>
<!--end::Global Javascript Bundle-->
<!--begin::Page Vendors Javascript(used by this page)-->
<script src="{{ gc_file($gc_templateFile.'assets/plugins/custom/fullcalendar/fullcalendar.bundle.js')}}"></script>
<script src="{{ gc_file($gc_templateFile.'assets/plugins/custom/datatables/datatables.bundle.js')}}"></script>
<!--end::Page Vendors Javascript-->
<!--begin::Page Custom Javascript(used by this page)-->
<script src="{{ gc_file($gc_templateFile.'assets/js/widgets.bundle.js')}}"></script>
<script src="{{ gc_file($gc_templateFile.'assets/js/custom/widgets.js')}}"></script>
<script src="{{ gc_file($gc_templateFile.'assets/js/custom/apps/chat/chat.js')}}"></script>
<script src="{{ gc_file($gc_templateFile.'assets/js/custom/utilities/modals/upgrade-plan.js')}}"></script>
<script src="{{ gc_file($gc_templateFile.'assets/js/custom/utilities/modals/create-app.js')}}"></script>
<script src="{{ gc_file($gc_templateFile.'assets/js/custom/utilities/modals/users-search.js')}}"></script>
<!--end::Page Custom Javascript-->
<!--end::Javascript-->
</body>
<!--end::Body-->
</html>