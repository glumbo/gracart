<?php

use Glumbo\Gracart\Front\Models\ShopLanguage;
use Illuminate\Support\Str;

if (!function_exists('gc_language_all') && !in_array('gc_language_all', config('helper_except', []))) {
    //Get all language
    function gc_language_all()
    {
        return ShopLanguage::getListActive();
    }
}

if (!function_exists('gc_languages') && !in_array('gc_languages', config('helper_except', []))) {
    /*
    Render language
    WARNING: Dont call this function (or functions that call it) in __construct or midleware, it may cause the display language to be incorrect
     */
    function gc_languages($locale)
    {
        $languages = \Glumbo\Gracart\Front\Models\Languages::getListAll($locale);
        return $languages;
    }
}

if (!function_exists('gc_language_replace') && !in_array('gc_language_replace', config('helper_except', []))) {
    /*
    Replace language
     */
    function gc_language_replace(string $line, array $replace)
    {
        foreach ($replace as $key => $value) {
            $line = str_replace(
                [':'.$key, ':'.Str::upper($key), ':'.Str::ucfirst($key)],
                [$value, Str::upper($value), Str::ucfirst($value)],
                $line
            );
        }
        return $line;
    }
}


if (!function_exists('gc_language_render') && !in_array('gc_language_render', config('helper_except', []))) {
    /*
    Render language
    WARNING: Dont call this function (or functions that call it) in __construct or midleware, it may cause the display language to be incorrect
     */
    function gc_language_render($string, array $replace = [], $locale = null)
    {
        $locale = $locale ? $locale : gc_get_locale();
        $languages = gc_languages($locale);
        return !empty($languages[$string]) ? gc_language_replace($languages[$string], $replace): trans($string, $replace);
    }
}


if (!function_exists('gc_language_quickly') && !in_array('gc_language_quickly', config('helper_except', []))) {
    /*
    Language quickly
     */
    function gc_language_quickly($string, $default = null)
    {
        $locale = gc_get_locale();
        $languages = gc_languages($locale);
        return !empty($languages[$string]) ? $languages[$string] : (\Lang::has($string) ? trans($string) : $default);
    }
}

if (!function_exists('gc_get_locale') && !in_array('gc_get_locale', config('helper_except', []))) {
    /*
    Get locale
    */
    function gc_get_locale()
    {
        return app()->getLocale();
    }
}


if (!function_exists('gc_lang_switch') && !in_array('gc_lang_switch', config('helper_except', []))) {
    /**
     * Switch language
     *
     * @param   [string]  $lang
     *
     * @return  [mix]
     */
    function gc_lang_switch($lang = null)
    {
        if (!$lang) {
            return ;
        }

        $languages = gc_language_all()->keys()->all();
        if (in_array($lang, $languages)) {
            app()->setLocale($lang);
            session(['locale' => $lang]);
        } else {
            return abort(404);
        }
    }
}
