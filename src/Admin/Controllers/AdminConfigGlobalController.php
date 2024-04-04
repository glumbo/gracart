<?php
namespace Glumbo\Gracart\Admin\Controllers;

use Glumbo\Gracart\Admin\Controllers\RootAdminController;
use Glumbo\Gracart\Admin\Models\AdminConfig;

class AdminConfigGlobalController extends RootAdminController
{
    public $templates;
    public $currencies;
    public $languages;
    public $timezones;

    public function __construct()
    {
        parent::__construct();
    }

    public function webhook()
    {
        $data = [
            'title' => gc_language_render('admin.config.webhook'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
        ];
        return view($this->templatePathAdmin.'screen.webhook')
            ->with($data);
    }

    /**
     * Update config global
     *
     * @return  [type]  [return description]
     */
    public function update()
    {
        $data = request()->all();
        $name = $data['name'];
        $value = $data['value'];
        try {
            AdminConfig::where('key', $name)
                ->where('store_id', GC_ID_GLOBAL)
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
