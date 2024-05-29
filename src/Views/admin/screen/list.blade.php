@extends($templatePathAdmin.'layout')

@section('main')
    <div class="row">
        <div class="card mb-5 mb-xl-8">
            <!--begin::Header-->
            <div class="card-header border-0 pt-5">
                <h3 class="card-title align-items-start flex-column">
                    <span class="card-label fw-bolder fs-3 mb-1">{!! $title??'' !!}</span>
                    @if (!empty($subTitle))
                        <span class="text-muted mt-1 fw-bold fs-7">{!! $subTitle !!}</span>
                    @endif
                </h3>
                <div class="card-toolbar">
                    @if (!empty($menuRight) && count($menuRight))
                        @foreach ($menuRight as $item)
                            <div class="menu-right">
                                @php
                                    $arrCheck = explode('view::', $item);
                                @endphp
                                @if (count($arrCheck) == 2)
                                    @if (view()->exists($arrCheck[1]))
                                        @include($arrCheck[1])
                                    @endif
                                @else
                                    {!! trim($item) !!}
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
            <div class="card-header border-0 pt-5">
                <div class="card-title align-items-start flex-column">
                    <div class="float-start">
                        @if (!empty($removeList))
{{--                            <div class="menu-left">--}}
{{--                                <button type="button" class="btn btn-default grid-select-all"><i class="far fa-square"></i></button>--}}
{{--                            </div>--}}
                            <div class="menu-left">
                                <span class="btn btn-sm btn-light btn-active-danger grid-trash" title="{{ gc_language_render('action.delete') }}"><i class="fas fa-trash-alt"></i></span>
                            </div>
                        @endif

                        @if (!empty($buttonRefresh))
                            <div class="menu-left">
                                <span class="btn btn-sm btn-light btn-active-primary grid-refresh" title="{{ gc_language_render('action.refresh') }}"><i class="fas fa-sync-alt"></i></span>
                            </div>
                        @endif

                        @if (!empty($menuLeft) && count($menuLeft))
                            @foreach ($menuLeft as $item)
                                <div class="menu-left">
                                    @php
                                        $arrCheck = explode('view::', $item);
                                    @endphp
                                    @if (count($arrCheck) == 2)
                                        @if (view()->exists($arrCheck[1]))
                                            @include($arrCheck[1])
                                        @endif
                                    @else
                                        {!! trim($item) !!}
                                    @endif
                                </div>
                            @endforeach
                        @endif

                    </div>
                </div>
                <div class="card-toolbar align-items-end float-end">
                    @if (!empty($topMenuRight) && count($topMenuRight))
                        @foreach ($topMenuRight as $item)
                            <div class="menu-right">
                                @php
                                    $arrCheck = explode('view::', $item);
                                @endphp
                                @if (count($arrCheck) == 2)
                                    @if (view()->exists($arrCheck[1]))
                                        @include($arrCheck[1])
                                    @endif
                                @else
                                    {!! trim($item) !!}
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
            <!--end::Header-->
            <!--begin::Body-->
            <div class="card-body py-3">
                <!--begin::Table container-->
                <div class="table-responsive">
                    <!--begin::Table-->
                    <table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3">
                        <!--begin::Table head-->
                        <thead>
                        <tr class="fw-bolder text-muted">
                            @if (!empty($removeList))
                                <th class="w-25px">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" value="1" data-kt-check="true"
                                               data-kt-check-target=".widget-13-check">
                                    </div>
                                </th>
                            @endif
                            @foreach ($listTh as $key => $th)
                                <th>{!! $th !!}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <!--end::Table head-->
                        <!--begin::Table body-->
                        <tbody>
                        @foreach ($dataTr as $keyRow => $tr)
                            <tr>
                                @if (!empty($removeList))
                                    <td>
                                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                                            <input class="form-check-input widget-13-check" type="checkbox" data-id="{{ $keyRow }}">
                                        </div>
                                    </td>
                                @endif
                                @foreach ($tr as $key => $trtd)
                                    <td>{!! $trtd !!}</td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>

                        <!--end::Table body-->
                    </table>
                    <!--end::Table-->
                </div>
                <!--end::Table container-->
            </div>
            <!--begin::Body-->
        </div>
    </div>
@endsection


@push('styles')
    {!! $css ?? '' !!}
@endpush

@push('scripts')
    {{-- //Pjax --}}
    <script src="{{ gc_file('admin/plugin/jquery.pjax.js')}}"></script>

    <script type="text/javascript">

        $('.grid-refresh').click(function () {
            $.pjax.reload({container: '#pjax-container'});
        });

        $(document).on('submit', '#button_search', function (event) {
            $.pjax.submit(event, '#pjax-container')
        })

        $(document).on('pjax:send', function () {
            $('#loading').show()
        })
        $(document).on('pjax:complete', function () {
            $('#loading').hide()
        })

        // tag a
        $(function () {
            $(document).pjax('a.page-link', '#pjax-container')
        })


        $(document).ready(function () {
            // does current browser support PJAX
            if ($.support.pjax) {
                $.pjax.defaults.timeout = 2000; // time in milliseconds
            }
        });

    </script>
    {{-- //End pjax --}}


    <script type="text/javascript">
        {{-- sweetalert2 --}}
        var selectedRows = function () {
            var selected = [];
            $('.grid-row-checkbox:checked').each(function () {
                selected.push($(this).data('id'));
            });

            return selected;
        }

        $('.grid-trash').on('click', function () {
            var ids = selectedRows().join();
            deleteItem(ids);
        });

        function deleteItem(ids) {
            Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: true,
            }).fire({
                title: '{{ gc_language_render('action.delete_confirm') }}',
                text: "",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ gc_language_render('action.confirm_yes') }}',
                confirmButtonColor: "#DD6B55",
                cancelButtonText: '{{ gc_language_render('action.confirm_no') }}',
                reverseButtons: true,

                preConfirm: function () {
                    return new Promise(function (resolve) {
                        $.ajax({
                            method: 'post',
                            url: '{{ $urlDeleteItem ?? '' }}',
                            data: {
                                ids: ids,
                                _token: '{{ csrf_token() }}',
                            },
                            success: function (data) {
                                if (data.error == 1) {
                                    alertMsg('error', data.msg, '{{ gc_language_render('action.warning') }}');
                                    $.pjax.reload('#pjax-container');

                                } else {
                                    alertMsg('success', data.msg);
                                    $.pjax.reload('#pjax-container');
                                    resolve(data);
                                }

                            }
                        });
                    });
                }

            }).then((result) => {
                if (result.value) {
                    alertMsg('success', '{{ gc_language_render('action.delete_confirm_deleted_msg') }}', '{{ gc_language_render('action.delete_confirm_deleted') }}');
                } else if (
                    // Read more about handling dismissals
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    // swalWithBootstrapButtons.fire(
                    //   'Cancelled',
                    //   'Your imaginary file is safe :)',
                    //   'error'
                    // )
                }
            })
        }


        function cloneProduct(id) {
            Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: true,
            }).fire({
                title: '{{ gc_language_render('product.admin.clone_confirm') }}',
                text: "",
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ gc_language_render('action.confirm_yes') }}',
                confirmButtonColor: "#DD6B55",
                cancelButtonText: '{{ gc_language_render('action.confirm_no') }}',
                reverseButtons: true,

                preConfirm: function () {
                    return new Promise(function (resolve) {
                        $.ajax({
                            method: 'post',
                            url: '{{ gc_route_admin('admin_product.clone') }}',
                            data: {
                                pId: id,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (data) {
                                if (data.error == 1) {
                                    alertMsg('error', data.msg, '{{ gc_language_render('action.warning') }}');
                                    $.pjax.reload('#pjax-container');

                                } else {
                                    alertMsg('success', data.msg);
                                    $.pjax.reload('#pjax-container');
                                    resolve(data);
                                }

                            }
                        });
                    });
                }

            }).then((result) => {
                if (result.value) {
                    alertMsg('success', '{{ gc_language_render('product.admin.clone_success') }}', '');
                } else if (
                    // Read more about handling dismissals
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    // swalWithBootstrapButtons.fire(
                    //   'Cancelled',
                    //   'Your imaginary file is safe :)',
                    //   'error'
                    // )
                }
            })
        }

        {{--/ sweetalert2 --}}


    </script>

    {!! $js ?? '' !!}
@endpush
