<?php
namespace Glumbo\Gracart\Admin\Controllers;

use Glumbo\Gracart\Admin\Admin;
use Glumbo\Gracart\Admin\Controllers\RootAdminController;
use Glumbo\Gracart\Front\Models\ShopAttributeGroup;
use Glumbo\Gracart\Front\Models\ShopCountry;
use Glumbo\Gracart\Front\Models\ShopCurrency;
use Glumbo\Gracart\Front\Models\ShopOrderDetail;
use Glumbo\Gracart\Front\Models\ShopOrderStatus;
use Glumbo\Gracart\Front\Models\ShopPaymentStatus;
use Glumbo\Gracart\Front\Models\ShopShippingStatus;
use Glumbo\Gracart\Admin\Models\AdminCustomer;
use Glumbo\Gracart\Admin\Models\AdminOrder;
use Glumbo\Gracart\Admin\Models\AdminProduct;
use Glumbo\Gracart\Front\Models\ShopOrderTotal;
use Validator;

class AdminOrderController extends RootAdminController
{
    public $statusPayment;
    public $statusOrder;
    public $statusShipping;
    public $statusOrderMap;
    public $statusShippingMap;
    public $statusPaymentMap;
    public $currency;
    public $country;
    public $countryMap;

    public function __construct()
    {
        parent::__construct();
        $this->statusOrder    = ShopOrderStatus::getIdAll();
        $this->currency       = ShopCurrency::getListActive();
        $this->country        = ShopCountry::getCodeAll();
        $this->statusPayment  = ShopPaymentStatus::getIdAll();
        $this->statusShipping = ShopShippingStatus::getIdAll();
    }

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        $data = [
            'title'         => gc_language_render('order.admin.list'),
            'subTitle'      => '',
            'icon'          => 'fa fa-indent',
            'urlDeleteItem' => gc_route_admin('admin_order.delete'),
            'removeList'    => 1, // 1 - Enable function delete list item
            'buttonRefresh' => 1, // 1 - Enable button refresh
            'css'           => '',
            'js'            => '',
        ];

        //Process add content
        $data['menuRight']    = gc_config_group('menuRight', \Request::route()->getName());
        $data['menuLeft']     = gc_config_group('menuLeft', \Request::route()->getName());
        $data['topMenuRight'] = gc_config_group('topMenuRight', \Request::route()->getName());
        $data['topMenuLeft']  = gc_config_group('topMenuLeft', \Request::route()->getName());
        $data['blockBottom']  = gc_config_group('blockBottom', \Request::route()->getName());

        $listTh = [
            'email'          => '<i class="fas fa-envelope" aria-hidden="true" title="'.gc_language_render('order.email').'"></i>',
            'subtotal'       => '<i class="fa fa-shopping-cart" aria-hidden="true" title="'.gc_language_render('order.subtotal').'"></i>',
            'shipping'       => '<i class="fa fa-truck" aria-hidden="true" title="'.gc_language_render('order.shipping').'"></i>',
            'discount'       => '<i class="fa fa-tags" aria-hidden="true" title="'.gc_language_render('order.discount').'"></i>',
            'tax'            => gc_language_render('order.tax'),
            'total'          => '<i class="fas fa-coins" aria-hidden="true" title="'.gc_language_render('order.total').'"></i>',
            'payment_method' => '<i class="fa fa-credit-card" aria-hidden="true" title="'.gc_language_render('order.admin.payment_method_short').'"></i>',
            'status'         => gc_language_render('order.admin.status'),
        ];
        if (gc_check_multi_shop_installed() && session('adminStoreId') == GC_ID_ROOT) {
            // Only show store info if store is root
            $listTh['shop_store'] = '<i class="fab fa-shopify" aria-hidden="true" title="'.gc_language_render('front.store_list').'"></i>';
        }
        $listTh['created_at'] = gc_language_render('admin.created_at');
        $listTh['action'] = gc_language_render('action.title');

        $sort_order   = gc_clean(request('sort_order') ?? 'id_desc');
        $keyword      = gc_clean(request('keyword') ?? '');
        $email        = gc_clean(request('email') ?? '');
        $from_to      = gc_clean(request('from_to') ?? '');
        $end_to       = gc_clean(request('end_to') ?? '');
        $order_status = gc_clean(request('order_status') ?? '');
        $arrSort = [
            'id__desc'         => gc_language_render('filter_sort.id_desc'),
            'id__asc'          => gc_language_render('filter_sort.id_asc'),
            'email__desc'      => gc_language_render('filter_sort.alpha_desc', ['alpha' => 'Email']),
            'email__asc'       => gc_language_render('filter_sort.alpha_asc', ['alpha' => 'Email']),
            'created_at__desc' => gc_language_render('filter_sort.value_desc', ['value' => 'Date']),
            'created_at__asc'  => gc_language_render('filter_sort.value_asc', ['value' => 'Date']),
        ];
        $dataSearch = [
            'keyword'      => $keyword,
            'email'        => $email,
            'from_to'      => $from_to,
            'end_to'       => $end_to,
            'sort_order'   => $sort_order,
            'arrSort'      => $arrSort,
            'order_status' => $order_status,
        ];
        $dataTmp = (new AdminOrder)->getOrderListAdmin($dataSearch);
        if (gc_check_multi_shop_installed() && session('adminStoreId') == GC_ID_ROOT) {
            $arrId = $dataTmp->pluck('id')->toArray();
            // Only show store info if store is root
            if (function_exists('gc_get_list_store_of_order')) {
                $dataStores = gc_get_list_store_of_order($arrId);
            } else {
                $dataStores = [];
            }
        }

        $styleStatus = $this->statusOrder;
        array_walk($styleStatus, function (&$v, $k) {
            $v = '<span class="badge badge-' . (AdminOrder::$mapStyleStatus[$k] ?? 'light') . '">' . $v . '</span>';
        });
        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            if($row['currency'] == 0){
                $row['currency'] = 'USD';
            }
            $dataMap = [
                'email'          => $row['email'] ?? 'N/A',
                'subtotal'       => gc_currency_render_symbol($row['subtotal'] ?? 0, $row['currency']),
                'shipping'       => gc_currency_render_symbol($row['shipping'] ?? 0, $row['currency']),
                'discount'       => gc_currency_render_symbol($row['discount'] ?? 0, $row['currency']),
                'tax'            => gc_currency_render_symbol($row['tax'] ?? 0, $row['currency']),
                'total'          => gc_currency_render_symbol($row['total'] ?? 0, $row['currency']),
                'payment_method' => $row['payment_method'].'<br>('.$row['currency'] . '/' . $row['exchange_rate'].')',
                'status'         => $styleStatus[$row['status']] ?? $row['status'],
            ];
            if (gc_check_multi_shop_installed() && session('adminStoreId') == GC_ID_ROOT) {
                // Only show store info if store is root
                if (!empty($dataStores[$row['id']])) {
                    $storeTmp = $dataStores[$row['id']]->pluck('code', 'id')->toArray();
                    $storeTmp = array_map(function ($code) {
                        return '<a target=_new href="'.gc_get_domain_from_code($code).'">'.$code.'</a>';
                    }, $storeTmp);
                    $dataMap['shop_store'] = '<i class="nav-icon fab fa-shopify"></i> '.implode('<br><i class="nav-icon fab fa-shopify"></i> ', $storeTmp);
                } else {
                    $dataMap['shop_store'] = '';
                }
            }

            $dataMap['created_at'] = $row['created_at'];
            $dataMap['action'] = '<a href="' . gc_route_admin('admin_order.detail', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '"><span title="' . gc_language_render('action.edit') . '" type="button" class="btn btn-sm btn-light btn-active-primary"><i class="fa fa-edit"></i></span></a>&nbsp;
            <span onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . gc_language_render('action.delete') . '" class="btn btn-sm btn-light btn-active-danger"><i class="fas fa-trash-alt"></i></span>
            ';
            $dataTr[$row['id']] = $dataMap;
        }
        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin.'component.pagination');
        $data['resultItems'] = gc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' =>  $dataTmp->total()]);


        //menuRight
        $data['menuRight'][] = '<a href="' . gc_route_admin('admin_order.create') . '" class="btn btn-sm btn-light btn-active-primary" title="New" id="button_create_new">
                           <i class="fa fa-plus" title="'.gc_language_render('action.add').'"></i>'.gc_language_render('action.add').'
                           </a>';
        //=menuRight

        //menuSort
        $optionSort = '';
        foreach ($arrSort as $key => $sort) {
            $optionSort .= '<option  ' . (($sort_order == $key) ? "selected" : "") . ' value="' . $key . '">' . $sort . '</option>';
        }
        //=menuSort

        //menuSearch
        $optionStatus = '';
        foreach ($this->statusOrder as $key => $status) {
            $optionStatus .= '<option  ' . (($order_status == $key) ? "selected" : "") . ' value="' . $key . '">' . $status . '</option>';
        }
        $rnd = rand(0,9999999999);
        $data['topMenuRight'][] = '
                <form action="' . gc_route_admin('admin_order.index') . '" id="button_search">
                    <div class="input-group float-left">
                        <div style="width:150px">
                            <div class="form-group">
                                <label>'.gc_language_render('action.sort').':</label>
                                <div class="input-group">
                                    <select class="form-control form-select form-select-solid rounded-0" name="sort_order" id="sort_order" data-control="select2" data-hide-search="true">
                                    '.$optionSort.'
                                    </select>
                                </div>
                            </div>
                        </div> &nbsp;


                        <div style="width:130px">
                            <div class="form-group">
                                <label>'.gc_language_render('action.from').':</label>
                                <div class="input-group">
                                <input type="text" name="from_to" id="from_to" class="form-control form-control-solid input-sm date_time rounded-0" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" /> 
                                </div>
                            </div>
                        </div> &nbsp;
                        <div style="width:130px">
                            <div class="form-group">
                                <label>'.gc_language_render('action.to').':</label>
                                <div class="input-group">
                                <input type="text" name="end_to" id="end_to" class="form-control form-control-solid input-sm date_time rounded-0" data-date-format="yyyy-mm-dd" placeholder="yyyy-mm-dd" /> 
                                </div>
                            </div>
                        </div> &nbsp;
                        <div style="width:150px">
                            <div class="form-group">
                                <label>'.gc_language_render('order.admin.status').':</label>
                                <div class="input-group">
                                <select class="form-control form-select form-select-solid rounded-0" name="order_status" data-control="select2" data-hide-search="true">
                                <option value="">'.gc_language_render('order.admin.search_order_status').'</option>
                                ' . $optionStatus . '
                                </select>
                                </div>
                            </div>
                        </div> &nbsp;
                        <div style="width:150px">
                            <div class="form-group">
                                <label>'.gc_language_render('order.admin.search_email').':</label>
                                <div class="input-group">
                                    <input type="text" name="email" class="form-control form-control-solid rounded-0 float-right" placeholder="' . gc_language_render('order.admin.search_email') . '" value="' . $email . '">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary  btn-flat"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>';
        //=menuSearch


        return view($this->templatePathAdmin.'screen.list')
            ->with($data);
    }

    /**
     * Form create new item in admin
     * @return [type] [description]
     */
    public function create()
    {
        $data = [
            'title'             => gc_language_render('order.admin.add_new_title'),
            'subTitle'          => '',
            'title_description' => gc_language_render('order.admin.add_new_des'),
            'icon'              => 'fa fa-plus',
        ];
        $paymentMethod = [];
        $shippingMethod = [];
        $paymentMethodTmp = gc_get_plugin_installed('payment', $onlyActive = false);
        foreach ($paymentMethodTmp as $key => $value) {
            $paymentMethod[$key] = gc_language_render($value->detail);
        }
        $shippingMethodTmp = gc_get_plugin_installed('shipping', $onlyActive = false);
        foreach ($shippingMethodTmp as $key => $value) {
            $shippingMethod[$key] = gc_language_render($value->detail);
        }
        $orderStatus            = $this->statusOrder;
        $currencies             = $this->currency;
        $countries              = $this->country;
        $currenciesRate         = json_encode(ShopCurrency::getListRate());
        $users                  = AdminCustomer::getListAll();
        $data['users']          = $users;
        $data['currencies']     = $currencies;
        $data['countries']      = $countries;
        $data['orderStatus']    = $orderStatus;
        $data['currenciesRate'] = $currenciesRate;
        $data['paymentMethod']  = $paymentMethod;
        $data['shippingMethod'] = $shippingMethod;

        return view($this->templatePathAdmin.'screen.order_add')
            ->with($data);
    }

    /**
     * Post create new item in admin
     * @return [type] [description]
     */
    public function postCreate()
    {
        $data = request()->all();
        $validate = [
            'first_name'      => 'required|max:100',
            'address1'        => 'required|max:100',
            'exchange_rate'   => 'required',
            'currency'        => 'required',
            'status'          => 'required',
            'payment_method'  => 'required',
            'shipping_method' => 'required',
        ];
        if (gc_config_admin('customer_lastname')) {
            $validate['last_name'] = 'required|max:100';
        }
        if (gc_config_admin('customer_address2')) {
            $validate['address2'] = 'required|max:100';
        }
        if (gc_config_admin('customer_address3')) {
            $validate['address3'] = 'required|max:100';
        }
        if (gc_config_admin('customer_phone')) {
            $validate['phone'] = config('validation.customer.phone_required', 'required|regex:/^0[^0][0-9\-]{6,12}$/');
        }
        if (gc_config_admin('customer_country')) {
            $validate['country'] = 'required|min:2';
        }
        if (gc_config_admin('customer_postcode')) {
            $validate['postcode'] = 'required|min:5';
        }
        if (gc_config_admin('customer_company')) {
            $validate['company'] = 'required|min:3';
        }
        $messages = [
            'last_name.required'       => gc_language_render('validation.required', ['attribute'=> gc_language_render('cart.last_name')]),
            'first_name.required'      => gc_language_render('validation.required', ['attribute'=> gc_language_render('cart.first_name')]),
            'email.required'           => gc_language_render('validation.required', ['attribute'=> gc_language_render('cart.email')]),
            'address1.required'        => gc_language_render('validation.required', ['attribute'=> gc_language_render('cart.address1')]),
            'address2.required'        => gc_language_render('validation.required', ['attribute'=> gc_language_render('cart.address2')]),
            'address3.required'        => gc_language_render('validation.required', ['attribute'=> gc_language_render('cart.address3')]),
            'phone.required'           => gc_language_render('validation.required', ['attribute'=> gc_language_render('cart.phone')]),
            'country.required'         => gc_language_render('validation.required', ['attribute'=> gc_language_render('cart.country')]),
            'postcode.required'        => gc_language_render('validation.required', ['attribute'=> gc_language_render('cart.postcode')]),
            'company.required'         => gc_language_render('validation.required', ['attribute'=> gc_language_render('cart.company')]),
            'sex.required'             => gc_language_render('validation.required', ['attribute'=> gc_language_render('cart.sex')]),
            'birthday.required'        => gc_language_render('validation.required', ['attribute'=> gc_language_render('cart.birthday')]),
            'email.email'              => gc_language_render('validation.email', ['attribute'=> gc_language_render('cart.email')]),
            'phone.regex'              => gc_language_render('customer.phone_regex'),
            'postcode.min'             => gc_language_render('validation.min', ['attribute'=> gc_language_render('cart.postcode')]),
            'country.min'              => gc_language_render('validation.min', ['attribute'=> gc_language_render('cart.country')]),
            'first_name.max'           => gc_language_render('validation.max', ['attribute'=> gc_language_render('cart.first_name')]),
            'email.max'                => gc_language_render('validation.max', ['attribute'=> gc_language_render('cart.email')]),
            'address1.max'             => gc_language_render('validation.max', ['attribute'=> gc_language_render('cart.address1')]),
            'address2.max'             => gc_language_render('validation.max', ['attribute'=> gc_language_render('cart.address2')]),
            'address3.max'             => gc_language_render('validation.max', ['attribute'=> gc_language_render('cart.address3')]),
            'last_name.max'            => gc_language_render('validation.max', ['attribute'=> gc_language_render('cart.last_name')]),
            'birthday.date'            => gc_language_render('validation.date', ['attribute'=> gc_language_render('cart.birthday')]),
            'birthday.date_format'     => gc_language_render('validation.date_format', ['attribute'=> gc_language_render('cart.birthday')]),
            'shipping_method.required' => gc_language_render('cart.validation.shippingMethod_required'),
            'payment_method.required'  => gc_language_render('cart.validation.paymentMethod_required'),
        ];


        $validator = Validator::make($data, $validate, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        //Create new order
        $dataCreate = [
            'customer_id'     => $data['customer_id'] ?? "",
            'first_name'      => $data['first_name'],
            'last_name'       => $data['last_name'] ?? '',
            'status'          => $data['status'],
            'currency'        => $data['currency'],
            'address1'        => $data['address1'],
            'address2'        => $data['address2'] ?? '',
            'address3'        => $data['address3'] ?? '',
            'country'         => $data['country'] ?? '',
            'company'         => $data['company'] ?? '',
            'postcode'        => $data['postcode'] ?? '',
            'phone'           => $data['phone'] ?? '',
            'payment_method'  => $data['payment_method'],
            'shipping_method' => $data['shipping_method'],
            'exchange_rate'   => $data['exchange_rate'],
            'email'           => $data['email'],
            'comment'         => $data['comment'],
        ];
        $dataCreate = gc_clean($dataCreate, [], true);
        $order = AdminOrder::create($dataCreate);
        AdminOrder::insertOrderTotal([
            ['id' => gc_uuid(),'code' => 'subtotal', 'value' => 0, 'title' => gc_language_render('order.totals.sub_total'), 'sort' => ShopOrderTotal::POSITION_SUBTOTAL, 'order_id' => $order->id],
            ['id' => gc_uuid(),'code' => 'tax', 'value' => 0, 'title' => gc_language_render('order.totals.tax'), 'sort' => ShopOrderTotal::POSITION_TAX, 'order_id' => $order->id],
            ['id' => gc_uuid(),'code' => 'shipping', 'value' => 0, 'title' => gc_language_render('order.totals.shipping'), 'sort' => ShopOrderTotal::POSITION_SHIPPING_METHOD, 'order_id' => $order->id],
            ['id' => gc_uuid(),'code' => 'discount', 'value' => 0, 'title' => gc_language_render('order.totals.discount'), 'sort' => ShopOrderTotal::POSITION_TOTAL_METHOD, 'order_id' => $order->id],
            ['id' => gc_uuid(),'code' => 'other_fee', 'value' => 0, 'title' => config('cart.process.other_fee.title'), 'sort' => ShopOrderTotal::POSITION_OTHER_FEE, 'order_id' => $order->id],
            ['id' => gc_uuid(),'code' => 'total', 'value' => 0, 'title' => gc_language_render('order.totals.total'), 'sort' => ShopOrderTotal::POSITION_TOTAL, 'order_id' => $order->id],
            ['id' => gc_uuid(),'code' => 'received', 'value' => 0, 'title' => gc_language_render('order.totals.received'), 'sort' => ShopOrderTotal::POSITION_RECEIVED, 'order_id' => $order->id],
        ]);
        //
        return redirect()->route('admin_order.index')->with('success', gc_language_render('action.create_success'));
    }

    /**
     * Order detail
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function detail($id)
    {
        $order = AdminOrder::getOrderAdmin($id);

        if (!$order) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }
        $products = (new AdminProduct)->getProductSelectAdmin(['kind' => [GC_PRODUCT_SINGLE, GC_PRODUCT_BUILD]]);
        $paymentMethod = [];
        $shippingMethod = [];
        $paymentMethodTmp = gc_get_plugin_installed('payment', $onlyActive = false);
        foreach ($paymentMethodTmp as $key => $value) {
            $paymentMethod[$key] = gc_language_render($value->detail);
        }
        $shippingMethodTmp = gc_get_plugin_installed('shipping', $onlyActive = false);
        foreach ($shippingMethodTmp as $key => $value) {
            $shippingMethod[$key] = gc_language_render($value->detail);
        }
        return view($this->templatePathAdmin.'screen.order_edit')->with(
            [
                "title" => gc_language_render('order.order_detail'),
                "subTitle" => '',
                'icon' => 'fa fa-file-text-o',
                "order" => $order,
                "products" => $products,
                "statusOrder" => $this->statusOrder,
                "statusPayment" => $this->statusPayment,
                "statusShipping" => $this->statusShipping,
                'dataTotal' => AdminOrder::getOrderTotal($id),
                'attributesGroup' => ShopAttributeGroup::pluck('name', 'id')->all(),
                'paymentMethod' => $paymentMethod,
                'shippingMethod' => $shippingMethod,
                'country' => $this->country,
            ]
        );
    }

    /**
     * [getInfoUser description]
     * @param   [description]
     * @return [type]           [description]
     */
    public function getInfoUser()
    {
        $id = request('id');
        return AdminCustomer::getCustomerAdminJson($id);
    }

    /**
     * [getInfoProduct description]
     * @param   [description]
     * @return [type]           [description]
     */
    public function getInfoProduct()
    {
        $id = request('id');
        $orderId = request('order_id');
        $oder = AdminOrder::getOrderAdmin($orderId);
        $product = AdminProduct::getProductAdmin($id);
        if (!$product) {
            return response()->json(['error' => 1, 'msg' => gc_language_render('admin.data_not_found_detail', ['msg' => '#product:'.$id]), 'detail' => '']);
        }
        $arrayReturn = $product->toArray();
        $arrayReturn['renderAttDetails'] = $product->renderAttributeDetailsAdmin($oder->currency, $oder->exchange_rate);
        $arrayReturn['price_final'] = $product->getFinalPrice();
        return response()->json($arrayReturn);
    }

    /**
     * process update order
     * @return [json]           [description]
     */
    public function postOrderUpdate()
    {
        $id = request('pk');
        $code = request('name');
        $value = request('value');
        if ($code == 'shipping' || $code == 'discount' || $code == 'received' || $code == 'other_fee') {
            $orderTotalOrigin = AdminOrder::getRowOrderTotal($id);
            $orderId = $orderTotalOrigin->order_id;
            $oldValue = $orderTotalOrigin->value;
            $order = AdminOrder::getOrderAdmin($orderId);
            if (!$order) {
                return response()->json(['error' => 1, 'msg' => gc_language_render('admin.data_not_found_detail', ['msg' => 'order#'.$orderId]), 'detail' => '']);
            }
            $dataRowTotal = [
                'id' => $id,
                'code' => $code,
                'value' => $value,
                'text' => gc_currency_render_symbol($value, $order->currency),
            ];
            AdminOrder::updateRowOrderTotal($dataRowTotal);
        } else {
            $orderId = $id;
            $order = AdminOrder::getOrderAdmin($orderId);
            if (!$order) {
                return response()->json(['error' => 1, 'msg' => gc_language_render('admin.data_not_found_detail', ['msg' => 'order#'.$orderId]), 'detail' => '']);
            }
            $oldValue = $order->{$code};
            $order->update([$code => $value]);

            if ($code == 'status') {
                //Process finish order
                if ($oldValue !=  5 && $value == 5) {
                    if (function_exists('gc_order_success_finish')) {
                        gc_order_success_finish($orderId);
                    }
                }
                if ($oldValue ==  5 && $value != 5) {
                    if (function_exists('gc_order_success_unfinish')) {
                        gc_order_success_unfinish($orderId);
                    }
                }
                //Process finish order
            }
        }

        //Add history
        $dataHistory = [
            'order_id' => $orderId,
            'content' => 'Change <b>' . $code . '</b> from <span style="color:blue">\'' . $oldValue . '\'</span> to <span style="color:red">\'' . $value . '\'</span>',
            'admin_id' => Admin::user()->id,
            'order_status_id' => $order->status,
        ];
        (new AdminOrder)->addOrderHistory($dataHistory);

        $orderUpdated = AdminOrder::getOrderAdmin($orderId);
        if ($orderUpdated->balance == 0 && $orderUpdated->total != 0) {
            $style = 'style="color:#0e9e33;font-weight:bold;"';
        } elseif ($orderUpdated->balance < 0) {
            $style = 'style="color:#ff2f00;font-weight:bold;"';
        } else {
            $style = 'style="font-weight:bold;"';
        }
        $blance = '<tr ' . $style . ' class="data-balance"><td>' . gc_language_render('order.totals.balance') . ':</td><td align="right">' . gc_currency_format($orderUpdated->balance) . '</td></tr>';
        return response()->json(['error' => 0, 'detail' =>
            [
                'total' => gc_currency_format($orderUpdated->total),
                'subtotal' => gc_currency_format($orderUpdated->subtotal),
                'tax' => gc_currency_format($orderUpdated->tax),
                'shipping' => gc_currency_format($orderUpdated->shipping),
                'discount' => gc_currency_format($orderUpdated->discount),
                'other_fee' => gc_currency_format($orderUpdated->other_fee),
                'received' => gc_currency_format($orderUpdated->received),
                'balance' => $blance,
            ],
            'msg' => gc_language_render('action.update_success')
        ]);
    }

    /**
     * [postAddItem description]
     * @param   [description]
     * @return [type]           [description]
     */
    public function postAddItem()
    {
        $addIds = request('add_id');
        $add_price = request('add_price');
        $add_qty = request('add_qty');
        $add_att = request('add_att');
        $add_tax = request('add_tax');
        $orderId = request('order_id');
        $items = [];

        $order = AdminOrder::getOrderAdmin($orderId);

        foreach ($addIds as $key => $id) {
            //where exits id and qty > 0
            if ($id && $add_qty[$key]) {
                $product = AdminProduct::getProductAdmin($id);
                if (!$product) {
                    return response()->json(['error' => 1, 'msg' => gc_language_render('admin.data_not_found_detail', ['msg' => '#'.$id]), 'detail' => '']);
                }
                $pAttr = json_encode($add_att[$id] ?? []);
                $items[] = array(
                    'id' => gc_uuid(),
                    'order_id' => $orderId,
                    'product_id' => $id,
                    'name' => $product->name,
                    'qty' => $add_qty[$key],
                    'price' => $add_price[$key],
                    'total_price' => $add_price[$key] * $add_qty[$key],
                    'sku' => $product->sku,
                    'tax' => $add_tax[$key],
                    'attribute' => $pAttr,
                    'currency' => $order->currency,
                    'exchange_rate' => $order->exchange_rate,
                    'created_at' => gc_time_now(),
                );
            }
        }
        if ($items) {
            try {
                (new ShopOrderDetail)->addNewDetail($items);
                //Add history
                $dataHistory = [
                    'order_id' => $orderId,
                    'content' => "Add product: <br>" . implode("<br>", array_column($items, 'name')),
                    'admin_id' => Admin::user()->id,
                    'order_status_id' => $order->status,
                ];
                (new AdminOrder)->addOrderHistory($dataHistory);

                AdminOrder::updateSubTotal($orderId);
                
                //end update total price
                return response()->json(['error' => 0, 'msg' => gc_language_render('action.update_success')]);
            } catch (\Throwable $e) {
                return response()->json(['error' => 1, 'msg' => 'Error: ' . $e->getMessage()]);
            }
        }
        return response()->json(['error' => 0, 'msg' => gc_language_render('action.update_success')]);
    }

    /**
     * [postEditItem description]
     * @param   [description]
     * @return [type]           [description]
     */
    public function postEditItem()
    {
        try {
            $id = request('pk');
            $field = request('name');
            $value = request('value');
            $item = ShopOrderDetail::find($id);
            $fieldOrg = $item->{$field};
            $orderId = $item->order_id;
            $item->{$field} = $value;
            if ($field == 'qty' || $field == 'price') {
                $item->total_price = $value * (($field == 'qty') ? $item->price : $item->qty);
            }
            $item->save();
            $item = $item->fresh();
            $order = AdminOrder::getOrderAdmin($orderId);
            if (!$order) {
                return response()->json(['error' => 1, 'msg' => gc_language_render('admin.data_not_found_detail', ['msg' => '#order:'.$orderId]), 'detail' => '']);
            }
            //Add history
            $dataHistory = [
                'order_id' => $orderId,
                'content' => gc_language_render('product.edit_product') . ' #' . $id . ': ' . $field . ' from ' . $fieldOrg . ' -> ' . $value,
                'admin_id' => Admin::user()->id,
                'order_status_id' => $order->status,
            ];
            (new AdminOrder)->addOrderHistory($dataHistory);

            //Update stock
            if ($field == 'qty') {
                $checkQty = $value - $fieldOrg;
                //Update stock, sold
                AdminProduct::updateStock($item->product_id, $checkQty);
            }

            //Update total price
            AdminOrder::updateSubTotal($orderId);
            //end update total price

            //fresh order info after update
            $orderUpdated = $order->fresh();

            if ($orderUpdated->balance == 0 && $orderUpdated->total != 0) {
                $style = 'style="color:#0e9e33;font-weight:bold;"';
            } elseif ($orderUpdated->balance < 0) {
                $style = 'style="color:#ff2f00;font-weight:bold;"';
            } else {
                $style = 'style="font-weight:bold;"';
            }
            $blance = '<tr ' . $style . ' class="data-balance"><td>' . gc_language_render('order.totals.balance') . ':</td><td align="right">' . gc_currency_format($orderUpdated->balance) . '</td></tr>';
            $arrayReturn = ['error' => 0, 'detail' => [
                'total'            => gc_currency_format($orderUpdated->total),
                'subtotal'         => gc_currency_format($orderUpdated->subtotal),
                'tax'              => gc_currency_format($orderUpdated->tax),
                'shipping'         => gc_currency_format($orderUpdated->shipping),
                'discount'         => gc_currency_format($orderUpdated->discount),
                'received'         => gc_currency_format($orderUpdated->received),
                'item_total_price' => gc_currency_render_symbol($item->total_price, $item->currency),
                'item_id'          => $id,
                'balance'          => $blance,
            ],'msg' => gc_language_render('action.update_success')
            ];
        } catch (\Throwable $e) {
            $arrayReturn = ['error' => 1, 'msg' => $e->getMessage()];
        }
        return response()->json($arrayReturn);
    }

    /**
     * [postDeleteItem description]
     * @param   [description]
     * @return [type]           [description]
     */
    public function postDeleteItem()
    {
        try {
            $data = request()->all();
            $pId = $data['pId'] ?? "";
            $itemDetail = (new ShopOrderDetail)->where('id', $pId)->first();
            if (!$itemDetail) {
                return response()->json(['error' => 1, 'msg' => gc_language_render('admin.data_not_found_detail', ['msg' => 'detail#'.$pId]), 'detail' => '']);
            }
            $orderId = $itemDetail->order_id;
            $order = AdminOrder::getOrderAdmin($orderId);
            if (!$order) {
                return response()->json(['error' => 1, 'msg' => gc_language_render('admin.data_not_found_detail', ['msg' => 'order#'.$orderId]), 'detail' => '']);
            }

            $pId = $itemDetail->product_id;
            $qty = $itemDetail->qty;
            $itemDetail->delete(); //Remove item from shop order detail
            //Update total price
            AdminOrder::updateSubTotal($orderId);
            //Update stock, sold
            AdminProduct::updateStock($pId, -$qty);

            //Add history
            $dataHistory = [
                'order_id' => $orderId,
                'content' => 'Remove item pID#' . $pId,
                'admin_id' => Admin::user()->id,
                'order_status_id' => $order->status,
            ];
            (new AdminOrder)->addOrderHistory($dataHistory);
            return response()->json(['error' => 0, 'msg' => gc_language_render('action.update_success')]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 1, 'msg' => 'Error: ' . $e->getMessage()]);
        }
    }

    /*
    Delete list order ID
    Need mothod destroy to boot deleting in model
    */
    public function deleteList()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => gc_language_render('admin.method_not_allow')]);
        } else {
            $ids = request('ids');
            $arrID = explode(',', $ids);
            $arrDontPermission = [];
            foreach ($arrID as $key => $id) {
                if (!$this->checkPermisisonItem($id)) {
                    $arrDontPermission[] = $id;
                }
            }
            if (count($arrDontPermission)) {
                return response()->json(['error' => 1, 'msg' => gc_language_render('admin.remove_dont_permisison') . ': ' . json_encode($arrDontPermission)]);
            } else {
                AdminOrder::destroy($arrID);
                return response()->json(['error' => 0, 'msg' => gc_language_render('action.update_success')]);
            }
        }
    }

    /**
     * Process invoice
     */
    public function invoice()
    {
        $orderId = request('order_id') ?? null;
        $action = request('action') ?? '';
        $order = AdminOrder::getOrderAdmin($orderId);
        if ($order) {
            $data                    = array();
            $data['name']            = $order['first_name'] . ' ' . $order['last_name'];
            $data['address']         = $order['address1'] . ', ' . $order['address2'] . ', ' . $order['address3'].', '.$order['country'];
            $data['phone']           = $order['phone'];
            $data['email']           = $order['email'];
            $data['comment']         = $order['comment'];
            $data['payment_method']  = $order['payment_method'];
            $data['shipping_method'] = $order['shipping_method'];
            $data['created_at']      = $order['created_at'];
            $data['currency']        = $order['currency'];
            $data['exchange_rate']   = $order['exchange_rate'];
            $data['subtotal']        = $order['subtotal'];
            $data['tax']             = $order['tax'];
            $data['shipping']        = $order['shipping'];
            $data['discount']        = $order['discount'];
            $data['total']           = $order['total'];
            $data['received']        = $order['received'];
            $data['balance']         = $order['balance'];
            $data['other_fee']       = $order['other_fee'] ?? 0;
            $data['comment']         = $order['comment'];
            $data['country']         = $order['country'];
            $data['id']              = $order->id;
            $data['details'] = [];

            $attributesGroup =  ShopAttributeGroup::pluck('name', 'id')->all();

            if ($order->details) {
                foreach ($order->details as $key => $detail) {
                    $arrAtt = json_decode($detail->attribute, true);
                    if ($arrAtt) {
                        $htmlAtt = '';
                        foreach ($arrAtt as $groupAtt => $att) {
                            $htmlAtt .= $attributesGroup[$groupAtt] .':'.gc_render_option_price($att, $order['currency'], $order['exchange_rate']);
                        }
                        $name = $detail->name.'('.strip_tags($htmlAtt).')';
                    } else {
                        $name = $detail->name;
                    }
                    $data['details'][] = [
                        'no' => $key + 1, 
                        'sku' => $detail->sku, 
                        'name' => $name, 
                        'qty' => $detail->qty, 
                        'price' => $detail->price, 
                        'total_price' => $detail->total_price,
                    ];
                }
            }

            if ($action =='invoice_excel') {
                $options = ['filename' => 'Order ' . $orderId];
                return \Export::export($action, $data, $options);
            }
            
            return view($this->templatePathAdmin.'format.invoice')
            ->with($data);
        } else {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }
    }

    /**
     * Check permisison item
     */
    public function checkPermisisonItem($id)
    {
        return AdminOrder::getOrderAdmin($id);
    }
}
