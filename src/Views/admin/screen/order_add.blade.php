@extends($templatePathAdmin.'layout')

@section('main')
   <div class="row">
      <div class="col-md-12">
         <div class="card">
                <div class="card-header with-border">
                    <h2 class="card-title">{{ $title_description??'' }}</h2>

                    <div class="card-tools">
                        <div class="btn-group float-right" style="margin-right: 5px">
                            <a href="{{ gc_route_admin('admin_order.index') }}" class="btn  btn-flat btn-default" title="List"><i class="fa fa-list"></i><span class="hidden-xs"> {{ gc_language_render('admin.back_list') }}</span></a>
                        </div>
                    </div>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form action="{{ gc_route_admin('admin_order.create') }}" method="post" accept-charset="UTF-8" class="form-horizontal" id="form-main">

                    <div class="card-body">

                            @includeIf($templatePathAdmin.'forms.select', ['name' => 'customer_id', 'id' => 'customer_id', 'options' => $users, 'placeholder' => gc_language_render('order.admin.select_customer'), 'label' => gc_language_render('order.admin.select_customer'), 'add_url' => gc_route_admin('admin_customer.index')])
                            @includeIf($templatePathAdmin.'forms.input', ['name' => 'email', 'type' => 'email', 'label' => gc_language_render('order.email'), 'prepend' => 'envelope'])
                            @includeIf($templatePathAdmin.'forms.input', ['name' => 'first_name', 'label' => gc_language_render('order.first_name')])
                            @if (gc_config_admin('customer_lastname'))
                                @includeIf($templatePathAdmin.'forms.input', ['name' => 'last_name', 'label' => gc_language_render('order.last_name')])
                            @endif
                            @if (gc_config_admin('customer_name_kana'))
                                @includeIf($templatePathAdmin.'forms.input', ['name' => 'first_name_kana', 'label' => gc_language_render('order.first_name_kana')])
                                @includeIf($templatePathAdmin.'forms.input', ['name' => 'last_name_kana', 'label' => gc_language_render('order.last_name_kana')])
                            @endif
                            @if (gc_config_admin('customer_company'))
                                @includeIf($templatePathAdmin.'forms.input', ['name' => 'company', 'label' => gc_language_render('order.company')])
                            @endif
                            @if (gc_config_admin('customer_postcode'))
                                @includeIf($templatePathAdmin.'forms.input', ['name' => 'postcode', 'label' => gc_language_render('order.postcode')])
                            @endif
                            @includeIf($templatePathAdmin.'forms.input', ['name' => 'address1', 'label' => gc_language_render('order.address1')])
                            @if (gc_config_admin('customer_address2'))
                                @includeIf($templatePathAdmin.'forms.input', ['name' => 'address2', 'label' => gc_language_render('order.address2')])
                            @endif
                            @if (gc_config_admin('customer_address3'))
                                @includeIf($templatePathAdmin.'forms.input', ['name' => 'address3', 'label' => gc_language_render('order.address3')])
                            @endif
                            @if (gc_config_admin('customer_phone'))
                                @includeIf($templatePathAdmin.'forms.input', ['name' => 'phone', 'label' => gc_language_render('order.phone'), 'prepend' => 'phone'])
                            @endif
                            @if (gc_config_admin('customer_country'))
                                @includeIf($templatePathAdmin.'forms.select', ['name' => 'country', 'options' => $countries, 'label' => gc_language_render('order.country')])
                            @endif

                            @includeIf($templatePathAdmin.'forms.select', ['name' => 'currency', 'options' => $currencies, 'label' => gc_language_render('order.currency')])
                            @includeIf($templatePathAdmin.'forms.input', ['name' => 'exchange_rate', 'label' => gc_language_render('order.exchange_rate')])
                            @includeIf($templatePathAdmin.'forms.textarea', ['name' => 'comment', 'label' => gc_language_render('order.note')])

                            @includeIf($templatePathAdmin.'forms.select', ['name' => 'payment_method', 'options' => $paymentMethod, 'label' => gc_language_render('order.payment_method')])
                            @includeIf($templatePathAdmin.'forms.select', ['name' => 'shipping_method', 'options' => $shippingMethod, 'label' => gc_language_render('order.shipping_method')])
                            @includeIf($templatePathAdmin.'forms.select', ['name' => 'status', 'options' => $orderStatus, 'label' => gc_language_render('order.status')])


                            <hr>

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

$(document).ready(function() {
//Initialize Select2 Elements
$('.select2').select2()
});
$('[name="customer_id"]').change(function(){
    addInfo();
});
$('[name="currency"]').change(function(){
    addExchangeRate();
});

function addExchangeRate(){
    var currency = $('[name="currency"]').val();
    var jsonCurrency = {!!$currenciesRate !!};
    $('[name="exchange_rate"]').val(jsonCurrency[currency]);
}

function addInfo(){
    id = $('[name="customer_id"]').val();
    if(id){
       $.ajax({
            url : '{{ gc_route_admin('admin_order.user_info') }}',
            type : "get",
            dateType:"application/json; charset=utf-8",
            data : {
                 id : id
            },
            beforeSend: function(){
                $('#loading').show();
            },
            success: function(result){
                var returnedData = JSON.parse(result);
                $('[name="email"]').val(returnedData.email);
                $('[name="first_name"]').val(returnedData.first_name);
                $('[name="last_name"]').val(returnedData.last_name);
                $('[name="first_name_kana"]').val(returnedData.first_name_kana);
                $('[name="last_name_kana"]').val(returnedData.last_name_kana);
                $('[name="address1"]').val(returnedData.address1);
                $('[name="address2"]').val(returnedData.address2);
                $('[name="address3"]').val(returnedData.address3);
                $('[name="phone"]').val(returnedData.phone);
                $('[name="company"]').val(returnedData.company);
                $('[name="postcode"]').val(returnedData.postcode);
                $('[name="country"]').val(returnedData.country).change();
                $('#loading').hide();
            }
        });
       }else{
            $('#form-main').reset();
       }

}

</script>
@endpush
