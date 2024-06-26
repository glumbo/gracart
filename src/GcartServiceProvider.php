<?php

namespace Glumbo\Gracart;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Glumbo\Gracart\Front\Models\ShopProduct;
use Glumbo\Gracart\Front\Models\ShopCategory;
use Glumbo\Gracart\Front\Models\ShopBanner;
use Glumbo\Gracart\Front\Models\ShopBrand;
use Glumbo\Gracart\Front\Models\ShopNews;
use Glumbo\Gracart\Front\Models\ShopPage;
use Glumbo\Gracart\Front\Models\ShopStore;
use Glumbo\Gracart\Commands\Customize;
use Glumbo\Gracart\Commands\Backup;
use Glumbo\Gracart\Commands\Restore;
use Glumbo\Gracart\Commands\Make;
use Glumbo\Gracart\Commands\Infomation;
use Glumbo\Gracart\Commands\ClearCart;
use Glumbo\Gracart\Commands\Update;
use Glumbo\Gracart\Admin\Models\AdminProduct;
use Glumbo\Gracart\Front\Middleware\Localization;
use Glumbo\Gracart\Front\Middleware\EmailIsVerified;
use Glumbo\Gracart\Front\Middleware\Currency;
use Glumbo\Gracart\Api\Middleware\ApiConnection;
use Glumbo\Gracart\Api\Middleware\ForceJsonResponse;
use Glumbo\Gracart\Front\Middleware\CheckDomain;
use Glumbo\Gracart\Admin\Middleware\Authenticate;
use Glumbo\Gracart\Admin\Middleware\LogOperation;
use Glumbo\Gracart\Admin\Middleware\PermissionMiddleware;
use Glumbo\Gracart\Admin\Middleware\AdminStoreId;
use Glumbo\Gracart\Admin\Middleware\AdminTheme;
use Laravel\Sanctum\Sanctum;
use Glumbo\Gracart\Front\Models\PersonalAccessToken;

class GcartServiceProvider extends ServiceProvider
{
    protected $commands = [
        Customize::class,
        Backup::class,
        Restore::class,
        Make::class,
        Infomation::class,
        ClearCart::class,
        Update::class,
    ];

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if(file_exists(base_path('bootstrap/cache/routes-v7.php'))) {
            echo ('<div style="color:red;font-size:10px; background:black;z-index:99999;position:fixed; top:1px;">Sorry!! SC cannot use route cache. Please delete the file "bootstrap/cache/routes-v7.php" or use the command "php artisan route:clear""</div>');
        }
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        $this->loadTranslationsFrom(__DIR__.'/Lang', 'grakan');

        //Load helper from front
        try {
            foreach (glob(__DIR__.'/Library/Helpers/*.php') as $filename) {
                require_once $filename;
            }
        } catch (\Throwable $e) {
            $msg = '#SC001::Message: ' .$e->getMessage().' - Line: '.$e->getLine().' - File: '.$e->getFile();
            // gc_report($msg);
            echo $msg;
            exit;
        }

        if (!file_exists(public_path('install.php')) && file_exists(base_path('.env'))) {

            //Check connection
            try {
                DB::connection(GC_CONNECTION)->getPdo();
            } catch (\Throwable $e) {
                $msg = '#SC003::Message: ' .$e->getMessage().' - Line: '.$e->getLine().' - File: '.$e->getFile();
                gc_report($msg);
                echo $msg;
                exit;
            }
            //Load Plugin Provider
            try {
                foreach (glob(app_path() . '/Plugins/*/*/Provider.php') as $filename) {
                    require_once $filename;
                }
            } catch (\Throwable $e) {
                $msg = '#SC004::Message: ' .$e->getMessage().' - Line: '.$e->getLine().' - File: '.$e->getFile();
                gc_report($msg);
                echo $msg;
                exit;
            }

            //Boot process S-Cart
            try {
                $this->bootScart();
            } catch (\Throwable $e) {
                $msg = '#SC005::Message: ' .$e->getMessage().' - Line: '.$e->getLine().' - File: '.$e->getFile();
                gc_report($msg);
                echo $msg;
                exit;
            }

            //Route Admin
            try {
                if (file_exists($routes = __DIR__.'/Admin/routes.php')) {
                    $this->loadRoutesFrom($routes);
                }
            } catch (\Throwable $e) {
                $msg = '#SC006::Message: ' .$e->getMessage().' - Line: '.$e->getLine().' - File: '.$e->getFile();
                gc_report($msg);
                echo $msg;
                exit;
            }

            //Route Api
            try {
                if (gc_config_global('api_mode')) {
                    if (file_exists($routes = __DIR__.'/Api/routes.php')) {
                        $this->loadRoutesFrom($routes);
                    }
                }
            } catch (\Throwable $e) {
                $msg = '#SC007::Message: ' .$e->getMessage().' - Line: '.$e->getLine().' - File: '.$e->getFile();
                gc_report($msg);
                echo $msg;
                exit;
            }

            //Route Front
            try {
                if (file_exists($routes = __DIR__.'/Front/routes.php')) {
                    $this->loadRoutesFrom($routes);
                }
            } catch (\Throwable $e) {
                $msg = '#SC008::Message: ' .$e->getMessage().' - Line: '.$e->getLine().' - File: '.$e->getFile();
                gc_report($msg);
                echo $msg;
                exit;
            }
        }

        try {
            $this->registerPublishing();
        } catch (\Throwable $e) {
            $msg = '#SC009::Message: ' .$e->getMessage().' - Line: '.$e->getLine().' - File: '.$e->getFile();
            gc_report($msg);
            echo $msg;
            exit;
        }
        
        try {
            $this->registerRouteMiddleware();
        } catch (\Throwable $e) {
            $msg = '#SC010::Message: ' .$e->getMessage().' - Line: '.$e->getLine().' - File: '.$e->getFile();
            gc_report($msg);
            echo $msg;
            exit;
        }
        
        try {
            $this->commands($this->commands);
        } catch (\Throwable $e) {
            $msg = '#SC011::Message: ' .$e->getMessage().' - Line: '.$e->getLine().' - File: '.$e->getFile();
            gc_report($msg);
            echo $msg;
            exit;
        }
        
        try {
            $this->validationExtend();
        } catch (\Throwable $e) {
            $msg = '#SC012::Message: ' .$e->getMessage().' - Line: '.$e->getLine().' - File: '.$e->getFile();
            gc_report($msg);
            echo $msg;
            exit;
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        Sanctum::ignoreMigrations();
        
        if (file_exists(__DIR__.'/Library/Const.php')) {
            require_once(__DIR__.'/Library/Const.php');
        }
        $this->app->bind('cart', '\Glumbo\Gracart\Library\ShoppingCart\Cart');
        
        $this->mergeConfigFrom(__DIR__.'/Config/admin.php', 'admin');
        $this->mergeConfigFrom(__DIR__.'/Config/validation.php', 'validation');
        $this->mergeConfigFrom(__DIR__.'/Config/lfm.php', 'lfm');
        $this->mergeConfigFrom(__DIR__.'/Config/gracart.php', 'gracart');
        $this->mergeConfigFrom(__DIR__.'/Config/cart.php', 'cart');
        $this->mergeConfigFrom(__DIR__.'/Config/middleware.php', 'middleware');
        $this->mergeConfigFrom(__DIR__.'/Config/api.php', 'api');
        $this->loadViewsFrom(__DIR__.'/Views/admin', 'gracart-admin');
        $this->loadViewsFrom(__DIR__.'/Views/front', 'gracart-front');
    }

    public function bootScart()
    {
        // Set store id
        // Default is domain root
        $storeId = GC_ID_ROOT;

        //Process for multi store
        if (gc_check_multi_shop_installed()) {
            $domain = gc_process_domain_store(url('/'));
            if (gc_check_multi_vendor_installed()) {
                $arrDomain = ShopStore::getDomainPartner();
            }
            if (gc_check_multi_store_installed()) {
                $arrDomain = ShopStore::getDomainStore();
            }
            if (in_array($domain, $arrDomain)) {
                $storeId =  array_search($domain, $arrDomain);
            }
        }
        //End process multi store
        config(['app.storeId' => $storeId]);
        // end set store Id

        if (gc_config_global('LOG_SLACK_WEBHOOK_URL')) {
            config(['logging.channels.slack.url' => gc_config_global('LOG_SLACK_WEBHOOK_URL')]);
        }

        //Config language url
        config(['app.seoLang' => (gc_config_global('url_seo_lang') ? '{lang?}/' : '')]);

        //Title app
        config(['app.name' => gc_store('title')]);

        //Config for  email
        if (
            // Default use smtp mode for for supplier if use multi-store
            ($storeId != GC_ID_ROOT && gc_check_multi_shop_installed())
            ||
            // Use smtp config from admin if root domain have smtp_mode enable
            ($storeId == GC_ID_ROOT && gc_config_global('smtp_mode'))
            ) {
            $smtpHost     = gc_config('smtp_host');
            $smtpPort     = gc_config('smtp_port');
            $smtpSecurity = gc_config('smtp_security');
            $smtpUser     = gc_config('smtp_user');
            $smtpPassword = gc_config('smtp_password');
            $smtpName     = gc_config('smtp_name');
            $smtpFrom     = gc_config('smtp_from');
            config(['mail.default'                 => 'smtp']);
            config(['mail.mailers.smtp.host'       => $smtpHost]);
            config(['mail.mailers.smtp.port'       => $smtpPort]);
            config(['mail.mailers.smtp.encryption' => $smtpSecurity]);
            config(['mail.mailers.smtp.username'   => $smtpUser]);
            config(['mail.mailers.smtp.password' => $smtpPassword]);
            config(['mail.from.address' => ($smtpFrom ?? gc_store('email'))]);
            config(['mail.from.name' => ($smtpName ?? gc_store('title'))]);
        } else {
            //Set default
            config(['mail.from.address' => (config('mail.from.address')) ? config('mail.from.address'): gc_store('email')]);
            config(['mail.from.name' => (config('mail.from.name')) ? config('mail.from.name'): gc_store('title')]);
        }
        //email

        //Share variable for view
        view()->share('gc_languages', gc_language_all());
        view()->share('gc_currencies', gc_currency_all());
        view()->share('gc_blocksContent', gc_store_block());
        view()->share('gc_layoutsUrl', gc_link());
//        view()->share('gc_templatePath', 'templates.backend.' . gc_store('backend_template'));
//        view()->share('gc_templateFile', 'templates/backend/' . gc_store('backend_template'));


        view()->composer('*','Glumbo\Gracart\Composers\GlobalComposer');
        view()->composer('templates.backend.*','Glumbo\Gracart\Composers\BackendComposer');
        view()->composer('templates.frontend.*','Glumbo\Gracart\Composers\FrontendComposer');


        //variable model
        view()->share('modelProduct', (new ShopProduct));
        view()->share('modelCategory', (new ShopCategory));
        view()->share('modelBanner', (new ShopBanner));
        view()->share('modelBrand', (new ShopBrand));
        view()->share('modelNews', (new ShopNews));
        view()->share('modelPage', (new ShopPage));
        //
        view()->share('templatePathAdmin', config('admin.path_view'));
    }

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'localization'     => Localization::class,
        'email.verify'     => EmailIsVerified::class,
        'currency'         => Currency::class,
        'api.connection'   => ApiConnection::class,
        'checkdomain'      => CheckDomain::class,
        'json.response'    => ForceJsonResponse::class,
        //Admin
        'admin.auth'       => Authenticate::class,
        'admin.log'        => LogOperation::class,
        'admin.permission' => PermissionMiddleware::class,
        'admin.storeId'    => AdminStoreId::class,
        'admin.theme'      => AdminTheme::class,
        //Sanctum
        'abilities' => \Laravel\Sanctum\Http\Middleware\CheckAbilities::class,
        'ability' => \Laravel\Sanctum\Http\Middleware\CheckForAnyAbility::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected function middlewareGroups()
    {
        return [
            'admin'           => config('middleware.admin'),
            'front'           => config('middleware.front'),
            'api.extend'      => config('middleware.api_extend'),
        ];
    }

    /**
     * Register the route middleware.
     *
     * @return void
     */
    protected function registerRouteMiddleware()
    {
        // register route middleware.
        foreach ($this->routeMiddleware as $key => $middleware) {
            app('router')->aliasMiddleware($key, $middleware);
        }

        // register middleware group.
        foreach ($this->middlewareGroups() as $key => $middleware) {
            app('router')->middlewareGroup($key, array_values($middleware));
        }
    }


    /**
     * Validattion extend
     *
     * @return  [type]  [return description]
     */
    protected function validationExtend()
    {
        Validator::extend('product_sku_unique', function ($attribute, $value, $parameters, $validator) {
            $productId = $parameters[0] ?? '';
            return (new AdminProduct)
                ->checkProductValidationAdmin('sku', $value, $productId, session('adminStoreId'));
        });

        Validator::extend('product_alias_unique', function ($attribute, $value, $parameters, $validator) {
            $productId = $parameters[0] ?? '';
            return (new AdminProduct)
                ->checkProductValidationAdmin('alias', $value, $productId, session('adminStoreId'));
        });
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/Views/admin'  => resource_path('views/vendor/grakan-admin')], 'sc:view-admin');
            $this->publishes([__DIR__.'/Views/front'  => resource_path('views/vendor/grakan-front')], 'sc:view-front');
            $this->publishes([__DIR__.'/Config/admin.php' => config_path('admin.php')], 'sc:config-admin');
            $this->publishes([__DIR__.'/Config/validation.php' => config_path('validation.php')], 'sc:config-validation');
            $this->publishes([__DIR__.'/Config/cart.php' => config_path('cart.php')], 'sc:config-cart');
            $this->publishes([__DIR__.'/Config/api.php' => config_path('api.php')], 'sc:config-api');
            $this->publishes([__DIR__.'/Config/middleware.php' => config_path('middleware.php')], 'sc:config-middleware');
            $this->publishes([__DIR__.'/Config/lfm.php' => config_path('lfm.php')], 'sc:config-lfm');
        }
    }
}
