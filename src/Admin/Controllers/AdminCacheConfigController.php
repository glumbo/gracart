<?php
namespace Glumbo\Gracart\Admin\Controllers;

use Glumbo\Gracart\Admin\Controllers\RootAdminController;
use Glumbo\Gracart\Admin\Models\AdminConfig;

class AdminCacheConfigController extends RootAdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $data = [
            'title' => gc_language_render('admin.cache.title'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',        ];
        $configs = AdminConfig::getListConfigByCode(['code' => 'cache']);
        $data['configs'] = $configs;
        $data['urlUpdateConfigGlobal'] = gc_route_admin('admin_config_global.update');
        return view($this->templatePathAdmin.'screen.cache_config')
            ->with($data);
    }

    /**
     * Clear cache
     *
     * @return  json
     */
    public function clearCache()
    {
        $action = request('action');
        $response = gc_clear_cache($action);
        return response()->json(
            $response
        );
    }
}
