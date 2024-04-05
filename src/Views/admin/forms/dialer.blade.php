@php
    if(!isset($name)){
        $name = 'name';
    }
    $rnd = rand(0,9999999999);
@endphp



<div class="w-lg-250px form-group row mb-2 {{ $errors->has($name) ? ' text-red' : '' }}" data-kt-menu="true"
     id="kt_menu_{{ $rnd }}">
    <label for="{{ $id ?? $name.$rnd }}" class="col-sm-2 col-form-label">{{ $label ?? $name }}</label>
    <div class="position-relative  col-sm-8" data-kt-dialer="true" data-kt-dialer-min="50" data-kt-dialer-max="50000" data-kt-dialer-step="100" data-kt-dialer-prefix="$"  data-kt-dialer-decimals="2">
        <!--begin::Decrease control-->
        <button type="button"
                class="btn btn-icon btn-active-color-gray-700 position-absolute translate-middle-y top-50 start-0"
                data-kt-dialer-control="decrease">
            <!--begin::Svg Icon | path: icons/duotune/general/gen042.svg-->
            <span class="svg-icon svg-icon-1">
															<svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                 height="24" viewBox="0 0 24 24" fill="none">
																<rect opacity="0.3" x="2" y="2" width="20" height="20"
                                                                      rx="10" fill="currentColor"/>
																<rect x="6.01041" y="10.9247" width="12" height="2"
                                                                      rx="1" fill="currentColor"/>
															</svg>
														</span>
            <!--end::Svg Icon-->
        </button>
        <!--end::Decrease control-->
        <!--begin::Input control-->
        <input type="text" class="form-control form-control-solid border-0 ps-12" data-kt-dialer-control="input"
               placeholder="Amount" name="budget_setup" readonly="readonly" value="$50"/>
        <!--end::Input control-->
        <!--begin::Increase control-->
        <button type="button"
                class="btn btn-icon btn-active-color-gray-700 position-absolute translate-middle-y top-50 end-0"
                data-kt-dialer-control="increase">
            <!--begin::Svg Icon | path: icons/duotune/general/gen041.svg-->
            <span class="svg-icon svg-icon-1">
															<svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                 height="24" viewBox="0 0 24 24" fill="none">
																<rect opacity="0.3" x="2" y="2" width="20" height="20"
                                                                      rx="10" fill="currentColor"/>
																<rect x="10.8891" y="17.8033" width="12" height="2"
                                                                      rx="1" transform="rotate(-90 10.8891 17.8033)"
                                                                      fill="currentColor"/>
																<rect x="6.01041" y="10.9247" width="12" height="2"
                                                                      rx="1" fill="currentColor"/>
															</svg>
														</span>
            <!--end::Svg Icon-->
        </button>
        <!--end::Increase control-->
    </div>
</div>


