<?php
namespace Glumbo\Gracart\Admin\Controllers;

use Glumbo\Gracart\Admin\Controllers\RootAdminController;
use Validator;
use Glumbo\Gracart\Admin\Models\AdminBanner;
use Glumbo\Gracart\Front\Models\ShopBannerType;
use Glumbo\Gracart\Front\Models\ShopCustomField;

class AdminBannerController extends RootAdminController
{
    protected $arrTarget;
    protected $dataType;
    public function __construct()
    {
        parent::__construct();
        $this->arrTarget = ['_blank' => '_blank', '_self' => '_self'];
        $this->dataType  = (new ShopBannerType)->pluck('name', 'code')->all();
        if (gc_check_multi_vendor_installed()) {
            $this->dataType['background-store'] = 'Background store';
            $this->dataType['breadcrumb-store'] = 'Breadcrumb store';
        }
        ksort($this->dataType);
    }

    public function index()
    {
        $data = [
            'title'         => gc_language_render('admin.banner.list'),
            'subTitle'      => '',
            'icon'          => 'fa fa-indent',
            'urlDeleteItem' => gc_route_admin('admin_banner.delete'),
            'removeList'    => 0, // 1 - Enable function delete list item
            'buttonRefresh' => 0, // 1 - Enable button refresh
            'css'           => '',
            'js'            => '',
        ];

        //Process add content
        $data['menuRight']    = gc_config_group('menuRight', \Request::route()->getName());
        $data['menuLeft']     = gc_config_group('menuLeft', \Request::route()->getName());
        $data['topMenuRight'] = gc_config_group('topMenuRight', \Request::route()->getName());
        $data['topMenuLeft']  = gc_config_group('topMenuLeft', \Request::route()->getName());
        $data['blockBottom']  = gc_config_group('blockBottom', \Request::route()->getName());

        $listTh = [
            'image'  => gc_language_render('admin.banner.image'),
            'title'  => gc_language_render('admin.banner.title'),
            'url'    => gc_language_render('admin.banner.url'),
            'sort'   => gc_language_render('admin.banner.sort'),
            'status' => gc_language_render('admin.banner.status'),
            'click'  => gc_language_render('admin.banner.click'),
            'target' => gc_language_render('admin.banner.target'),
            'type'   => gc_language_render('admin.banner.type'),
        ];
        if (gc_check_multi_shop_installed() && session('adminStoreId') == GC_ID_ROOT) {
            // Only show store info if store is root
            $listTh['shop_store'] = gc_language_render('front.store_list');
        }
        $listTh['action'] = gc_language_render('action.title');

        $sort_order = gc_clean(request('sort_order') ?? 'id_desc');
        $keyword    = gc_clean(request('keyword') ?? '');
        $arrSort = [
            'id__desc' => gc_language_render('filter_sort.id_desc'),
            'id__asc' => gc_language_render('filter_sort.id_asc'),
        ];
        
        $dataSearch = [
            'keyword'    => $keyword,
            'sort_order' => $sort_order,
            'arrSort'    => $arrSort,
        ];
        $dataTmp = AdminBanner::getBannerListAdmin($dataSearch);

        if (gc_check_multi_shop_installed() && session('adminStoreId') == GC_ID_ROOT) {
            $arrId = $dataTmp->pluck('id')->toArray();
            // Only show store info if store is root
            if (function_exists('gc_get_list_store_of_banner')) {
                $dataStores = gc_get_list_store_of_banner($arrId);
            } else {
                $dataStores = [];
            }
        }

        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataMap = [
                'image' => gc_image_render($row->getThumb(), '', '50px', 'Banner'),
                'title' => $row['title'],
                'url' => $row['url'],
                'sort' => $row['sort'],
                'status' => $row['status'] ? '<span class="badge badge-light-success">ON</span>' : '<span class="badge badge-light-danger">OFF</span>',
                'click' => number_format($row['click']),
                'target' => $row['target'],
                'type' => $this->dataType[$row['type']]??'N/A',
            ];

            if (gc_check_multi_shop_installed() && session('adminStoreId') == GC_ID_ROOT) {
                // Only show store info if store is root
                if (!empty($dataStores[$row['id']])) {
                    $storeTmp = $dataStores[$row['id']]->pluck('code', 'id')->toArray();
                    $storeTmp = array_map(function ($code) {
                        return '<a target=_new href="'.gc_get_domain_from_code($code).'">'.$code.'</a>';
                    }, $storeTmp);
                    $dataMap['shop_store'] = '<i class="nav-icon fab fa-shopify"></i> '.implode('<br><i class="nav-icon fab fa-shopify"></i> ', $storeTmp);
                } else {
                    $dataMap['shop_store'] = '';
                }
            }
            $dataMap['action'] = '<a href="' . gc_route_admin('admin_banner.edit', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '"><span title="' . gc_language_render('action.edit') . '" type="button" class="btn btn-sm btn-light btn-active-primary"><i class="fa fa-edit"></i></span></a>&nbsp;
            <span onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . gc_language_render('action.delete') . '" class="btn btn-sm btn-light btn-active-danger"><i class="fas fa-trash-alt"></i></span>
            ';
            $dataTr[$row['id']] = $dataMap;
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin.'component.pagination');
        $data['resultItems'] = gc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' =>  $dataTmp->total()]);

        //menuRight
        $data['menuRight'][] = '<a href="' . gc_route_admin('admin_banner.create') . '" class="btn btn-sm btn-light btn-active-primary" title="New" id="button_create_new">
        <i class="fa fa-plus" title="'.gc_language_render('action.add').'"></i>'.gc_language_render('action.add').'
                           </a>';
        //=menuRight

        //menuSort
        $optionSort = '';
        foreach ($arrSort as $key => $status) {
            $optionSort .= '<option  ' . (($sort_order == $key) ? "selected" : "") . ' value="' . $key . '">' . $status . '</option>';
        }
        //=menuSort


        //menuSearch
        $data['topMenuRight'][] = '
        <form action="' . gc_route_admin('admin_banner.index') . '" id="button_search">

            <div class="input-group float-start" style="width: 350px;">
                <select class="form-control rounded-0" name="sort_order" id="sort_order">
                '.$optionSort.'
                </select> &nbsp;
        
                <input type="text" name="keyword" class="form-control rounded-0 float-end" placeholder="' . gc_language_render('search.placeholder') . '" value="' . $keyword . '">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </form>';
        //=menuSearch



        return view($this->templatePathAdmin.'screen.list')
            ->with($data);
    }

    /**
     * Form create new item in admin
     * @return [type] [description]
     */
    public function create()
    {
        $data = [
            'title'             => gc_language_render('admin.banner.add_new'),
            'subTitle'          => '',
            'title_description' => '',
            'icon'              => 'fa fa-plus',
            'banner'            => [],
            'arrTarget'         => $this->arrTarget,
            'dataType'          => $this->dataType,
            'url_action'        => gc_route_admin('admin_banner.create'),
            'customFields'      => (new ShopCustomField)->getCustomField($type = 'shop_banner'),
        ];
        return view($this->templatePathAdmin.'screen.banner')
            ->with($data);
    }

    /**
     * Post create new item in admin
     * @return [type] [description]
     */
    public function postCreate()
    {
        $data = request()->all();
        $dataOrigin = request()->all();
        $arrValidation = [
            'sort' => 'numeric|min:0',
        ];
        //Custom fields
        $customFields = (new ShopCustomField)->getCustomField($type = 'shop_banner');
        if ($customFields) {
            foreach ($customFields as $field) {
                if ($field->required) {
                    $arrValidation['fields.'.$field->code] = 'required';
                }
            }
        }
        $validator = Validator::make($dataOrigin, $arrValidation);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $dataCreate = [
            'image'    => $data['image'],
            'url'      => $data['url'],
            'title'    => $data['title'],
            'html'     => $data['html'],
            'type'     => $data['type'] ?? 0,
            'target'   => $data['target'],
            'status'   => empty($data['status']) ? 0 : 1,
            'sort'     => (int) $data['sort'],
        ];
        $dataCreate = gc_clean($dataCreate, ['html'], true);
        $banner = AdminBanner::createBannerAdmin($dataCreate);

        $shopStore        = $data['shop_store'] ?? [session('adminStoreId')];
        $banner->stores()->detach();
        if ($shopStore) {
            $banner->stores()->attach($shopStore);
        }

        //Insert custom fields
        $fields = $data['fields'] ?? [];
        gc_update_custom_field($fields, $banner->id, 'shop_banner');

        return redirect()->route('admin_banner.index')->with('success', gc_language_render('action.create_success'));
    }

    /**
     * Form edit
     */
    public function edit($id)
    {
        $banner = AdminBanner::getBannerAdmin($id);

        if (!$banner) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }

        $data = [
            'title'             => gc_language_render('action.edit'),
            'subTitle'          => '',
            'title_description' => '',
            'icon'              => 'fa fa-edit',
            'arrTarget'         => $this->arrTarget,
            'dataType'          => $this->dataType,
            'banner'            => $banner,
            'url_action'        => gc_route_admin('admin_banner.edit', ['id' => $banner['id']]),
            'customFields'      => (new ShopCustomField)->getCustomField($type = 'shop_banner'),
        ];
        return view($this->templatePathAdmin.'screen.banner')
            ->with($data);
    }

    /*
     * update status
     */
    public function postEdit($id)
    {
        $banner = AdminBanner::getBannerAdmin($id);
        if (!$banner) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }

        $data = request()->all();
        $dataOrigin = request()->all();
        $arrValidation = [
            'sort' => 'numeric|min:0',
        ];
        //Custom fields
        $customFields = (new ShopCustomField)->getCustomField($type = 'shop_banner');
        if ($customFields) {
            foreach ($customFields as $field) {
                if ($field->required) {
                    $arrValidation['fields.'.$field->code] = 'required';
                }
            }
        }

        $validator = Validator::make($dataOrigin, $arrValidation);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        //Edit
        $dataUpdate = [
            'image'    => $data['image'],
            'url'      => $data['url'],
            'title'    => $data['title'],
            'html'     => $data['html'],
            'type'     => $data['type'] ?? 0,
            'target'   => $data['target'],
            'status'   => empty($data['status']) ? 0 : 1,
            'sort'     => (int) $data['sort'],
        ];
        $dataUpdate = gc_clean($dataUpdate, ['html'], true);
        $banner->update($dataUpdate);

        $shopStore        = $data['shop_store'] ?? [session('adminStoreId')];
        $banner->stores()->detach();
        if ($shopStore) {
            $banner->stores()->attach($shopStore);
        }

        //Insert custom fields
        $fields = $data['fields'] ?? [];
        gc_update_custom_field($fields, $banner->id, 'shop_banner');

        return redirect()->route('admin_banner.index')->with('success', gc_language_render('action.edit_success'));
    }

    /*
    Delete list item
    Need mothod destroy to boot deleting in model
    */
    public function deleteList()
    {
        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => gc_language_render('admin.method_not_allow')]);
        } else {
            $ids = request('ids');
            $arrID = explode(',', $ids);
            $arrDontPermission = [];
            foreach ($arrID as $key => $id) {
                if (!$this->checkPermisisonItem($id)) {
                    $arrDontPermission[] = $id;
                }
            }
            if (count($arrDontPermission)) {
                return response()->json(['error' => 1, 'msg' => gc_language_render('admin.remove_dont_permisison') . ': ' . json_encode($arrDontPermission)]);
            }

            AdminBanner::destroy($arrID);
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    /**
     * Check permisison item
     */
    public function checkPermisisonItem($id)
    {
        return AdminBanner::getBannerAdmin($id);
    }
}
