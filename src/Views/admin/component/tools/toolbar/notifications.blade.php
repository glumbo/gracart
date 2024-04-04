@php
    $countNotice = \Glumbo\Gracart\Admin\Models\AdminNotice::getCountNoticeNew();
    $topNotice = \Glumbo\Gracart\Admin\Models\AdminNotice::getTopNotice();
@endphp
<div class="d-flex align-items-center ms-1 ms-lg-3">
    <!--begin::Menu- wrapper-->
    <div class="btn btn-icon btn-icon-muted btn-active-light btn-active-color-primary w-30px h-30px w-md-40px h-md-40px"
         data-kt-menu-trigger="click" data-kt-menu-attach="parent"
         data-kt-menu-placement="bottom-end">
        <!--begin::Svg Icon | path: icons/duotune/general/gen022.svg-->
        <span class="svg-icon svg-icon-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                 viewBox="0 0 24 24" fill="none">
                <path d="M11.2929 2.70711C11.6834 2.31658 12.3166 2.31658 12.7071 2.70711L15.2929 5.29289C15.6834 5.68342 15.6834 6.31658 15.2929 6.70711L12.7071 9.29289C12.3166 9.68342 11.6834 9.68342 11.2929 9.29289L8.70711 6.70711C8.31658 6.31658 8.31658 5.68342 8.70711 5.29289L11.2929 2.70711Z"
                      fill="currentColor"/>
                <path d="M11.2929 14.7071C11.6834 14.3166 12.3166 14.3166 12.7071 14.7071L15.2929 17.2929C15.6834 17.6834 15.6834 18.3166 15.2929 18.7071L12.7071 21.2929C12.3166 21.6834 11.6834 21.6834 11.2929 21.2929L8.70711 18.7071C8.31658 18.3166 8.31658 17.6834 8.70711 17.2929L11.2929 14.7071Z"
                      fill="currentColor"/>
                <path opacity="0.3"
                      d="M5.29289 8.70711C5.68342 8.31658 6.31658 8.31658 6.70711 8.70711L9.29289 11.2929C9.68342 11.6834 9.68342 12.3166 9.29289 12.7071L6.70711 15.2929C6.31658 15.6834 5.68342 15.6834 5.29289 15.2929L2.70711 12.7071C2.31658 12.3166 2.31658 11.6834 2.70711 11.2929L5.29289 8.70711Z"
                      fill="currentColor"/>
                <path opacity="0.3"
                      d="M17.2929 8.70711C17.6834 8.31658 18.3166 8.31658 18.7071 8.70711L21.2929 11.2929C21.6834 11.6834 21.6834 12.3166 21.2929 12.7071L18.7071 15.2929C18.3166 15.6834 17.6834 15.6834 17.2929 15.2929L14.7071 12.7071C14.3166 12.3166 14.3166 11.6834 14.7071 11.2929L17.2929 8.70711Z"
                      fill="currentColor"/>
            </svg>
        </span>
        <!--end::Svg Icon-->
    </div>
    <!--begin::Menu-->
    <div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px"
         data-kt-menu="true">
        <!--begin::Heading-->
        <div class="d-flex flex-column bgi-no-repeat rounded-top"
             style="background-image:url('{{ gc_file($gc_templateFile.'assets/media/misc/pattern-1.jpg')}}')">
            <!--begin::Title-->
            <h3 class="text-white fw-bold px-9 mt-10 mb-6">{{ gc_language_render('admin_notice.notifications') }}
                <span class="fs-8 opacity-75 ps-3">{{ $countNotice }}</span></h3>
            <!--end::Title-->
            <!--begin::Tabs-->
            <ul class="nav nav-line-tabs nav-line-tabs-2x nav-stretch fw-bold px-9">
                <li class="nav-item">
                    <a class="nav-link text-white opacity-75 opacity-state-100 pb-4 active"
                       data-bs-toggle="tab" href="#kt_topbar_notifications_1">{{ gc_language_render('admin_notice.alerts') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white opacity-75 opacity-state-100 pb-4"
                       data-bs-toggle="tab" href="#kt_topbar_notifications_2">{{ gc_language_render('admin_notice.logs') }}</a>
                </li>
{{--                <li class="nav-item">--}}
{{--                    <a class="nav-link text-white opacity-75 opacity-state-100 pb-4"--}}
{{--                       data-bs-toggle="tab" href="#kt_topbar_notifications_3">{{ gc_language_render('admin_notice.updates') }}</a>--}}
{{--                </li>--}}
            </ul>
            <!--end::Tabs-->
        </div>
        <!--end::Heading-->
        <!--begin::Tab content-->
        <div class="tab-content">
            <!--begin::Tab panel-->
            <div class="tab-pane fade" id="kt_topbar_notifications_1" role="tabpanel">
                @if ($topNotice->count())
                    <div class="scroll-y mh-325px my-5 px-8">
                        @foreach ($topNotice as $notice)
                            @if (in_array($notice->type, ['gc_order_created', 'gc_order_success', 'gc_order_update_status']))
                            <!--begin::Item-->
                            <div class="d-flex flex-stack py-4">
                                <!--begin::Section-->
                                <div class="d-flex align-items-center">
                                    <!--begin::Symbol-->
                                    <div class="symbol symbol-35px me-4">
                                        <span class="symbol-label bg-light-danger">
                                            <!--begin::Svg Icon | path: icons/duotune/general/gen044.svg-->
                                            <span class="svg-icon svg-icon-2 svg-icon-danger">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     width="24" height="24"
                                                     viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.3" x="2" y="2"
                                                          width="20" height="20" rx="10"
                                                          fill="currentColor"/>
                                                    <rect x="11" y="14" width="7" height="2"
                                                          rx="1"
                                                          transform="rotate(-90 11 14)"
                                                          fill="currentColor"/>
                                                    <rect x="11" y="17" width="2" height="2"
                                                          rx="1"
                                                          transform="rotate(-90 11 17)"
                                                          fill="currentColor"/>
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                        </span>
                                    </div>
                                    <!--end::Symbol-->
                                    <!--begin::Title-->
                                    <div class="mb-0 me-2">
                                        <a href="#"
                                           class="fs-6 text-gray-800 text-hover-primary fw-bolder">{{ gc_language_render($notice->content) }}</a>
                                        <div class="text-gray-400 fs-7">{{ gc_language_render($notice->type) }}
                                        </div>
                                    </div>
                                    <!--end::Title-->
                                </div>
                                <!--end::Section-->
                                <!--begin::Label-->
                                <span class="badge badge-light fs-8">{{ gc_datetime_to_date($notice->created_at, 'Y-m-d H:i:s') }}</span>
                                <!--end::Label-->
                            </div>
                            <!--end::Item-->
                            @endif
                        @endforeach
                    </div>
                    <!--begin::View more-->
                    <div class="py-3 text-center border-top">
                        <a href="{{ gc_route_admin('admin_notice.index') }}"
                           class="btn btn-color-gray-600 btn-active-color-primary">{{ gc_language_render('action.view_more') }}
                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
                            <span class="svg-icon svg-icon-5">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                 height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="18" y="13" width="13" height="2"
                                      rx="1" transform="rotate(-180 18 13)"
                                      fill="currentColor"/>
                                <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z"
                                      fill="currentColor"/>
                            </svg>
                        </span>
                            <!--end::Svg Icon--></a>
                    </div>
                    <!--end::View more-->
                @else
                    <div class="py-3 text-center border-top">
                        <div class="pt-10 pb-0">
                            <h3 class="text-dark text-center fw-bolder">{{ gc_language_render('admin_notice.empty') }}</h3>
                        </div>
                    </div>
                @endif
            </div>
            <!--end::Tab panel-->
            <!--begin::Tab panel-->
            <div class="tab-pane fade show active" id="kt_topbar_notifications_2"
                 role="tabpanel">
                @if ($topNotice->count())
                    <div class="scroll-y mh-325px my-5 px-8">
                        @foreach ($topNotice as $notice)
                            @if (in_array($notice->type, ['gc_customer_created']))
                                <!--begin::Item-->
                                <div class="d-flex flex-stack py-4">
                                    <!--begin::Section-->
                                    <div class="d-flex align-items-center">
                                        <!--begin::Symbol-->
                                        <div class="symbol symbol-35px me-4">
                                        <span class="symbol-label bg-light-danger">
                                            <!--begin::Svg Icon | path: icons/duotune/general/gen044.svg-->
                                            <span class="svg-icon svg-icon-2 svg-icon-danger">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     width="24" height="24"
                                                     viewBox="0 0 24 24" fill="none">
                                                    <rect opacity="0.3" x="2" y="2"
                                                          width="20" height="20" rx="10"
                                                          fill="currentColor"/>
                                                    <rect x="11" y="14" width="7" height="2"
                                                          rx="1"
                                                          transform="rotate(-90 11 14)"
                                                          fill="currentColor"/>
                                                    <rect x="11" y="17" width="2" height="2"
                                                          rx="1"
                                                          transform="rotate(-90 11 17)"
                                                          fill="currentColor"/>
                                                </svg>
                                            </span>
                                            <!--end::Svg Icon-->
                                        </span>
                                        </div>
                                        <!--end::Symbol-->
                                        <!--begin::Title-->
                                        <div class="mb-0 me-2">
                                            <a href="#"
                                               class="fs-6 text-gray-800 text-hover-primary fw-bolder">{{ gc_language_render($notice->content) }}</a>
                                            <div class="text-gray-400 fs-7">{{ gc_language_render($notice->type) }}
                                            </div>
                                        </div>
                                        <!--end::Title-->
                                    </div>
                                    <!--end::Section-->
                                    <!--begin::Label-->
                                    <span class="badge badge-light fs-8">{{ gc_datetime_to_date($notice->created_at, 'Y-m-d H:i:s') }}</span>
                                    <!--end::Label-->
                                </div>
                                <!--end::Item-->
                            @endif
                        @endforeach
                    </div>
                    <!--begin::View more-->
                    <div class="py-3 text-center border-top">
                        <a href="{{ gc_route_admin('admin_notice.index') }}"
                           class="btn btn-color-gray-600 btn-active-color-primary">{{ gc_language_render('action.view_more') }}
                            <!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
                            <span class="svg-icon svg-icon-5">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                 height="24" viewBox="0 0 24 24" fill="none">
                                <rect opacity="0.5" x="18" y="13" width="13" height="2"
                                      rx="1" transform="rotate(-180 18 13)"
                                      fill="currentColor"/>
                                <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z"
                                      fill="currentColor"/>
                            </svg>
                        </span>
                            <!--end::Svg Icon--></a>
                    </div>
                    <!--end::View more-->
                @else
                    <div class="py-3 text-center border-top">
                        <div class="pt-10 pb-0">
                            <h3 class="text-dark text-center fw-bolder">{{ gc_language_render('admin_notice.empty') }}</h3>
                        </div>
                    </div>
                @endif
            </div>
            <!--end::Tab panel-->
            <!--begin::Tab panel-->
            <div class="tab-pane fade" id="kt_topbar_notifications_3" role="tabpanel">
                <!--begin::Items-->
                <div class="scroll-y mh-325px my-5 px-8">
                    <!--begin::Item-->
                    <div class="d-flex flex-stack py-4">
                        <!--begin::Section-->
                        <div class="d-flex align-items-center me-2">
                            <!--begin::Code-->
                            <span class="w-70px badge badge-light-success me-4">200 OK</span>
                            <!--end::Code-->
                            <!--begin::Title-->
                            <a href="#" class="text-gray-800 text-hover-primary fw-bold">New
                                order</a>
                            <!--end::Title-->
                        </div>
                        <!--end::Section-->
                        <!--begin::Label-->
                        <span class="badge badge-light fs-8">Just now</span>
                        <!--end::Label-->
                    </div>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <div class="d-flex flex-stack py-4">
                        <!--begin::Section-->
                        <div class="d-flex align-items-center me-2">
                            <!--begin::Code-->
                            <span class="w-70px badge badge-light-danger me-4">500 ERR</span>
                            <!--end::Code-->
                            <!--begin::Title-->
                            <a href="#" class="text-gray-800 text-hover-primary fw-bold">New
                                customer</a>
                            <!--end::Title-->
                        </div>
                        <!--end::Section-->
                        <!--begin::Label-->
                        <span class="badge badge-light fs-8">2 hrs</span>
                        <!--end::Label-->
                    </div>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <div class="d-flex flex-stack py-4">
                        <!--begin::Section-->
                        <div class="d-flex align-items-center me-2">
                            <!--begin::Code-->
                            <span class="w-70px badge badge-light-success me-4">200 OK</span>
                            <!--end::Code-->
                            <!--begin::Title-->
                            <a href="#" class="text-gray-800 text-hover-primary fw-bold">Payment
                                process</a>
                            <!--end::Title-->
                        </div>
                        <!--end::Section-->
                        <!--begin::Label-->
                        <span class="badge badge-light fs-8">5 hrs</span>
                        <!--end::Label-->
                    </div>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <div class="d-flex flex-stack py-4">
                        <!--begin::Section-->
                        <div class="d-flex align-items-center me-2">
                            <!--begin::Code-->
                            <span class="w-70px badge badge-light-warning me-4">300 WRN</span>
                            <!--end::Code-->
                            <!--begin::Title-->
                            <a href="#" class="text-gray-800 text-hover-primary fw-bold">Search
                                query</a>
                            <!--end::Title-->
                        </div>
                        <!--end::Section-->
                        <!--begin::Label-->
                        <span class="badge badge-light fs-8">2 days</span>
                        <!--end::Label-->
                    </div>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <div class="d-flex flex-stack py-4">
                        <!--begin::Section-->
                        <div class="d-flex align-items-center me-2">
                            <!--begin::Code-->
                            <span class="w-70px badge badge-light-success me-4">200 OK</span>
                            <!--end::Code-->
                            <!--begin::Title-->
                            <a href="#" class="text-gray-800 text-hover-primary fw-bold">API
                                connection</a>
                            <!--end::Title-->
                        </div>
                        <!--end::Section-->
                        <!--begin::Label-->
                        <span class="badge badge-light fs-8">1 week</span>
                        <!--end::Label-->
                    </div>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <div class="d-flex flex-stack py-4">
                        <!--begin::Section-->
                        <div class="d-flex align-items-center me-2">
                            <!--begin::Code-->
                            <span class="w-70px badge badge-light-success me-4">200 OK</span>
                            <!--end::Code-->
                            <!--begin::Title-->
                            <a href="#" class="text-gray-800 text-hover-primary fw-bold">Database
                                restore</a>
                            <!--end::Title-->
                        </div>
                        <!--end::Section-->
                        <!--begin::Label-->
                        <span class="badge badge-light fs-8">Mar 5</span>
                        <!--end::Label-->
                    </div>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <div class="d-flex flex-stack py-4">
                        <!--begin::Section-->
                        <div class="d-flex align-items-center me-2">
                            <!--begin::Code-->
                            <span class="w-70px badge badge-light-warning me-4">300 WRN</span>
                            <!--end::Code-->
                            <!--begin::Title-->
                            <a href="#" class="text-gray-800 text-hover-primary fw-bold">System
                                update</a>
                            <!--end::Title-->
                        </div>
                        <!--end::Section-->
                        <!--begin::Label-->
                        <span class="badge badge-light fs-8">May 15</span>
                        <!--end::Label-->
                    </div>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <div class="d-flex flex-stack py-4">
                        <!--begin::Section-->
                        <div class="d-flex align-items-center me-2">
                            <!--begin::Code-->
                            <span class="w-70px badge badge-light-warning me-4">300 WRN</span>
                            <!--end::Code-->
                            <!--begin::Title-->
                            <a href="#" class="text-gray-800 text-hover-primary fw-bold">Server
                                OS update</a>
                            <!--end::Title-->
                        </div>
                        <!--end::Section-->
                        <!--begin::Label-->
                        <span class="badge badge-light fs-8">Apr 3</span>
                        <!--end::Label-->
                    </div>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <div class="d-flex flex-stack py-4">
                        <!--begin::Section-->
                        <div class="d-flex align-items-center me-2">
                            <!--begin::Code-->
                            <span class="w-70px badge badge-light-warning me-4">300 WRN</span>
                            <!--end::Code-->
                            <!--begin::Title-->
                            <a href="#" class="text-gray-800 text-hover-primary fw-bold">API
                                rollback</a>
                            <!--end::Title-->
                        </div>
                        <!--end::Section-->
                        <!--begin::Label-->
                        <span class="badge badge-light fs-8">Jun 30</span>
                        <!--end::Label-->
                    </div>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <div class="d-flex flex-stack py-4">
                        <!--begin::Section-->
                        <div class="d-flex align-items-center me-2">
                            <!--begin::Code-->
                            <span class="w-70px badge badge-light-danger me-4">500 ERR</span>
                            <!--end::Code-->
                            <!--begin::Title-->
                            <a href="#" class="text-gray-800 text-hover-primary fw-bold">Refund
                                process</a>
                            <!--end::Title-->
                        </div>
                        <!--end::Section-->
                        <!--begin::Label-->
                        <span class="badge badge-light fs-8">Jul 10</span>
                        <!--end::Label-->
                    </div>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <div class="d-flex flex-stack py-4">
                        <!--begin::Section-->
                        <div class="d-flex align-items-center me-2">
                            <!--begin::Code-->
                            <span class="w-70px badge badge-light-danger me-4">500 ERR</span>
                            <!--end::Code-->
                            <!--begin::Title-->
                            <a href="#" class="text-gray-800 text-hover-primary fw-bold">Withdrawal
                                process</a>
                            <!--end::Title-->
                        </div>
                        <!--end::Section-->
                        <!--begin::Label-->
                        <span class="badge badge-light fs-8">Sep 10</span>
                        <!--end::Label-->
                    </div>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <div class="d-flex flex-stack py-4">
                        <!--begin::Section-->
                        <div class="d-flex align-items-center me-2">
                            <!--begin::Code-->
                            <span class="w-70px badge badge-light-danger me-4">500 ERR</span>
                            <!--end::Code-->
                            <!--begin::Title-->
                            <a href="#" class="text-gray-800 text-hover-primary fw-bold">Mail
                                tasks</a>
                            <!--end::Title-->
                        </div>
                        <!--end::Section-->
                        <!--begin::Label-->
                        <span class="badge badge-light fs-8">Dec 10</span>
                        <!--end::Label-->
                    </div>
                    <!--end::Item-->
                </div>
                <!--end::Items-->
                <!--begin::View more-->
                <div class="py-3 text-center border-top">
                    <a href="../../demo1/dist/pages/user-profile/activity.html"
                       class="btn btn-color-gray-600 btn-active-color-primary">View All
                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr064.svg-->
                        <span class="svg-icon svg-icon-5">
															<svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                 height="24" viewBox="0 0 24 24" fill="none">
																<rect opacity="0.5" x="18" y="13" width="13" height="2"
                                                                      rx="1" transform="rotate(-180 18 13)"
                                                                      fill="currentColor"/>
																<path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z"
                                                                      fill="currentColor"/>
															</svg>
														</span>
                        <!--end::Svg Icon--></a>
                </div>
                <!--end::View more-->
            </div>
            <!--end::Tab panel-->
        </div>
        <!--end::Tab content-->
    </div>
    <!--end::Menu-->
    <!--end::Menu wrapper-->
</div>