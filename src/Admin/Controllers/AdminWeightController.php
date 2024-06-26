<?php
namespace Glumbo\Gracart\Admin\Controllers;

use Glumbo\Gracart\Admin\Controllers\RootAdminController;
use Glumbo\Gracart\Front\Models\ShopWeight;
use Validator;

class AdminWeightController extends RootAdminController
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
            'title' => gc_language_render('admin.weightlist'),
            'title_action' => '<i class="fa fa-plus" aria-hidden="true"></i> ' . gc_language_render('admin.weight.add_new_title'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => gc_route_admin('admin_weight_unit.delete'),
            'removeList' => 0, // 1 - Enable function delete list item
            'buttonRefresh' => 0, // 1 - Enable button refresh
            'css' => '',
            'js' => '',
            'url_action' => gc_route_admin('admin_weight_unit.create'),
        ];

        $listTh = [
            'name' => gc_language_render('admin.weight.name'),
            'description' => gc_language_render('admin.weight.description'),
            'action' => gc_language_render('action.title'),
        ];
        $obj = new ShopWeight;
        $obj = $obj->orderBy('id', 'desc');
        $dataTmp = $obj->paginate(20);

        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataTr[$row['id']] = [
                'name' => $row['name'],
                'description' => $row['description'],
                'action' => '
                    <a href="' . gc_route_admin('admin_weight_unit.edit', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '"><span title="' . gc_language_render('action.edit') . '" type="button" class="btn btn-sm btn-light btn-active-primary"><i class="fa fa-edit"></i></span></a>&nbsp;
                  <span onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . gc_language_render('action.delete') . '" class="btn btn-sm btn-light btn-active-danger"><i class="fas fa-trash-alt"></i></span>
                  ',
            ];
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin.'component.pagination');
        $data['resultItems'] = gc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' =>  $dataTmp->total()]);

        $data['layout'] = 'index';
        return view($this->templatePathAdmin.'screen.weight')
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
            'name' => 'required|unique:"'.ShopWeight::class.'",name',
            'description' => 'required',
        ], [
            'name.required' => gc_language_render('validation.required'),
        ]);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        //Create new order
        $dataCreate = [
            'name' => $data['name'],
            'description' => $data['description'],
        ];
        $dataCreate  = gc_clean($dataCreate, [], true);
        $obj = ShopWeight::create($dataCreate);

        return redirect()->route('admin_weight_unit.index')->with('success', gc_language_render('action.create_success'));
    }

    /**
     * Form edit
     */
    public function edit($id)
    {
        $weight = ShopWeight::find($id);
        if (!$weight) {
            return 'No data';
        }
        $data = [
        'title' => gc_language_render('admin.weightlist'),
        'title_action' => '<i class="fa fa-edit" aria-hidden="true"></i> ' . gc_language_render('action.edit'),
        'subTitle' => '',
        'icon' => 'fa fa-indent',
        'urlDeleteItem' => gc_route_admin('admin_weight_unit.delete'),
        'removeList' => 0, // 1 - Enable function delete list item
        'buttonRefresh' => 0, // 1 - Enable button refresh
        'css' => '',
        'js' => '',
        'url_action' => gc_route_admin('admin_weight_unit.edit', ['id' => $weight['id']]),
        'weight' => $weight,
        'id' => $id,
    ];

        $listTh = [
        'name' => gc_language_render('admin.weight.name'),
        'description' => gc_language_render('admin.weight.description'),
        'action' => gc_language_render('action.title'),
    ];
        $obj = new ShopWeight;
        $obj = $obj->orderBy('id', 'desc');
        $dataTmp = $obj->paginate(20);

        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataTr[$row['id']] = [
            'name' => $row['name'],
            'description' => $row['description'],
            'action' => '
                <a href="' . gc_route_admin('admin_weight_unit.edit', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '"><span title="' . gc_language_render('action.edit') . '" type="button" class="btn btn-sm btn-light btn-active-primary"><i class="fa fa-edit"></i></span></a>&nbsp;
            <span onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . gc_language_render('action.delete') . '" class="btn btn-sm btn-light btn-active-danger"><i class="fas fa-trash-alt"></i></span>
            ',
        ];
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin.'component.pagination');
        $data['resultItems'] = gc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' =>  $dataTmp->total()]);

        $data['layout'] = 'edit';
        return view($this->templatePathAdmin.'screen.weight')
        ->with($data);
    }

    /**
     * update status
     */
    public function postEdit($id)
    {
        $data = request()->all();
        $dataOrigin = request()->all();
        $obj = ShopWeight::find($id);
        $validator = Validator::make($dataOrigin, [
            'name' => 'required|unique:"'.ShopWeight::class.'",name,' . $obj->id . ',id',
            'description' => 'required',
        ], [
            'name.required' => gc_language_render('validation.required'),
        ]);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        //Edit
        $dataUpdate = [
            'name' => $data['name'],
            'description' => $data['description'],
        ];
        $dataUpdate = gc_clean($dataUpdate, [], true);

        $obj->update($dataUpdate);


        return redirect()->back()->with('success', gc_language_render('admin.edit_success'));
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
            ShopWeight::destroy($arrID);
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }
}
