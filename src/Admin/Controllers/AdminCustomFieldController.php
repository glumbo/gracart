<?php
namespace Glumbo\Gracart\Admin\Controllers;

use Glumbo\Gracart\Admin\Controllers\RootAdminController;
use Glumbo\Gracart\Front\Models\ShopCustomField;
use Validator;

class AdminCustomFieldController extends RootAdminController
{

    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $data = [
            'title' => gc_language_render('admin.custom_field.list'),
            'title_action' => '<i class="fa fa-plus" aria-hidden="true"></i> ' . gc_language_render('admin.custom_field.add_new_title'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => gc_route_admin('admin_custom_field.delete'),
            'removeList' => 0, // 1 - Enable function delete list item
            'buttonRefresh' => 0, // 1 - Enable button refresh
            'css' => '',
            'js' => '',
            'url_action' => gc_route_admin('admin_custom_field.create'),
        ];

        $listTh = [
            'type' => gc_language_render('admin.custom_field.type'),
            'code' => gc_language_render('admin.custom_field.code'),
            'name' => gc_language_render('admin.custom_field.name'),
            'required' => gc_language_render('admin.custom_field.required'),
            'option' => gc_language_render('admin.custom_field.option'),
            'default' => gc_language_render('admin.custom_field.default'),
            'status' => gc_language_render('admin.custom_field.status'),
            'action' => gc_language_render('action.title'),
        ];
        $obj = new ShopCustomField;
        $obj = $obj->orderBy('id', 'desc');
        $dataTmp = $obj->paginate(20);

        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataTr[$row['id']] = [
                'type' => $this->fieldTypes()[$row['type']] ?? $row['type'],
                'code' => $row['code'],
                'name' => $row['name'],
                'required' => $row['required'],
                'option' => $row['option'],
                'default' => $row['default'],
                'status' => $row['status'] ? '<span class="badge badge-light-success">ON</span>' : '<span class="badge badge-light-danger">OFF</span>',
                'action' => '
                    <a href="' . gc_route_admin('admin_custom_field.edit', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '"><span title="' . gc_language_render('action.edit') . '" type="button" class="btn btn-sm btn-light btn-active-primary"><i class="fa fa-edit"></i></span></a>&nbsp;
    
                  <span onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . gc_language_render('action.delete') . '" class="btn btn-sm btn-light btn-active-danger"><i class="fas fa-trash-alt"></i></span>
                  ',
            ];
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin.'component.pagination');
        $data['resultItems'] = gc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' =>  $dataTmp->total()]);

        $data['layout'] = 'index';
        $data['fieldTypes'] = $this->fieldTypes();
        $data['selectTypes'] = $this->selectTypes();
        return view($this->templatePathAdmin.'screen.custom_field')
            ->with($data);
    }


    /**
     * Post create new item in admin
     * @return [type] [description]
     */
    public function postCreate()
    {
        $data = request()->all();

        $validator = Validator::make($data, [
            'type' => 'required|string|max:100',
            'code' => 'required|string|max:100',
            'name' => 'required|string|max:250',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($data);
        }
        $dataCreate = [
            'type' => $data['type'],
            'name' => $data['name'],
            'code' => $data['code'],
            'option' => $data['option'],
            'default' => $data['default'],
            'required' => (!empty($data['required']) ? 1 : 0),
            'status' => (!empty($data['status']) ? 1 : 0),
        ];
        $dataCreate = gc_clean($dataCreate, ['default'], true);
        ShopCustomField::create($dataCreate);

        return redirect()->route('admin_custom_field.index')->with('success', gc_language_render('action.create_success'));
    }

    /**
     * Form edit
     */
    public function edit($id)
    {
        $customField = ShopCustomField::find($id);
        if (!$customField) {
            return 'No data';
        }
        $data = [
            'title' => gc_language_render('admin.custom_field.list'),
            'title_action' => '<i class="fa fa-edit" aria-hidden="true"></i> ' . gc_language_render('action.edit'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => gc_route_admin('admin_custom_field.delete'),
            'removeList' => 0, // 1 - Enable function delete list item
            'buttonRefresh' => 0, // 1 - Enable button refresh
            'buttonSort' => 0, // 1 - Enable button sort
            'css' => '',
            'js' => '',
            'url_action' => gc_route_admin('admin_custom_field.edit', ['id' => $customField['id']]),
            'customField' => $customField,
            'id' => $id,
        ];

        $listTh = [
            'type' => gc_language_render('admin.custom_field.type'),
            'code' => gc_language_render('admin.custom_field.code'),
            'name' => gc_language_render('admin.custom_field.name'),
            'required' => gc_language_render('admin.custom_field.required'),
            'option' => gc_language_render('admin.custom_field.option'),
            'default' => gc_language_render('admin.custom_field.default'),
            'status' => gc_language_render('admin.custom_field.status'),
            'action' => gc_language_render('action.title'),
        ];
        $obj = new ShopCustomField;
        $obj = $obj->orderBy('id', 'desc');
        $dataTmp = $obj->paginate(20);

        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataTr[$row['id']] = [
                'type' => $this->fieldTypes()[$row['type']] ?? $row['type'],
                'code' => $row['code'],
                'name' => $row['name'],
                'required' => $row['required'],
                'option' => $row['option'],
                'default' => $row['default'],
                'status' => $row['status'] ? '<span class="badge badge-light-success">ON</span>' : '<span class="badge badge-light-danger">OFF</span>',
                'action' => '
                    <a href="' . gc_route_admin('admin_custom_field.edit', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '"><span title="' . gc_language_render('action.edit') . '" type="button" class="btn btn-sm btn-light btn-active-primary"><i class="fa fa-edit"></i></span></a>&nbsp;

                <span onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . gc_language_render('action.delete') . '" class="btn btn-sm btn-light btn-active-danger"><i class="fas fa-trash-alt"></i></span>
                ',
            ];
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin.'component.pagination');
        $data['resultItems'] = gc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' =>  $dataTmp->total()]);
        $data['fieldTypes'] = $this->fieldTypes();
        $data['selectTypes'] = $this->selectTypes();
        $data['layout'] = 'edit';
        return view($this->templatePathAdmin.'screen.custom_field')
            ->with($data);
    }


    /**
     * update status
     */
    public function postEdit($id)
    {
        $customField = ShopCustomField::find($id);
        $data = request()->all();
        $validator = Validator::make($data, [
            'type' => 'required|string|max:100',
            'code' => 'required|string|max:100',
            'name' => 'required|string|max:250',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($data);
        }
        //Edit

        $dataUpdate = [
            'type' => $data['type'],
            'name' => $data['name'],
            'code' => $data['code'],
            'option' => $data['option'],
            'default' => $data['default'],
            'required' => (!empty($data['required']) ? 1 : 0),
            'status' => (!empty($data['status']) ? 1 : 0),
        ];
        $dataUpdate = gc_clean($dataUpdate, ['default'], true);

        $customField->update($dataUpdate);
        
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
            ShopCustomField::destroy($arrID);
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    /**
     * List field support custom
     *
     * @return  [type]  [return description]
     */
    public function fieldTypes() {
            return     [
            'shop_customer' => gc_language_render('admin.field_name.shop_customer'),
            'shop_product'  => gc_language_render('admin.field_name.shop_product'),
            'shop_banner'   => gc_language_render('admin.field_name.shop_banner'),
            'shop_brand'    => gc_language_render('admin.field_name.shop_brand'),
            'shop_supplier' => gc_language_render('admin.field_name.shop_supplier'),
            'shop_category' => gc_language_render('admin.field_name.shop_category'),
            'shop_news'     => gc_language_render('admin.field_name.shop_news'),
            'shop_page'     => gc_language_render('admin.field_name.shop_page'),
            ];
    }

    public function selectTypes() {
        return [
            'text'     => 'Text', 
            'number'   => 'Number', 
            'date'     => 'Date', 
            'month'    => 'Month', 
            'week'     => 'Week', 
            'time'     => 'Time', 
            'color'    => 'Color', 
            'email'    => 'Email', 
            'password' => 'Password', 
            'url'      => 'Url', 
            'range'    => 'Range', 
            'textarea' => 'Textarea', 
            'select'   => 'Select', 
            'radio'    => 'Radio', 
            'checkbox' => 'Check box',
        ];
    }
}
