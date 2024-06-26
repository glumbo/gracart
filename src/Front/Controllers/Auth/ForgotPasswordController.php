<?php

namespace Glumbo\Gracart\Front\Controllers\Auth;

use Glumbo\Gracart\Front\Controllers\RootFrontController;
use Auth;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends RootFrontController
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
     */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('guest');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(\Illuminate\Http\Request $request)
    {
        $data = $request->all();
        $dataMapping['email'] = 'required|string|email';
        if (gc_captcha_method() && in_array('forgot', gc_captcha_page())) {
            $data['captcha_field'] = $data[gc_captcha_method()->getField()] ?? '';
            $dataMapping['captcha_field'] = ['required', 'string', new \Glumbo\Gracart\Rules\CaptchaRule];
        }
        $validator = \Illuminate\Support\Facades\Validator::make($data, $dataMapping);
        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker()->sendResetLink(
            $this->credentials($request)
        );

        return $response == \Illuminate\Support\Facades\Password::RESET_LINK_SENT
                    ? $this->sendResetLinkResponse($request, $response)
                    : $this->sendResetLinkFailedResponse($request, $response);
    }


    /**
     * Process front Form forgot password
     *
     * @param [type] ...$params
     * @return void
     */
    public function showLinkRequestFormProcessFront(...$params)
    {
        if (config('app.seoLang')) {
            $lang = $params[0] ?? '';
            gc_lang_switch($lang);
        }
        return $this->_showLinkRequestForm();
    }

    /**
     * Form forgot password
     * @return [view]
     */
    private function _showLinkRequestForm()
    {
        if (Auth::user()) {
            return redirect()->route('home');
        }
        $viewCaptcha = '';
        if (gc_captcha_method() && in_array('forgot', gc_captcha_page())) {
            if (view()->exists(gc_captcha_method()->pathPlugin.'::render')) {
                $dataView = [
                    'titleButton' => gc_language_render('action.submit'),
                    'idForm' => 'gc_form-process',
                    'idButtonForm' => 'gc_button-form-process',
                ];
                $viewCaptcha = view(gc_captcha_method()->pathPlugin.'::render', $dataView)->render();
            }
        }
        gc_check_view($this->templatePath . '.auth.forgot');
        return view(
            $this->templatePath . '.auth.forgot',
            array(
                'title'       => gc_language_render('customer.password_forgot'),
                'layout_page' => 'shop_auth',
                'viewCaptcha' => $viewCaptcha,
                'breadcrumbs' => [
                    ['url'    => '', 'title' => gc_language_render('customer.password_forgot')],
                ],
            )
        );
    }
}
