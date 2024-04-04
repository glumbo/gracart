<?php
namespace Glumbo\Gracart\Front\Controllers;

use Glumbo\Gracart\Front\Controllers\RootFrontController;
use Glumbo\Gracart\Front\Models\ShopCountry;
use Glumbo\Gracart\Front\Models\ShopOrder;
use Glumbo\Gracart\Front\Models\ShopOrderStatus;
use Glumbo\Gracart\Front\Models\ShopShippingStatus;
use Glumbo\Gracart\Front\Models\ShopCustomer;
use Glumbo\Gracart\Front\Models\ShopCustomField;
use Glumbo\Gracart\Front\Models\ShopAttributeGroup;
use Glumbo\Gracart\Front\Models\ShopCustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Glumbo\Gracart\Front\Controllers\Auth\AuthTrait;

class ShopAccountController extends RootFrontController
{
    use AuthTrait;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Process front index profile
     *
     * @param [type] ...$params
     * @return void
     */
    public function indexProcessFront(...$params)
    {
        if (config('app.seoLang')) {
            $lang = $params[0] ?? '';
            gc_lang_switch($lang);
        }
        return $this->_index();
    }

    /**
     * Index user profile
     *
     * @return  [view]
     */
    private function _index()
    {
        $customer = auth()->user();

        gc_check_view($this->templatePath . '.account.index');
        return view($this->templatePath . '.account.index')
            ->with(
                [
                    'title'       => gc_language_render('customer.my_account'),
                    'customer'    => $customer,
                    'layout_page' => 'shop_profile',
                    'breadcrumbs' => [
                        ['url'    => '', 'title' => gc_language_render('customer.my_account')],
                    ],
                ]
            );
    }

    /**
     * Process front change passord
     *
     * @param [type] ...$params
     * @return void
     */
    public function changePasswordProcessFront(...$params)
    {
        if (config('app.seoLang')) {
            $lang = $params[0] ?? '';
            gc_lang_switch($lang);
        }
        return $this->_changePassword();
    }

    /**
     * Form Change password
     *
     * @return  [view]
     */
    private function _changePassword()
    {
        $customer = auth()->user();
        gc_check_view($this->templatePath . '.account.change_password');
        return view($this->templatePath . '.account.change_password')
        ->with(
            [
                'title'       => gc_language_render('customer.change_password'),
                'customer'    => $customer,
                'layout_page' => 'shop_profile',
                'breadcrumbs' => [
                    ['url'    => gc_route('customer.index'), 'title' => gc_language_render('front.my_account')],
                    ['url'    => '', 'title' => gc_language_render('customer.change_password')],
                ],
            ]
        );
    }

    /**
     * Post change password
     *
     * @param   Request  $request  [$request description]
     *
     * @return  [redirect]
     */
    public function postChangePassword(Request $request)
    {
        $dataUser = Auth::user();
        $password = $request->get('password');
        $password_old = $request->get('password_old');
        if (trim($password_old) == '') {
            return redirect()->back()
                ->with(
                    [
                        'password_old_error' => gc_language_render('customer.password_old_required')
                    ]
                );
        } else {
            if (!\Hash::check($password_old, $dataUser->password)) {
                return redirect()->back()
                    ->with(
                        [
                            'password_old_error' => gc_language_render('customer.password_old_notcorrect')
                        ]
                    );
            }
        }
        $messages = [
            'password.required' => gc_language_render('validation.required', ['attribute'=> gc_language_render('customer.password')]),
            'password.confirmed' => gc_language_render('validation.confirmed', ['attribute'=> gc_language_render('customer.password')]),
            'password_old.required' => gc_language_render('validation.required', ['attribute'=> gc_language_render('customer.password_old')]),
            'password.min' => gc_language_render('validation.password.min', ['attribute'=> gc_language_render('customer.password')]),
            'password.max' => gc_language_render('validation.password.max', ['attribute'=> gc_language_render('customer.password')]),
            'password.letters' => gc_language_render('validation.password.letters', ['attribute'=> gc_language_render('customer.password')]),
            'password.mixed' => gc_language_render('validation.password.mixed', ['attribute'=> gc_language_render('customer.password')]),
            'password.numbers' => gc_language_render('validation.password.numbers', ['attribute'=> gc_language_render('customer.password')]),
            'password.symbols' => gc_language_render('validation.password.symbols', ['attribute'=> gc_language_render('customer.password')]),
        ];
        $v = Validator::make(
            $request->all(),
            [
                'password_old' => 'required',
                'password' => gc_customer_validate_password()['password_confirm'],
            ],
            $messages
        );
        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors());
        }
        $dataUser->password = bcrypt($password);
        $dataUser->save();

        return redirect(gc_route('customer.index'))
            ->with(['success' => gc_language_render('customer.update_success')]);
    }

    /**
     * Process front change info
     *
     * @param [type] ...$params
     * @return void
     */
    public function changeInfomationProcessFront(...$params)
    {
        if (config('app.seoLang')) {
            $lang = $params[0] ?? '';
            gc_lang_switch($lang);
        }
        return $this->_changeInfomation();
    }

    /**
     * Form change info
     *
     * @return  [view]
     */
    private function _changeInfomation()
    {
        $customer = auth()->user();
        gc_check_view($this->templatePath . '.account.change_infomation');
        return view($this->templatePath . '.account.change_infomation')
            ->with(
                [
                    'title'       => gc_language_render('customer.change_infomation'),
                    'customer'    => $customer,
                    'countries'   => ShopCountry::getCodeAll(),
                    'layout_page' => 'shop_profile',
                    'customFields'=> (new ShopCustomField)->getCustomField($type = 'shop_customer'),
                    'breadcrumbs' => [
                        ['url'    => gc_route('customer.index'), 'title' => gc_language_render('front.my_account')],
                        ['url'    => '', 'title' => gc_language_render('customer.change_infomation')],
                    ],
                ]
            );
    }

    /**
     * Process update info
     *
     * @param   Request  $request  [$request description]
     *
     * @return  [redirect]
     */
    public function postChangeInfomation(Request $request)
    {
        $user = Auth::user();
        $cId = $user->id;
        $data = request()->all();

        $v =  $this->validator($data);
        if ($v->fails()) {
            return redirect()->back()
                ->withErrors($v)
                ->withInput();
        }
        $user = $this->updateCustomer($data, $cId);

        return redirect(gc_route('customer.index'))
            ->with(['success' => gc_language_render('customer.update_success')]);
    }

    /**
     * Validate data input
     */
    protected function validator(array $data)
    {
        $dataMapp = $this->mappingValidatorEdit($data);
        return Validator::make($data, $dataMapp['validate'], $dataMapp['messages']);
    }

    /**
     * Update data customer
     */
    protected function updateCustomer(array $data, string $cId)
    {
        $dataMapp = $this->mappingValidatorEdit($data);
        $user = ShopCustomer::updateInfo($dataMapp['dataUpdate'], $cId);

        return $user;
    }

    /**
     * Process front order list
     *
     * @param [type] ...$params
     * @return void
     */
    public function orderListProcessFront(...$params)
    {
        if (config('app.seoLang')) {
            $lang = $params[0] ?? '';
            gc_lang_switch($lang);
        }
        return $this->_orderList();
    }

    /**
     * Render order list
     * @return [view]
     */
    private function _orderList()
    {
        $customer = auth()->user();
        $statusOrder = ShopOrderStatus::getIdAll();
        gc_check_view($this->templatePath . '.account.order_list');
        return view($this->templatePath . '.account.order_list')
            ->with(
                [
                'title'       => gc_language_render('customer.order_history'),
                'statusOrder' => $statusOrder,
                'orders'      => (new ShopOrder)->profile()->getData(),
                'customer'    => $customer,
                'layout_page' => 'shop_profile',
                'breadcrumbs' => [
                    ['url'    => gc_route('customer.index'), 'title' => gc_language_render('front.my_account')],
                    ['url'    => '', 'title' => gc_language_render('customer.order_history')],
                ],
                ]
            );
    }

    /**
     * Process front order detail
     *
     * @param [type] ...$params
     * @return void
     */
    public function orderDetailProcessFront(...$params)
    {
        if (config('app.seoLang')) {
            $lang = $params[0] ?? '';
            $id = $params[1] ?? '';
            gc_lang_switch($lang);
        } else {
            $id = $params[0] ?? '';
        }
        return $this->_orderDetail($id);
    }

    /**
     * Render order detail
     * @return [view]
     */
    private function _orderDetail($id)
    {
        $customer = auth()->user();
        $statusOrder = ShopOrderStatus::getIdAll();
        $statusShipping = ShopShippingStatus::getIdAll();
        $attributesGroup = ShopAttributeGroup::pluck('name', 'id')->all();
        $order = ShopOrder::where('id', $id) ->where('customer_id', $customer->id)->first();
        if ($order) {
            $title = gc_language_render('customer.order_detail').' #'.$order->id;
        } else {
            return $this->pageNotFound();
        }
        gc_check_view($this->templatePath . '.account.order_detail');
        return view($this->templatePath . '.account.order_detail')
        ->with(
            [
            'title'           => $title,
            'statusOrder'     => $statusOrder,
            'statusShipping'  => $statusShipping,
            'countries'       => ShopCountry::getCodeAll(),
            'attributesGroup' => $attributesGroup,
            'order'           => $order,
            'customer'        => $customer,
            'layout_page'     => 'shop_profile',
            'breadcrumbs'     => [
                ['url'        => gc_route('customer.index'), 'title' => gc_language_render('front.my_account')],
                ['url'        => '', 'title' => $title],
            ],
            ]
        );
    }

    /**
     * Process front address list
     *
     * @param [type] ...$params
     * @return void
     */
    public function addressListProcessFront(...$params)
    {
        if (config('app.seoLang')) {
            $lang = $params[0] ?? '';
            gc_lang_switch($lang);
        }
        return $this->_addressList();
    }

    /**
     * Render address list
     * @return [view]
     */
    private function _addressList()
    {
        $customer = auth()->user();
        gc_check_view($this->templatePath . '.account.address_list');
        return view($this->templatePath . '.account.address_list')
            ->with(
                [
                'title'       => gc_language_render('customer.address_list'),
                'addresses'   => $customer->addresses,
                'countries'   => ShopCountry::getCodeAll(),
                'customer'    => $customer,
                'layout_page' => 'shop_profile',
                'breadcrumbs' => [
                    ['url'    => gc_route('customer.index'), 'title' => gc_language_render('front.my_account')],
                    ['url'    => '', 'title' => gc_language_render('customer.address_list')],
                ],
                ]
            );
    }

    /**
     * Process front address update
     *
     * @param [type] ...$params
     * @return void
     */
    public function updateAddressProcessFront(...$params)
    {
        if (config('app.seoLang')) {
            $lang = $params[0] ?? '';
            $id = $params[1] ?? '';
            gc_lang_switch($lang);
        } else {
            $id = $params[0] ?? '';
        }
        return $this->_updateAddress($id);
    }

    /**
     * Render address detail
     * @return [view]
     */
    private function _updateAddress($id)
    {
        $customer = auth()->user();
        $address =  (new ShopCustomerAddress)->where('customer_id', $customer->id)
            ->where('id', $id)
            ->first();
        if ($address) {
            $title = gc_language_render('customer.address_detail');
        } else {
            return $this->pageNotFound();
        }
        gc_check_view($this->templatePath . '.account.update_address');
        return view($this->templatePath . '.account.update_address')
        ->with(
            [
            'title'       => $title,
            'address'     => $address,
            'customer'    => $customer,
            'countries'   => ShopCountry::getCodeAll(),
            'layout_page' => 'shop_profile',
            'breadcrumbs' => [
                ['url'    => gc_route('customer.index'), 'title' => gc_language_render('front.my_account')],
                ['url'    => '', 'title' => $title],
            ],
            ]
        );
    }

    /**
     * Process update address
     *
     * @param   Request  $request  [$request description]
     *
     * @return  [redirect]
     */
    public function postUpdateAddressFront(...$params)
    {
        if (config('app.seoLang')) {
            $lang = $params[0] ?? '';
            $id = $params[1] ?? '';
            gc_lang_switch($lang);
        } else {
            $id = $params[0] ?? '';
        }
        return $this->_postUpdateAddress($id);
    }

    /**
     * Process update address
     *
     * @param   Request  $request  [$request description]
     *
     * @return  [redirect]
     */
    private function _postUpdateAddress($id)
    {
        $customer = auth()->user();
        $data = request()->all();
        $address =  (new ShopCustomerAddress)->where('customer_id', $customer->id)
            ->where('id', $id)
            ->first();
        
        $dataMapp = gc_customer_address_mapping($data);
        $dataUpdate = $dataMapp['dataAddress'];
        $validate = $dataMapp['validate'];
        $messages = $dataMapp['messages'];

        $v = Validator::make(
            $dataUpdate,
            $validate,
            $messages
        );
        if ($v->fails()) {
            return redirect()->back()->withErrors($v->errors());
        }

        $address->update(gc_clean($dataUpdate));

        if (!empty($data['default'])) {
            (new ShopCustomer)->find($customer->id)->update(['address_id' => $id]);
        }
        return redirect(gc_route('customer.address_list'))
            ->with(['success' => gc_language_render('customer.update_success')]);
    }

    /**
     * Get address detail
     *
     * @return  [json]
     */
    public function getAddress()
    {
        $customer = auth()->user();
        $id = request('id');
        $address =  (new ShopCustomerAddress)->where('customer_id', $customer->id)
            ->where('id', $id)
            ->first();
        if ($address) {
            return $address->toJson();
        } else {
            return $this->pageNotFound();
        }
    }

    /**
     * Get address detail
     *
     * @return  [json]
     */
    public function deleteAddress()
    {
        $customer = auth()->user();
        $id = request('id');
        (new ShopCustomerAddress)->where('customer_id', $customer->id)
            ->where('id', $id)
            ->delete();
        return json_encode(['error' => 0, 'msg' => gc_language_render('customer.delete_address_success')]);
    }

    /**
     * Process front address update
     *
     * @param [type] ...$params
     * @return void
     */
    public function verificationProcessFront(...$params)
    {
        if (config('app.seoLang')) {
            $lang = $params[0] ?? '';
            gc_lang_switch($lang);
        }
        return $this->_verification();
    }

    /**
     * _verification function
     *
     * @return void
     */
    private function _verification()
    {
        $customer = auth()->user();
        if (!$customer->hasVerifiedEmail()) {
            return redirect(gc_route('customer.index'));
        }
        gc_check_view($this->templatePath . '.account.verify');
        return view($this->templatePath . '.account.verify')
            ->with(
                [
                    'title' => gc_language_render('customer.verify_email.title_page'),
                    'customer' => $customer,
                ]
            );
    }

    /**
     * Resend email verification
     *
     * @return void
     */
    public function resendVerification()
    {
        $customer = auth()->user();
        if (!$customer->hasVerifiedEmail()) {
            return redirect(gc_route('customer.index'));
        }
        $resend = $customer->sendEmailVerify();

        if ($resend) {
            return redirect()->back()->with('resent', true);
        }
    }

    /**
     * Process Verification
     *
     * @param [type] $id
     * @param [type] $token
     * @return void
     */
    public function verificationProcessData(Request $request, $id = null, $token = null)
    {
        $arrMsg = [
            'error' => 0,
            'msg' => '',
            'detail' => '',
        ];
        $customer = auth()->user();
        if (!$customer) {
            $arrMsg = [
                'error' => 1,
                'msg' => gc_language_render('customer.verify_email.link_invalid'),
            ];
        } elseif ($customer->id != $id) {
            $arrMsg = [
                'error' => 1,
                'msg' => gc_language_render('customer.verify_email.link_invalid'),
            ];
        } elseif (sha1($customer->email) != $token) {
            $arrMsg = [
                'error' => 1,
                'msg' => gc_language_render('customer.verify_email.link_invalid'),
            ];
        }
        if (! $request->hasValidSignature()) {
            abort(401);
        }
        if ($arrMsg['error']) {
            return redirect(route('home'))->with(['error' => $arrMsg['msg']]);
        } else {
            $customer->update(['email_verified_at' => \Carbon\Carbon::now()]);
            return redirect(gc_route('customer.index'))->with(['message' => gc_language_render('customer.verify_email.verify_success')]);
        }
    }
}
