<?php

namespace Glumbo\Gracart\Front\Controllers\Auth;

use Glumbo\Gracart\Front\Controllers\RootFrontController;
use Glumbo\Gracart\Front\Models\ShopCountry;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends RootFrontController
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = '/';
    protected function redirectTo()
    {
        return gc_route('customer.index');
    }
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('guest')->except('logout');
    }

    protected function validateLogin(Request $request)
    {
        $messages = [
            'email.email'       => gc_language_render('validation.email', ['attribute'=> gc_language_render('customer.email')]),
            'email.required'    => gc_language_render('validation.required', ['attribute'=> gc_language_render('customer.email')]),
            'password.required' => gc_language_render('validation.required', ['attribute'=> gc_language_render('customer.password')]),
            ];
        $this->validate($request, [
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ], $messages);
    }

    /**
     * Process front form login
     *
     * @param [type] ...$params
     * @return void
     */
    public function showLoginFormProcessFront(...$params)
    {
        if (config('app.seoLang')) {
            $lang = $params[0] ?? '';
            gc_lang_switch($lang);
        }
        return $this->_showLoginForm();
    }


    /**
     * Form login
     *
     * @return  [type]  [return description]
     */
    private function _showLoginForm()
    {
        if (Auth::user()) {
            return redirect()->route('home');
        }
        gc_check_view($this->templatePath . '.auth.login');
        return view(
            $this->templatePath . '.auth.login',
            array(
                'title'       => gc_language_render('customer.login_title'),
                'countries'   => ShopCountry::getCodeAll(),
                'layout_page' => 'shop_auth',
                'breadcrumbs' => [
                    ['url'    => '', 'title' => gc_language_render('customer.login_title')],
                ],
            )
        );
    }


    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect(gc_route('login'));
    }

    protected function authenticated(Request $request, $user)
    {
        if (auth()->user()) {
            session(['customer' => auth()->user()->toJson()]);
        } else {
            session(['customer' => []]);
        }
    }
}
