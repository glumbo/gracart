<?php
namespace Glumbo\Gracart\Admin\Controllers;

use Glumbo\Gracart\Admin\Controllers\RootAdminController;
use Glumbo\Gracart\Front\Models\ShopCurrency;
use Validator;

class AdminCurrencyController extends RootAdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $data = [
            'title' => gc_language_render('admin.currency.list'),
            'subTitle' => '',
            'icon' => 'fa fa-indent',
            'urlDeleteItem' => gc_route_admin('admin_currency.delete'),
            'removeList' => 0, // 1 - Enable function delete list item
            'buttonRefresh' => 0, // 1 - Enable button refresh
            'css' => '',
            'js' => '',
        ];
        //Process add content
        $data['menuRight'] = gc_config_group('menuRight', \Request::route()->getName());
        $data['menuLeft'] = gc_config_group('menuLeft', \Request::route()->getName());
        $data['topMenuRight'] = gc_config_group('topMenuRight', \Request::route()->getName());
        $data['topMenuLeft'] = gc_config_group('topMenuLeft', \Request::route()->getName());
        $data['blockBottom'] = gc_config_group('blockBottom', \Request::route()->getName());

        $listTh = [
            'name' => gc_language_render('admin.currency.name'),
            'code' => gc_language_render('admin.currency.code'),
            'symbol' => gc_language_render('admin.currency.symbol'),
            'exchange_rate' => gc_language_render('admin.currency.exchange_rate'),
            'precision' => gc_language_render('admin.currency.precision'),
            'symbol_first' => gc_language_render('admin.currency.symbol_first'),
            'thousands' => gc_language_render('admin.currency.thousands'),
            'sort' => gc_language_render('admin.currency.sort'),
            'status' => gc_language_render('admin.currency.status'),
            'action' => gc_language_render('action.title'),
        ];

        $sort_order = gc_clean(request('sort_order') ?? 'id_desc');
        $keyword    = gc_clean(request('keyword') ?? '');
        $arrSort = [
            'id__desc' => gc_language_render('filter_sort.id_desc'),
            'id__asc' => gc_language_render('filter_sort.id_asc'),
            'name__desc' => gc_language_render('filter_sort.name_desc'),
            'name__asc' => gc_language_render('filter_sort.name_asc'),
        ];
        $obj = new ShopCurrency;
        if ($keyword) {
            $obj = $obj->whereRaw('(code = "' . $keyword . '" OR name like "%' . $keyword . '%" )');
        }

        if ($sort_order && array_key_exists($sort_order, $arrSort)) {
            $field = explode('__', $sort_order)[0];
            $sort_field = explode('__', $sort_order)[1];
            $obj = $obj->orderBy($field, $sort_field);
        } else {
            $obj = $obj->orderBy('id', 'desc');
        }
        $dataTmp = $obj->paginate(20);

        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataTr[$row['id']] = [
                'name' => $row['name'],
                'code' => $row['code'],
                'symbol' => $row['symbol'],
                'exchange_rate' => $row['exchange_rate'],
                'precision' => $row['precision'],
                'symbol_first' => $row['symbol_first'],
                'thousands' => $row['thousands'],
                'sort' => $row['sort'],
                'status' => $row['status'] ? '<span class="badge badge-light-success">ON</span>' : '<span class="badge badge-light-danger">OFF</span>',
                'action' => '
                    <a href="' . gc_route_admin('admin_currency.edit', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '"><span title="' . gc_language_render('action.edit') . '" type="button" class="btn btn-sm btn-light btn-active-primary"><i class="fa fa-edit"></i></span></a>&nbsp;

                  <span ' . (in_array($row['id'], GC_GUARD_CURRENCY) ? "style='display:none'" : "") . ' onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . gc_language_render('action.delete') . '" class="btn btn-sm btn-light btn-active-danger"><i class="fas fa-trash-alt"></i></span>
                  ',
            ];
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin.'component.pagination');
        $data['resultItems'] = gc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' =>  $dataTmp->total()]);

        //menuRight
        $data['menuRight'][] = '<a href="' . gc_route_admin('admin_currency.create') . '" class="btn btn-sm btn-light btn-active-primary" title="New" id="button_create_new">
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
                <form action="' . gc_route_admin('admin_currency.index') . '" id="button_search">
                <div class="input-group input-group" style="width: 350px;">
                <select class="form-control rounded-0 select2" name="sort_order" id="sort_order">
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
            'title' => gc_language_render('admin.currency.add_new_title'),
            'subTitle' => '',
            'title_description' => gc_language_render('admin.currency.add_new_des'),
            'icon' => 'fa fa-plus',
            'currency' => [],
            'url_action' => gc_route_admin('admin_currency.create'),
        ];
        return view($this->templatePathAdmin.'screen.currency')
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
        $validator = Validator::make($data, [
            'symbol' => 'required',
            'exchange_rate' => 'required|numeric|gt:0',
            'precision' => 'required',
            'symbol_first' => 'required',
            'thousands' => 'required',
            'sort' => 'numeric|min:0',
            'name' => 'required|string|max:100',
            'code' => 'required|unique:"'.ShopCurrency::class.'",code',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $dataCreate = [
            'name' => $data['name'],
            'code' => $data['code'],
            'symbol' => $data['symbol'],
            'exchange_rate' => $data['exchange_rate'],
            'precision' => $data['precision'],
            'symbol_first' => $data['symbol_first'],
            'thousands' => $data['thousands'],
            'status' => empty($data['status']) ? 0 : 1,
            'sort' => (int) $data['sort'],
        ];
        $dataCreate = gc_clean($dataCreate, [], true);
        ShopCurrency::create($dataCreate);

        return redirect()->route('admin_currency.index')->with('success', gc_language_render('action.create_success'));
    }

    /**
     * Form edit
     */
    public function edit($id)
    {
        $currency = ShopCurrency::find($id);
        if ($currency === null) {
            return 'no data';
        }
        $data = [
            'title' => gc_language_render('action.edit'),
            'subTitle' => '',
            'title_description' => '',
            'icon' => 'fa fa-edit',
            'currency' => $currency,
            'url_action' => gc_route_admin('admin_currency.edit', ['id' => $currency['id']]),
        ];
        return view($this->templatePathAdmin.'screen.currency')
            ->with($data);
    }

    /**
     * update status
     */
    public function postEdit($id)
    {
        $currency = ShopCurrency::find($id);
        $data = request()->all();
        $dataOrigin = request()->all();
        $validator = Validator::make($data, [
            'symbol' => 'required',
            'exchange_rate' => 'required|numeric|gt:0',
            'precision' => 'required',
            'symbol_first' => 'required',
            'thousands' => 'required',
            'sort' => 'numeric|min:0',
            'name' => 'required|string|max:100',
            'code' => 'required|unique:"'.ShopCurrency::class.'",code,' . $currency->id . ',id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        //Edit

        $dataUpdate = [
            'name' => $data['name'],
            'code' => $data['code'],
            'symbol' => $data['symbol'],
            'exchange_rate' => $data['exchange_rate'],
            'precision' => $data['precision'],
            'symbol_first' => $data['symbol_first'],
            'thousands' => $data['thousands'],
            'sort' => (int) $data['sort'],

        ];

        //Check status before change
        $check = ShopCurrency::where('status', 1)->where('code', '<>', $data['code'])->count();
        if ($check) {
            $dataUpdate['status'] = empty($data['status']) ? 0 : 1;
        } else {
            $dataUpdate['status'] = 1;
        }
        //End check status

        $obj = ShopCurrency::find($id);
        $dataUpdate = gc_clean($dataUpdate, [], true);
        $obj->update($dataUpdate);

        return redirect()->route('admin_currency.index')->with('success', gc_language_render('action.edit_success'));
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
            $arrID = array_diff($arrID, GC_GUARD_CURRENCY);
            ShopCurrency::destroy($arrID);
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }
}
