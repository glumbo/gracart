<?php

if (!function_exists('gc_captcha_method') && !in_array('gc_captcha_method', config('helper_except', []))) {
    function gc_captcha_method()
    {
        //If function captcha disable or dont setup
        if (empty(gc_config('captcha_mode'))) {
            return null;
        }

        // If method captcha selected
        if (!empty(gc_config('captcha_method'))) {
            $moduleClass = gc_config('captcha_method');
            //If class plugin captcha exist
            if (class_exists($moduleClass)) {
                //Check plugin captcha disable
                $key = (new $moduleClass)->configKey;
                if (gc_config($key)) {
                    return (new $moduleClass);
                } else {
                    return null;
                }
            }
        }
        return null;
    }
}

if (!function_exists('gc_captcha_page') && !in_array('gc_captcha_page', config('helper_except', []))) {
    function gc_captcha_page():array
    {
        if (empty(gc_config('captcha_page'))) {
            return [];
        }

        if (!empty(gc_config('captcha_page'))) {
            return json_decode(gc_config('captcha_page'));
        }
    }
}

if (!function_exists('gc_get_plugin_captcha_installed') && !in_array('gc_get_plugin_captcha_installed', config('helper_except', []))) {
    /**
     * Get all class plugin captcha installed
     *
     * @param   [string]  $code  Payment, Shipping
     *
     */
    function gc_get_plugin_captcha_installed($onlyActive = true)
    {
        $listPluginInstalled =  \Glumbo\Gracart\Admin\Models\AdminConfig::getPluginCaptchaCode($onlyActive);
        $arrPlugin = [];
        if ($listPluginInstalled) {
            foreach ($listPluginInstalled as $key => $plugin) {
                $keyPlugin = gc_word_format_class($plugin->key);
                $pathPlugin = app_path() . '/Plugins/Other/'.$keyPlugin;
                $nameSpaceConfig = '\App\Plugins\Other\\'.$keyPlugin.'\AppConfig';
                if (file_exists($pathPlugin . '/AppConfig.php') && class_exists($nameSpaceConfig)) {
                    $arrPlugin[$nameSpaceConfig] = gc_language_render($plugin->detail);
                }
            }
        }
        return $arrPlugin;
    }
}
