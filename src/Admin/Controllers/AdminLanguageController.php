<?php
namespace Glumbo\Gracart\Admin\Controllers;

use Glumbo\Gracart\Admin\Controllers\RootAdminController;
use Glumbo\Gracart\Front\Models\ShopLanguage;
use Validator;

class AdminLanguageController extends RootAdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
        $data = [
            'title' => gc_language_render('admin.language.list'),
            'title_action' => '<i class="fa fa-plus" aria-hidden="true"></i> ' . gc_language_render('admin.language.add_new_title'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => gc_route_admin('admin_language.delete'),
            'removeList' => 0, // 1 - Enable function delete list item
            'buttonRefresh' => 0, // 1 - Enable button refresh
            'css' => '',
            'js' => '',
            'url_action' => gc_route_admin('admin_language.create'),
        ];

        $listTh = [
            'name' => gc_language_render('admin.language.name'),
            'code' => gc_language_render('admin.language.code'),
            'icon' => gc_language_render('admin.language.icon'),
            'rtl' => gc_language_render('admin.language.layout_rtl'),
            'sort' => gc_language_render('admin.language.sort'),
            'status' => gc_language_render('admin.language.status'),
            'action' => gc_language_render('action.title'),
        ];

        $obj = new ShopLanguage;
        $obj = $obj->orderBy('id', 'desc');
        $dataTmp = $obj->paginate(20);

        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataTr[$row['id']] = [
                'name' => $row['name'],
                'code' => $row['code'],
                'icon' => gc_image_render($row['icon'], '30px', '30px', $row['name']),
                'rtl' => $row['rtl'],
                'sort' => $row['sort'],
                'status' => $row['status'] ? '<span class="badge badge-light-success">ON</span>' : '<span class="badge badge-light-danger">OFF</span>',
                'action' => '
                    <a href="' . gc_route_admin('admin_language.edit', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '"><span title="' . gc_language_render('action.edit') . '" type="button" class="btn btn-sm btn-light btn-active-primary"><i class="fa fa-edit"></i></span></a>&nbsp;

                  <span ' . (in_array($row['id'], GC_GUARD_LANGUAGE) ? "style='display:none'" : "") . ' onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . gc_language_render('action.delete') . '" class="btn btn-sm btn-light btn-active-danger"><i class="fas fa-trash-alt"></i></span>
                  ',
            ];
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin.'component.pagination');
        $data['resultItems'] = gc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' =>  $dataTmp->total()]);

        $data['layout'] = 'index';
        return view($this->templatePathAdmin.'screen.language')
            ->with($data);
    }

    /**
     * Post create
     * @return [type] [description]
     */
    public function postCreate()
    {
        $data = request()->all();
        $dataOrigin = request()->all();
        $validator = Validator::make($dataOrigin, [
            'icon' => 'required',
            'sort' => 'numeric|min:0',
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:10|unique:"'.ShopLanguage::class.'",code',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $dataCreate = [
            'icon' => $data['icon'],
            'name' => $data['name'],
            'code' => $data['code'],
            'rtl' => empty($data['rtl']) ? 0 : 1,
            'status' => empty($data['status']) ? 0 : 1,
            'sort' => (int) $data['sort'],
        ];
        $dataCreate = gc_clean($dataCreate, [], true);
        $obj = ShopLanguage::create($dataCreate);

        return redirect()->route('admin_language.edit', ['id' => $obj['id']])->with('success', gc_language_render('action.create_success'));
    }

    /**
     * Form edit
     */
    public function edit($id)
    {
        $language = ShopLanguage::find($id);
        if (!$language) {
            return 'No data';
        }
        $data = [
        'title' => gc_language_render('admin.language.list'),
        'title_action' => '<i class="fa fa-edit" aria-hidden="true"></i> ' . gc_language_render('action.edit'),
        'subTitle' => '',
        'icon' => 'fa fa-indent',
        'urlDeleteItem' => gc_route_admin('admin_language.delete'),
        'removeList' => 0, // 1 - Enable function delete list item
        'buttonRefresh' => 0, // 1 - Enable button refresh
        'css' => '',
        'js' => '',
        'url_action' => gc_route_admin('admin_language.edit', ['id' => $language['id']]),
        'language' => $language,
    ];

        $listTh = [
        'name' => gc_language_render('admin.language.name'),
        'code' => gc_language_render('admin.language.code'),
        'icon' => gc_language_render('admin.language.icon'),
        'rtl' => gc_language_render('admin.language.layout_rtl'),
        'sort' => gc_language_render('admin.language.sort'),
        'status' => gc_language_render('admin.language.status'),
        'action' => gc_language_render('action.title'),
    ];
        $obj = new ShopLanguage;
        $obj = $obj->orderBy('id', 'desc');
        $dataTmp = $obj->paginate(20);

        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataTr[$row['id']] = [
            'name' => $row['name'],
            'code' => $row['code'],
            'icon' => gc_image_render($row['icon'], '30px', '30px', $row['name']),
            'rtl' => $row['rtl'],
            'sort' => $row['sort'],
            'status' => $row['status'] ? '<span class="badge badge-light-success">ON</span>' : '<span class="badge badge-light-danger">OFF</span>',
            'action' => '
                <a href="' . gc_route_admin('admin_language.edit', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '"><span title="' . gc_language_render('action.edit') . '" type="button" class="btn btn-sm btn-light btn-active-primary"><i class="fa fa-edit"></i></span></a>&nbsp;

              <span ' . (in_array($row['id'], GC_GUARD_LANGUAGE) ? "style='display:none'" : "") . ' onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . gc_language_render('action.delete') . '" class="btn btn-sm btn-light btn-active-danger"><i class="fas fa-trash-alt"></i></span>
              ',
        ];
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin.'component.pagination');
        $data['resultItems'] = gc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' =>  $dataTmp->total()]);

        $data['layout'] = 'edit';
        return view($this->templatePathAdmin.'screen.language')
        ->with($data);
    }

    /**
     * update
     */
    public function postEdit($id)
    {
        $language = ShopLanguage::find($id);
        $data = request()->all();
        $dataOrigin = request()->all();
        $validator = Validator::make($dataOrigin, [
            'icon' => 'required',
            'name' => 'required',
            'sort' => 'numeric|min:0',
            'code' => 'required|string|max:10|unique:"'.ShopLanguage::class.'",code,' . $language->id . ',id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        //Edit

        $dataUpdate = [
            'icon' => $data['icon'],
            'name' => $data['name'],
            'code' => $data['code'],
            'rtl' => empty($data['rtl']) ? 0 : 1,
            'sort' => (int)$data['sort'],
        ];
        //Check status before change
        $check = ShopLanguage::where('status', 1)->where('code', '<>', $data['code'])->count();
        if ($check) {
            $dataUpdate['status'] = empty($data['status']) ? 0 : 1;
        } else {
            $dataUpdate['status'] = 1;
        }
        //End check status
        $obj = ShopLanguage::find($id);
        $dataUpdate =  gc_clean($dataUpdate, [], true);
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
            $arrID = array_diff($arrID, GC_GUARD_LANGUAGE);
            ShopLanguage::destroy($arrID);
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }
}
