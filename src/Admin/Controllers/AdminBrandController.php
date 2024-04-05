<?php
namespace Glumbo\Gracart\Admin\Controllers;

use Glumbo\Gracart\Admin\Controllers\RootAdminController;
use Glumbo\Gracart\Front\Models\ShopBrand;
use Glumbo\Gracart\Front\Models\ShopCustomField;
use Validator;

class AdminBrandController extends RootAdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $data = [
            'title' => gc_language_render('admin.brand.list'),
            'title_action' => '<i class="fa fa-plus" aria-hidden="true"></i> ' . gc_language_render('admin.brand.add_new_title'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => gc_route_admin('admin_brand.delete'),
            'removeList' => 0, // 1 - Enable function delete list item
            'buttonRefresh' => 0, // 1 - Enable button refresh
            'css' => '',
            'js' => '',
            'url_action' => gc_route_admin('admin_brand.create'),
            'customFields'      => (new ShopCustomField)->getCustomField($type = 'shop_brand'),
        ];

        $listTh = [
            'name' => gc_language_render('admin.brand.name'),
            'image' => gc_language_render('admin.brand.image'),
            'status' => gc_language_render('admin.brand.status'),
        ];

        if (gc_check_multi_shop_installed() && session('adminStoreId') == GC_ID_ROOT) {
            // Only show store info if store is root
            $listTh['shop_store'] = gc_language_render('front.store_list');
        }
        $listTh['action'] = gc_language_render('action.title');

        $obj = new ShopBrand;
        $obj = $obj->orderBy('id', 'desc');
        $dataTmp = $obj->paginate(20);

        if (gc_check_multi_shop_installed() && session('adminStoreId') == GC_ID_ROOT) {
            $arrId = $dataTmp->pluck('id')->toArray();
            // Only show store info if store is root
            if (function_exists('gc_get_list_store_of_brand')) {
                $dataStores = gc_get_list_store_of_brand($arrId);
            } else {
                $dataStores = [];
            }
        }

        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataMap = [
                'name' => $row['name'],
                'image' => gc_image_render($row->getThumb(), '50px', '', $row['name']),
                'status' => $row['status'] ? '<span class="badge badge-success">ON</span>' : '<span class="badge badge-danger">OFF</span>',
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
            $dataMap['action'] = '<a href="' . gc_route_admin('admin_brand.edit', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '"><span title="' . gc_language_render('action.edit') . '" type="button" class="btn btn-flat btn-sm btn-primary"><i class="fa fa-edit"></i></span></a>&nbsp;
                                <span onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . gc_language_render('action.delete') . '" class="btn btn-flat btn-sm btn-danger"><i class="fas fa-trash-alt"></i></span>
                                ';
//            $dataMap['action'] = view($this->templatePathAdmin.'component.actions.action', ['edit_text' => gc_language_render('action.edit'),'edit_url' => gc_route_admin('admin_brand.edit', ['id' => $row['id'] ? $row['id'] : 'not-found-id']), 'delete_id' => $row['id'], 'delete_text' => gc_language_render('action.delete')])->render();

            $dataTr[$row['id']] = $dataMap;
        }



        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin.'component.pagination');
        $data['resultItems'] = gc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' =>  $dataTmp->total()]);

        $data['layout'] = 'index';
        return view($this->templatePathAdmin.'screen.brand')
            ->with($data);
    }


    /**
     * Post create new item in admin
     * @return [type] [description]
     */
    public function postCreate()
    {
        $data = request()->all();

        $data['alias'] = !empty($data['alias'])?$data['alias']:$data['name'];
        $data['alias'] = gc_word_format_url($data['alias']);
        $data['alias'] = gc_word_limit($data['alias'], 100);
        $arrValidation = [
            'name'  => 'required|string|max:100',
            'alias' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|unique:"'.ShopBrand::class.'",alias|string|max:100',
            'image' => 'required',
            'sort'  => 'numeric|min:0',
            'url'   => 'url|nullable',
        ];
        //Custom fields
        $customFields = (new ShopCustomField)->getCustomField($type = 'shop_brand');
        if ($customFields) {
            foreach ($customFields as $field) {
                if ($field->required) {
                    $arrValidation['fields.'.$field->code] = 'required';
                }
            }
        }

        $validator = Validator::make($data, $arrValidation, [
            'name.required' => gc_language_render('validation.required', ['attribute' => gc_language_render('admin.brand.name')]),
            'alias.regex' => gc_language_render('admin.brand.alias_validate'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($data);
        }
        $dataCreate = [
            'image' => $data['image'],
            'name' => $data['name'],
            'alias' => $data['alias'],
            'url' => $data['url'],
            'sort' => (int) $data['sort'],
            'status' => (!empty($data['status']) ? 1 : 0),
        ];
        $dataCreate = gc_clean($dataCreate, [], true);
        $brand = ShopBrand::create($dataCreate);

        $shopStore        = $data['shop_store'] ?? [session('adminStoreId')];
        $brand->stores()->detach();
        if ($shopStore) {
            $brand->stores()->attach($shopStore);
        }

        //Insert custom fields
        $fields = $data['fields'] ?? [];
        gc_update_custom_field($fields, $brand->id, 'shop_brand');

        return redirect()->route('admin_brand.index')->with('success', gc_language_render('action.create_success'));
    }

    /**
     * Form edit
     */
    public function edit($id)
    {
        $brand = ShopBrand::find($id);
        if (!$brand) {
            return 'No data';
        }
        $data = [
        'title' => gc_language_render('admin.brand.list'),
        'title_action' => '<i class="fa fa-edit" aria-hidden="true"></i> ' . gc_language_render('action.edit'),
        'subTitle' => '',
        'icon' => 'fa fa-indent',
        'urlDeleteItem' => gc_route_admin('admin_brand.delete'),
        'removeList' => 0, // 1 - Enable function delete list item
        'buttonRefresh' => 0, // 1 - Enable button refresh
        'css' => '',
        'js' => '',
        'url_action' => gc_route_admin('admin_brand.edit', ['id' => $brand['id']]),
        'brand' => $brand,
        'id' => $id,
        'customFields'      => (new ShopCustomField)->getCustomField($type = 'shop_brand'),
    ];

        $listTh = [
        'name' => gc_language_render('admin.brand.name'),
        'image' => gc_language_render('admin.brand.image'),
        'sort' => gc_language_render('admin.brand.sort'),
        'status' => gc_language_render('admin.brand.status'),
        'action' => gc_language_render('action.title'),
    ];
        $obj = new ShopBrand;
        $obj = $obj->orderBy('id', 'desc');
        $dataTmp = $obj->paginate(20);

        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataTr[$row['id']] = [
            'name' => $row['name'],
            'image' => gc_image_render($row->getThumb(), '50px', '', $row['name']),
            'sort' => $row['sort'],
            'status' => $row['status'] ? '<span class="badge badge-success">ON</span>' : '<span class="badge badge-danger">OFF</span>',
            'action' => '
                <a href="' . gc_route_admin('admin_brand.edit', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '"><span title="' . gc_language_render('action.edit') . '" type="button" class="btn btn-flat btn-sm btn-primary"><i class="fa fa-edit"></i></span></a>&nbsp;

              <span onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . gc_language_render('action.delete') . '" class="btn btn-flat btn-sm btn-danger"><i class="fas fa-trash-alt"></i></span>
              ',
        ];
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin.'component.pagination');
        $data['resultItems'] = gc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' =>  $dataTmp->total()]);

        $data['layout'] = 'edit';
        return view($this->templatePathAdmin.'screen.brand')
        ->with($data);
    }


    /**
     * update status
     */
    public function postEdit($id)
    {
        $brand = ShopBrand::find($id);
        $data = request()->all();
        $data['alias'] = !empty($data['alias'])?$data['alias']:$data['name'];
        $data['alias'] = gc_word_format_url($data['alias']);
        $data['alias'] = gc_word_limit($data['alias'], 100);
        $arrValidation = [
            'name'  => 'required|string|max:100',
            'alias' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|unique:"'.ShopBrand::class.'",alias,' . $brand->id . ',id|string|max:100',
            'image' => 'required',
            'sort'  => 'numeric|min:0',
        ];
        //Custom fields
        $customFields = (new ShopCustomField)->getCustomField($type = 'shop_brand');
        if ($customFields) {
            foreach ($customFields as $field) {
                if ($field->required) {
                    $arrValidation['fields.'.$field->code] = 'required';
                }
            }
        }
        $validator = Validator::make($data, $arrValidation, [
            'name.required' => gc_language_render('validation.required', ['attribute' => gc_language_render('admin.brand.name')]),
            'alias.regex' => gc_language_render('admin.brand.alias_validate'),
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($data);
        }
        //Edit

        $dataUpdate = [
            'image' => $data['image'],
            'name' => $data['name'],
            'alias' => $data['alias'],
            'url' => $data['url'],
            'sort' => (int) $data['sort'],
            'status' => (!empty($data['status']) ? 1 : 0),

        ];
        $dataUpdate = gc_clean($dataUpdate, [], true);
        $brand->update($dataUpdate);

        $shopStore        = $data['shop_store'] ?? [session('adminStoreId')];
        $brand->stores()->detach();
        if ($shopStore) {
            $brand->stores()->attach($shopStore);
        }

        //Insert custom fields
        $fields = $data['fields'] ?? [];
        gc_update_custom_field($fields, $brand->id, 'shop_brand');

        return redirect()->back()->with('success', gc_language_render('action.edit_success'));
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
            ShopBrand::destroy($arrID);
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }
}
