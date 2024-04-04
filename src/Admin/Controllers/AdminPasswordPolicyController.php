<?php
namespace Glumbo\Gracart\Admin\Controllers;

use Glumbo\Gracart\Admin\Controllers\RootAdminController;
use Glumbo\Gracart\Admin\Models\AdminConfig;

class AdminPasswordPolicyController extends RootAdminController
{

    public function __construct()
    {
        parent::__construct();
        foreach (timezone_identifiers_list() as $key => $value) {
            $timezones[$value] = $value;
        }
    }

    public function index()
    {
        $id = GC_ID_GLOBAL;
        $data = [
            'title' => gc_language_render('admin.menu_titles.password_policy'),
            'subTitle' => '',
            'icon' => 'fas fa-cogs',
        ];

        // Customer config
        $dataPassswordPolicy = [
            'code' => 'password_policy',
            'storeId' => $id,
            'keyBy' => 'key',
            'sort' => 'asc'
        ];
        $passwordPolicy = AdminConfig::getListConfigByCode($dataPassswordPolicy);
        //End email
        $data['passwordPolicy']                = $passwordPolicy;
        $data['storeId']                        = $id;
        $data['urlUpdateConfigGlobal']          = gc_route_admin('admin_config_global.update');

        return view($this->templatePathAdmin.'screen.password_policy')
        ->with($data);
    }

    /*
    Update value config store
    */
    public function update()
    {
        $data = request()->all();
        $name = $data['name'];
        $value = $data['value'];
        $storeId = $data['storeId'] ?? '';
        if (!$storeId) {
            return response()->json(
                [
                'error' => 1,
                'field' => 'storeId',
                'value' => $storeId,
                'msg'   => 'Store ID can not empty!',
                ]
            );
        }

        try {
            AdminConfig::where('key', $name)
                ->where('store_id', $storeId)
                ->update(['value' => $value]);
            $error = 0;
            $msg = gc_language_render('action.update_success');
        } catch (\Throwable $e) {
            $error = 1;
            $msg = $e->getMessage();
        }
        return response()->json(
            [
            'error' => $error,
            'field' => $name,
            'value' => $value,
            'msg'   => $msg,
            ]
        );
    }
}
