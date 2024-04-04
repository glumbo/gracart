<?php
use Glumbo\Gracart\Mail\SendMail;
use Glumbo\Gracart\Jobs\SendEmailJob;
use Illuminate\Support\Facades\Mail;

/**
 * Function send mail
 * Mail queue to run need setting crontab for php artisan schedule:run
 *
 * @param   [string]  $view            Path to view
 * @param   array     $dataView        Content send to view
 * @param   array     $emailConfig     to, cc, bbc, subject..
 * @param   array     $attach      Attach file
 *
 * @return  mixed
 */
if (!function_exists('gc_send_mail') && !in_array('gc_send_mail', config('helper_except', []))) {
    function gc_send_mail($view, array $dataView = [], array $emailConfig = [], array $attach = [])
    {
        if (!empty(gc_config('email_action_mode'))) {
            if (!empty(gc_config('email_action_queue'))) {
                dispatch(new SendEmailJob($view, $dataView, $emailConfig, $attach));
            } else {
                gc_process_send_mail($view, $dataView, $emailConfig, $attach);
            }
        } else {
            return false;
        }
    }
}
/**
 * Process send mail
 *
 * @param   [type]  $view         [$view description]
 * @param   array   $dataView     [$dataView description]
 * @param   array   $emailConfig  [$emailConfig description]
 * @param   array   $attach       [$attach description]
 *
 * @return  [][][]                [return description]
 */
if (!function_exists('gc_process_send_mail') && !in_array('gc_process_send_mail', config('helper_except', []))) {
    function gc_process_send_mail($view, array $dataView = [], array $emailConfig = [], array $attach = [])
    {
        try {
            Mail::send(new SendMail($view, $dataView, $emailConfig, $attach));
        } catch (\Throwable $e) {
            gc_report("Sendmail view:" . $view . PHP_EOL . $e->getMessage());
        }
    }
}
