<?php
namespace Glumbo\Gracart\Admin\Controllers;

use Glumbo\Gracart\Admin\Controllers\RootAdminController;
use Glumbo\Gracart\Front\Models\ShopStoreCss;
use Glumbo\Gracart\Admin\Models\AdminTemplate;
use Glumbo\Gracart\Admin\Models\AdminStore;
class AdminStoreCssController extends RootAdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Form edit
     */
    public function index()
    {
        $storeId = request('store_id', session('adminStoreId'));
        $store     = AdminStore::find($storeId);
        $templates = (new AdminTemplate)->getListTemplate();
        $template = $store->template;
        if (!key_exists($template, $templates)) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }

        $cssContent = ShopStoreCss::where('store_id', $storeId)
            ->where('template', $template)
            ->first();
        if (!$cssContent) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }
        $data = [
            'title' => gc_language_render('store.admin.css').' #'.$storeId,
            'subTitle' => '',
            'title_description' => '',
            'template' => $template,
            'templates' => $templates,
            'storeId' => $storeId,
            'icon' => 'fa fa-edit',
            'css' => $cssContent->css,
            'store_css' => ['css' => $cssContent->css],
            'url_action' => gc_route_admin('admin_store_css.index'),
        ];
        return view($this->templatePathAdmin.'screen.store_css')
            ->with($data);
    }
    
    /**
     * update css template
     */
    public function postEdit()
    {
        $data = request()->all();
        $storeId = $data['storeId'];
        $template = $data['template'];
        $cssContent = ShopStoreCss::where('store_id', $storeId)->where('template', $template)->first();
        $cssContent->css = request('css');
        $cssContent->save();
        return redirect()->route('admin_store_css.index', ['store_id' => $storeId])->with('success', gc_language_render('action.edit_success'));
    }
}
