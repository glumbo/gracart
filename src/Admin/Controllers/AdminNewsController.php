<?php
namespace Glumbo\Gracart\Admin\Controllers;

use Glumbo\Gracart\Admin\Controllers\RootAdminController;
use Glumbo\Gracart\Front\Models\ShopLanguage;
use Glumbo\Gracart\Admin\Models\AdminNews;
use Glumbo\Gracart\Front\Models\ShopCustomField;
use Validator;

class AdminNewsController extends RootAdminController
{
    public $languages;

    public function __construct()
    {
        parent::__construct();
        $this->languages = ShopLanguage::getListActive();
    }

    public function index()
    {
        $data = [
            'title'         => gc_language_render('admin.news.list'),
            'subTitle'      => '',
            'icon'          => 'fa fa-indent',
            'urlDeleteItem' => gc_route_admin('admin_news.delete'),
            'removeList'    => 1, // 1 - Enable function delete list item
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
            'title'  => gc_language_render('admin.news.title'),
            'image'  => gc_language_render('admin.news.image'),
            'sort'   => gc_language_render('admin.news.sort'),
            'status' => gc_language_render('admin.news.status'),
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
            'title__desc' => gc_language_render('filter_sort.title_desc'),
            'title__asc' => gc_language_render('filter_sort.title_asc'),
        ];

        $dataSearch = [
            'keyword'    => $keyword,
            'sort_order' => $sort_order,
            'arrSort'    => $arrSort,
        ];
        $dataTmp = AdminNews::getNewsListAdmin($dataSearch);

        if (gc_check_multi_shop_installed() && session('adminStoreId') == GC_ID_ROOT) {
            $arrId = $dataTmp->pluck('id')->toArray();
            // Only show store info if store is root
            $dataStores =  gc_get_list_store_of_news($arrId);
        }

        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataMap = [
                'title' => $row['title'],
                'image' => gc_image_render($row['image'], '50px', null, $row['title']),
                'sort' => $row['sort'],
                'status' => $row['status'] ? '<span class="badge badge-light-success">ON</span>' : '<span class="badge badge-light-danger">OFF</span>',
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
            $dataMap['action'] = '<a href="' . gc_route_admin('admin_news.edit', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '"><span title="' . gc_language_render('action.edit') . '" type="button" class="btn btn-sm btn-light btn-active-primary"><i class="fa fa-edit"></i></span></a>&nbsp;
            <span onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . gc_language_render('action.delete') . '" class="btn btn-sm btn-light btn-active-danger"><i class="fas fa-trash-alt"></i></span>&nbsp;
            <a target=_new href="' . gc_route('news.detail', ['alias' => $row['alias']]) . '"><span title="Link" type="button" class="btn btn-sm btn-light btn-active-warning"><i class="fas fa-external-link-alt"></i></a>
            ';
            $dataTr[$row['id']] = $dataMap;
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin.'component.pagination');
        $data['resultItems'] = gc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' =>  $dataTmp->total()]);


        //menuRight
        $data['menuRight'][] = '<a href="' . gc_route_admin('admin_news.create') . '" class="btn btn-sm btn-light btn-active-primary" title="New" id="button_create_new">
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
                <form action="' . gc_route_admin('admin_news.index') . '" id="button_search">
                    <div class="input-group input-group" style="width: 350px;">
                        <select class="form-control rounded-0 select2" name="sort_order" id="sort_order">
                        '.$optionSort.'
                        </select> &nbsp;
                        <input type="text" name="keyword" class="form-control rounded-0 float-right" placeholder="' . gc_language_render('admin.news.search_place') . '" value="' . $keyword . '">
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
        $news = [];
        $data = [
            'title'             => gc_language_render('admin.news.add_new_title'),
            'subTitle'          => '',
            'title_description' => gc_language_render('admin.news.add_new_des'),
            'icon'              => 'fa fa-plus',
            'languages'         => $this->languages,
            'news'              => $news,
            'url_action'        => gc_route_admin('admin_news.create'),
            'customFields'      => (new ShopCustomField)->getCustomField($type = 'shop_news'),
        ];

        return view($this->templatePathAdmin.'screen.news')
            ->with($data);
    }

    /**
     * Post create new item in admin
     * @return [type] [description]
     */
    public function postCreate()
    {
        $data = request()->all();

        $langFirst = array_key_first(gc_language_all()->toArray()); //get first code language active
        $data['alias'] = !empty($data['alias'])?$data['alias']:$data['descriptions'][$langFirst]['title'];
        $data['alias'] = gc_word_format_url($data['alias']);
        $data['alias'] = gc_word_limit($data['alias'], 100);
        $arrValidation = [
            'alias' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|string|max:100',
            'descriptions.*.title' => 'required|string|max:200',
            'descriptions.*.keyword' => 'nullable|string|max:200',
            'descriptions.*.description' => 'nullable|string|max:500',
        ];
        //Custom fields
        $customFields = (new ShopCustomField)->getCustomField($type = 'shop_news');
        if ($customFields) {
            foreach ($customFields as $field) {
                if ($field->required) {
                    $arrValidation['fields.'.$field->code] = 'required';
                }
            }
        }
        $validator = Validator::make(
            $data,$arrValidation,
            [
                'alias.regex' => gc_language_render('admin.news.alias_validate'),
                'descriptions.*.title.required' => gc_language_render('validation.required', ['attribute' => gc_language_render('admin.news.title')]),
            ]
        );
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($data);
        }

        $dataCreate = [
            'image'    => $data['image'],
            'sort'     => (int)$data['sort'],
            'alias'    => $data['alias'],
            'status'   => !empty($data['status']) ? 1 : 0,
        ];
        $dataCreate = gc_clean($dataCreate, [], true);
        $news = AdminNews::createNewsAdmin($dataCreate);
        $id = $news->id;
        $dataDes = [];
        $languages = $this->languages;
        foreach ($languages as $code => $value) {
            $dataDes[] = [
                'news_id'     => $id,
                'lang'        => $code,
                'title'       => $data['descriptions'][$code]['title'],
                'keyword'     => $data['descriptions'][$code]['keyword'],
                'description' => $data['descriptions'][$code]['description'],
                'content'     => $data['descriptions'][$code]['content'],
            ];
        }
        $dataDes = gc_clean($dataDes, ['content'], true);
        AdminNews::insertDescriptionAdmin($dataDes);

        $shopStore        = $data['shop_store'] ?? [session('adminStoreId')];
        $news->stores()->detach();
        if ($shopStore) {
            $news->stores()->attach($shopStore);
        }

        //Insert custom fields
        $fields = $data['fields'] ?? [];
        gc_update_custom_field($fields, $news->id, 'shop_news');

        gc_clear_cache('cache_news');

        return redirect()->route('admin_news.index')->with('success', gc_language_render('action.create_success'));
    }

    /**
     * Form edit
     */
    public function edit($id)
    {
        $news = AdminNews::getNewsAdmin($id);
        if (!$news) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }
        $data = [
            'title'             => gc_language_render('admin.news.edit'),
            'subTitle'          => '',
            'title_description' => '',
            'icon'              => 'fa fa-edit',
            'languages'         => $this->languages,
            'news'              => $news,
            'url_action'        => gc_route_admin('admin_news.edit', ['id' => $news['id']]),
            'customFields'      => (new ShopCustomField)->getCustomField($type = 'shop_news'),
        ];
        return view($this->templatePathAdmin.'screen.news')
            ->with($data);
    }

    /**
     * update status
     */
    public function postEdit($id)
    {
        $news = AdminNews::getNewsAdmin($id);
        if (!$news) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }
        $data = request()->all();

        $langFirst = array_key_first(gc_language_all()->toArray()); //get first code language active
        $data['alias'] = !empty($data['alias'])?$data['alias']:$data['descriptions'][$langFirst]['title'];
        $data['alias'] = gc_word_format_url($data['alias']);
        $data['alias'] = gc_word_limit($data['alias'], 100);
        $arrValidation = [
            'descriptions.*.title' => 'required|string|max:200',
            'descriptions.*.keyword' => 'nullable|string|max:200',
            'descriptions.*.description' => 'nullable|string|max:500',
            'alias' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|string|max:100',
        ];
        //Custom fields
        $customFields = (new ShopCustomField)->getCustomField($type = 'shop_news');
        if ($customFields) {
            foreach ($customFields as $field) {
                if ($field->required) {
                    $arrValidation['fields.'.$field->code] = 'required';
                }
            }
        }
        $validator = Validator::make(
            $data,$arrValidation,
            [
                'alias.regex' => gc_language_render('admin.news.alias_validate'),
                'descriptions.*.title.required' => gc_language_render('validation.required', ['attribute' => gc_language_render('admin.news.title')]),
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($data);
        }
        //Edit
        $dataUpdate = [
            'image' => $data['image'],
            'alias' => $data['alias'],
            'sort' => (int)$data['sort'],
            'status' => !empty($data['status']) ? 1 : 0,
        ];
        $dataUpdate = gc_clean($dataUpdate, [], true);

        $news->update($dataUpdate);
        $news->descriptions()->delete();
        $dataDes = [];
        foreach ($data['descriptions'] as $code => $row) {
            $dataDes[] = [
                'news_id' => $id,
                'lang' => $code,
                'title' => $row['title'],
                'keyword' => $row['keyword'],
                'description' => $row['description'],
                'content' => $row['content'],
            ];
        }
        $dataDes = gc_clean($dataDes, ['content'], true);
        AdminNews::insertDescriptionAdmin($dataDes);

        $shopStore        = $data['shop_store'] ?? [session('adminStoreId')];
        $news->stores()->detach();
        if ($shopStore) {
            $news->stores()->attach($shopStore);
        }
        //Insert custom fields
        $fields = $data['fields'] ?? [];
        gc_update_custom_field($fields, $news->id, 'shop_news');

        gc_clear_cache('cache_news');

        return redirect()->route('admin_news.index')->with('success', gc_language_render('action.edit_success'));
    }

    /*
    Delete list Item
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
            AdminNews::destroy($arrID);
            gc_clear_cache('cache_news');

            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    /**
     * Check permisison item
     */
    public function checkPermisisonItem($id)
    {
        return AdminNews::getNewsAdmin($id);
    }
}
