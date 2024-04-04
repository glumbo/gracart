<?php

/**
 * Send email reset password
 */
if (!function_exists('gc_admin_sendmail_reset_notification') && !in_array('gc_admin_sendmail_reset_notification', config('helper_except', []))) {
    function gc_admin_sendmail_reset_notification(string $token, string $emailReset)
    {
        $checkContent = (new \Glumbo\Gracart\Front\Models\ShopEmailTemplate)->where('group', 'forgot_password')->where('status', 1)->first();
        if ($checkContent) {
            $content = $checkContent->text;
            $dataFind = [
                '/\{\{\$title\}\}/',
                '/\{\{\$reason_sendmail\}\}/',
                '/\{\{\$note_sendmail\}\}/',
                '/\{\{\$note_access_link\}\}/',
                '/\{\{\$reset_link\}\}/',
                '/\{\{\$reset_button\}\}/',
            ];
            $url = gc_route('admin.password_reset', ['token' => $token]);
            $dataReplace = [
                gc_language_render('email.forgot_password.title'),
                gc_language_render('email.forgot_password.reason_sendmail'),
                gc_language_render('email.forgot_password.note_sendmail', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]),
                gc_language_render('email.forgot_password.note_access_link', ['reset_button' => gc_language_render('email.forgot_password.reset_button'), 'url' => $url]),
                $url,
                gc_language_render('email.forgot_password.reset_button'),
            ];
            $content = preg_replace($dataFind, $dataReplace, $content);
            $dataView = [
                'content' => $content,
            ];

            $config = [
                'to' => $emailReset,
                'subject' => gc_language_render('email.forgot_password.reset_button'),
            ];

            gc_send_mail('templates.' . gc_store('template') . '.mail.forgot_password', $dataView, $config, $dataAtt = []);
        }
    }
}
