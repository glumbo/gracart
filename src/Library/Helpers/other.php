<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

/*
String to Url
 */
if (!function_exists('gc_word_format_url') && !in_array('gc_word_format_url', config('helper_except', []))) {
    function gc_word_format_url(string $str = ""):string
    {
        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );

        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        return strtolower(preg_replace(
            array('/[\s\-\/\\\?\(\)\~\.\[\]\%\*\#\@\$\^\&\!\'\"\`\;\:]+/'),
            array('-'),
            strtolower($str)
        ));
    }
}


if (!function_exists('gc_url_render') && !in_array('gc_url_render', config('helper_except', []))) {
    /*
    url render
     */
    function gc_url_render(string $string = ""):string
    {
        $arrCheckRoute = explode('route::', $string);
        $arrCheckUrl = explode('admin::', $string);

        if (count($arrCheckRoute) == 2) {
            $arrRoute = explode('::', $string);
            if (Str::startsWith($string, 'route::admin')) {
                if (isset($arrRoute[2])) {
                    return gc_route_admin($arrRoute[1], explode(',', $arrRoute[2]));
                } else {
                    return gc_route_admin($arrRoute[1]);
                }
            } else {
                if (isset($arrRoute[2])) {
                    return gc_route($arrRoute[1], explode(',', $arrRoute[2]));
                } else {
                    return gc_route($arrRoute[1]);
                }
            }
        }

        if (count($arrCheckUrl) == 2) {
            $string = Str::start($arrCheckUrl[1], '/');
            $string = GC_ADMIN_PREFIX . $string;
            return url($string);
        }
        return url($string);
    }
}


if (!function_exists('gc_html_render') && !in_array('gc_html_render', config('helper_except', []))) {
    /*
    Html render
     */
    function gc_html_render(string $string = ""):string
    {
        $string = htmlspecialchars_decode($string);
        return $string;
    }
}

if (!function_exists('gc_word_format_class') && !in_array('gc_word_format_class', config('helper_except', []))) {
    /*
    Format class name
     */
    function gc_word_format_class(string $word = ""):string
    {
        $word = Str::camel($word);
        $word = ucfirst($word);
        return $word;
    }
}

if (!function_exists('gc_word_limit') && !in_array('gc_word_limit', config('helper_except', []))) {
    /*
    Truncates words
     */
    function gc_word_limit(string $word = "", int $limit = 20, string $arg = ''):string
    {
        $word = Str::limit($word, $limit, $arg);
        return $word;
    }
}

if (!function_exists('gc_token') && !in_array('gc_token', config('helper_except', []))) {
    /*
    Create random token
     */
    function gc_token(int $length = 32)
    {
        $token = Str::random($length);
        return $token;
    }
}

if (!function_exists('gc_report') && !in_array('gc_report', config('helper_except', []))) {
    /*
    Handle report
     */
    function gc_report(string $msg = "", array $ext = [])
    {
        $msg = gc_time_now(config('app.timezone')).' ('.config('app.timezone').'):'.PHP_EOL.$msg.PHP_EOL;
        if (!in_array('slack', $ext)) {
            if (config('logging.channels.slack.url')) {
                try {
                    \Log::channel('slack')->emergency($msg);
                } catch (\Throwable $e) {
                    $msg .= $e->getFile().'- Line: '.$e->getLine().PHP_EOL.$e->getMessage().PHP_EOL;
                }
            }
        }
        \Log::error($msg);
    }
}


if (!function_exists('gc_handle_exception') && !in_array('gc_handle_exception', config('helper_except', []))) {
    /*
    Process msg exception
     */
    function gc_handle_exception(\Throwable $exception)
    {
        $msg = "```". $exception->getMessage().'```'.PHP_EOL;
        $msg .= "```IP:```".request()->ip().PHP_EOL;
        $msg .= "*File* `".$exception->getFile()."`, *Line:* ".$exception->getLine().", *Code:* ".$exception->getCode().PHP_EOL.'URL= '.url()->current();
        if (function_exists('gc_report') && $msg) {
            gc_report($msg);
        }
    }
}


if (!function_exists('gc_push_include_view') && !in_array('gc_push_include_view', config('helper_except', []))) {
    /**
     * Push view
     *
     * @param   [string]  $position
     * @param   [string]  $pathView
     *
     */
    function gc_push_include_view(string $position = "", string $pathView = "")
    {
        $includePathView = config('gc_include_view.'.$position, []);
        $includePathView[] = $pathView;
        config(['gc_include_view.'.$position => $includePathView]);
    }
}


if (!function_exists('gc_push_include_script') && !in_array('gc_push_include_script', config('helper_except', []))) {
    /**
     * Push script
     *
     * @param   [string]  $position
     * @param   [string]  $pathScript
     *
     */
    function gc_push_include_script($position, $pathScript)
    {
        $includePathScript = config('gc_include_script.'.$position, []);
        $includePathScript[] = $pathScript;
        config(['gc_include_script.'.$position => $includePathScript]);
    }
}


/**
 * convert datetime to date
 */
if (!function_exists('gc_datetime_to_date') && !in_array('gc_datetime_to_date', config('helper_except', []))) {
    function gc_datetime_to_date($datetime, $format = 'Y-m-d')
    {
        if (empty($datetime)) {
            return null;
        }
        return  date($format, strtotime($datetime));
    }
}


if (!function_exists('admin') && !in_array('admin', config('helper_except', []))) {
    /**
     * Admin login information
     */
    function admin()
    {
        return auth()->guard('admin');
    }
}

if (!function_exists('gc_sync_cart') && !in_array('gc_sync_cart', config('helper_except', []))) {
    /**
     * Sync data cart
     */
    function gc_sync_cart($userId)
    {
        //Process sync cart data and session
        $cartDB = \Glumbo\Gracart\Library\ShoppingCart\CartModel::where('identifier', $userId)
            ->where('store_id', config('app.storeId'))
            ->get()->keyBy('instance');
        $arrayInstance = ['default', 'wishlist', 'compare'];
        if ($cartDB) {
            foreach ($cartDB as $instance => $cartInstance) {
                $arrayInstance = array_diff($arrayInstance, [$instance]);
                $content = json_decode($cartInstance->content, true);
                if ($content) {
                    foreach ($content as $key => $dataItem) {
                        \Cart::instance($instance)->add($dataItem);
                    }
                }
            }
        }

        if ($arrayInstance) {
            foreach ($arrayInstance as  $instance) {
                if (\Cart::instance($instance)->content()->count()) {
                    \Cart::instance($instance)->saveDatabase($userId);
                }
            }
        }
    }
}

if (!function_exists('gc_time_now') && !in_array('gc_time_now', config('helper_except', []))) {
    /**
     * Return object carbon
     */
    function gc_time_now($timezone = "")
    {
        if(empty($timezone)){
            $timezone = config('app.timezone');
        }
        return (new \Carbon\Carbon)->now($timezone);
    }
}

if (!function_exists('gc_request') && !in_array('gc_request', config('helper_except', []))) {
    /**
     * Return object carbon
     */
    function gc_request($key = "", $default = "", string $type = "")
    {

        if ($type == 'string') {
            if (is_array(request($key, $default))) {
                return 'array';
            }
        }

        if ($type == 'array') {
            if (is_string(request($key, $default))) {
                return [request($key, $default)];
            }
        }

        return request($key, $default);
    }
}
