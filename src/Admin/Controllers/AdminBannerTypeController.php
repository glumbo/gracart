<?php
namespace Glumbo\Gracart\Admin\Controllers;

use Glumbo\Gracart\Admin\Controllers\RootAdminController;
use Glumbo\Gracart\Front\Models\ShopBannerType;
use Validator;

class AdminBannerTypeController extends RootAdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        $data = [
            'title' => gc_language_render('admin.banner_type.list'),
            'title_action' => '<i class="fa fa-plus" aria-hidden="true"></i> ' . gc_language_render('admin.banner_type.add_new_title'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => gc_route_admin('admin_banner_type.delete'),
            'removeList' => 0, // 1 - Enable function delete list item
            'buttonRefresh' => 0, // 1 - Enable button refresh
            'css' => '',
            'js' => '',
            'url_action' => gc_route_admin('admin_banner_type.create'),
        ];

        $listTh = [
            'code' => gc_language_render('admin.banner_type.code'),
            'name' => gc_language_render('admin.banner_type.name'),
            'action' => gc_language_render('action.title'),
        ];
        $obj = new ShopBannerType;
        $obj = $obj->orderBy('id', 'desc');
        $dataTmp = $obj->paginate(20);

        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataTr[$row['id']] = [
                'code' => $row['code'] ?? 'N/A',
                'name' => $row['name'] ?? 'N/A',
                'action' => '
                    <a href="' . gc_route_admin('admin_banner_type.edit', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '"><span title="' . gc_language_render('action.edit') . '" type="button" class="btn btn-sm btn-light btn-active-primary"><i class="fa fa-edit"></i></span></a>&nbsp;

                  <span onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . gc_language_render('action.delete') . '" class="btn btn-sm btn-light btn-active-danger"><i class="fas fa-trash-alt"></i></span>
                  ',
            ];
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin.'component.pagination');
        $data['resultItems'] = gc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' =>  $dataTmp->total()]);

        $data['layout'] = 'index';
        return view($this->templatePathAdmin.'screen.banner_type')
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
        $validator = Validator::make($dataOrigin, [
            'name' => 'required',
            'code' => 'required|unique:"'.ShopBannerType::class.'",code',
        ], [
            'name.required' => gc_language_render('validation.required'),
        ]);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $data['code'] = gc_word_format_url($data['code']);
        $data['code'] = gc_word_limit($data['code'], 100);
        $dataCreate = [
            'code' => $data['code'],
            'name' => $data['name'],
        ];
        $dataCreate = gc_clean($dataCreate, [], true);
        ShopBannerType::create($dataCreate);

        return redirect()->route('admin_banner_type.index')->with('success', gc_language_render('action.create_success'));
    }

    /**
     * Form edit
     */
    public function edit($id)
    {
        $banner_type = ShopBannerType::find($id);
        if (!$banner_type) {
            return 'No data';
        }
        $data = [
        'title' => gc_language_render('admin.banner_type.list'),
        'title_action' => '<i class="fa fa-edit" aria-hidden="true"></i> ' . gc_language_render('action.edit'),
        'subTitle' => '',
        'icon' => 'fa fa-indent',
        'urlDeleteItem' => gc_route_admin('admin_banner_type.delete'),
        'removeList' => 0, // 1 - Enable function delete list item
        'buttonRefresh' => 0, // 1 - Enable button refresh
        'buttonSort' => 0, // 1 - Enable button sort
        'css' => '',
        'js' => '',
        'url_action' => gc_route_admin('admin_banner_type.edit', ['id' => $banner_type['id']]),
        'banner_type' => $banner_type,
        'id' => $id,
    ];

        $listTh = [
        'code' => gc_language_render('admin.banner_type.code'),
        'name' => gc_language_render('admin.banner_type.name'),
        'action' => gc_language_render('action.title'),
    ];
        $obj = new ShopBannerType;
        $obj = $obj->orderBy('id', 'desc');
        $dataTmp = $obj->paginate(20);

        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataTr[$row['id']] = [
            'code' => $row['code'] ?? 'N/A',
            'name' => $row['name'] ?? 'N/A',
            'action' => '
                <a href="' . gc_route_admin('admin_banner_type.edit', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '"><span title="' . gc_language_render('action.edit') . '" type="button" class="btn btn-sm btn-light btn-active-primary"><i class="fa fa-edit"></i></span></a>&nbsp;

              <span onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . gc_language_render('action.delete') . '" class="btn btn-sm btn-light btn-active-danger"><i class="fas fa-trash-alt"></i></span>
              ',
        ];
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin.'component.pagination');
        $data['resultItems'] = gc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' =>  $dataTmp->total()]);

        $data['layout'] = 'edit';
        return view($this->templatePathAdmin.'screen.banner_type')
        ->with($data);
    }

    /**
     * update status
     */
    public function postEdit($id)
    {
        $data = request()->all();
        $dataOrigin = request()->all();
        $obj = ShopBannerType::find($id);
        $validator = Validator::make($dataOrigin, [
            'code' => 'required|unique:"'.ShopBannerType::class.'",code,' . $obj->id . ',id',
            'name' => 'required',
        ], [
            'name.required' => gc_language_render('validation.required'),
        ]);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        //Edit
        $data['code'] = gc_word_format_url($data['code']);
        $data['code'] = gc_word_limit($data['code'], 100);
        $dataUpdate = [
            'code' => $data['code'],
            'name' => $data['name'],
        ];
        $dataUpdate = gc_clean($dataUpdate, [], true);
        $obj->update($dataUpdate);

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
            ShopBannerType::destroy($arrID);
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }
}
