<?php
namespace Glumbo\Gracart\Admin\Controllers;

use Glumbo\Gracart\Admin\Controllers\RootAdminController;
use Glumbo\Gracart\Front\Models\ShopEmailTemplate;
use Glumbo\Gracart\Admin\Models\AdminEmailTemplate;
use Validator;

class AdminEmailTemplateController extends RootAdminController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $data = [
            'title'         => gc_language_render('admin.email_template.list'),
            'subTitle'      => '',
            'icon'          => 'fa fa-indent',
            'urlDeleteItem' => gc_route_admin('admin_email_template.delete'),
            'removeList'    => 0, // 1 - Enable function delete list item
            'buttonRefresh' => 0, // 1 - Enable button refresh
            'css'           => '',
            'js'            => '',
        ];
        //Process add content
        $data['menuRight'] = gc_config_group('menuRight', \Request::route()->getName());
        $data['menuLeft'] = gc_config_group('menuLeft', \Request::route()->getName());
        $data['topMenuRight'] = gc_config_group('topMenuRight', \Request::route()->getName());
        $data['topMenuLeft'] = gc_config_group('topMenuLeft', \Request::route()->getName());
        $data['blockBottom'] = gc_config_group('blockBottom', \Request::route()->getName());

        $listTh = [
            'name' => gc_language_render('admin.email_template.name'),
            'group' => gc_language_render('admin.email_template.group'),
            'status' => gc_language_render('admin.email_template.status'),
            'action' => gc_language_render('action.title'),
        ];
        $dataSearch = [];
        $dataTmp = AdminEmailTemplate::getEmailTemplateListAdmin($dataSearch);

        $dataTr = [];
        foreach ($dataTmp as $key => $row) {
            $dataTr[$row['id']] = [
                'name' => $row['name'] ?? 'N/A',
                'group' => $row['group'] ?? 'N/A',
                'status' => $row['status'] ? '<span class="badge badge-success">ON</span>' : '<span class="badge badge-danger">OFF</span>',
                'action' => '
                    <a href="' . gc_route_admin('admin_email_template.edit', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '"><span title="' . gc_language_render('action.admin.edit') . '" type="button" class="btn btn-flat btn-sm btn-primary"><i class="fa fa-edit"></i></span></a>&nbsp;

                  <span onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . gc_language_render('action.delete') . '" class="btn btn-flat btn-sm btn-danger"><i class="fas fa-trash-alt"></i></span>
                  ',
            ];
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin.'component.pagination');
        $data['resultItems'] = gc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' =>  $dataTmp->total()]);

        //menuRight
        $data['menuRight'][] = '<a href="' . gc_route_admin('admin_email_template.create') . '" class="btn btn-sm btn-light btn-active-primary" title="New" id="button_create_new">
                           <i class="fa fa-plus" title="'.gc_language_render('action.add').'"></i>'.gc_language_render('action.add').'
                           </a>';
        //=menuRight

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
            'title' => gc_language_render('admin.email_template.add_new_title'),
            'subTitle' => '',
            'title_description' => gc_language_render('admin.email_template.add_new_des'),
            'icon' => 'fa fa-plus',
            'arrayGroup' => $this->arrayGroup(),
            'obj' => [],
            'url_action' => gc_route_admin('admin_email_template.create'),
        ];
        return view($this->templatePathAdmin.'screen.email_template')
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
            'group' => 'required',
            'text' => 'required',
        ]);

        if ($validator->fails()) {

            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $dataCreate = [
            'name'     => $data['name'],
            'group'    => $data['group'],
            'text'     => $data['text'],
            'status'   => !empty($data['status']) ? 1 : 0,
            'store_id' => session('adminStoreId'),
        ];
        $dataCreate = gc_clean($dataCreate, ['text'], true);
        AdminEmailTemplate::createEmailTemplateAdmin($dataCreate);

        return redirect()->route('admin_email_template.index')->with('success', gc_language_render('action.create_success'));
    }

    /**
     * Form edit
     */
    public function edit($id)
    {
        $emailTemplate = AdminEmailTemplate::getEmailTemplateAdmin($id);
        if (!$emailTemplate) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }
        $data = [
            'title' => gc_language_render('action.edit'),
            'subTitle' => '',
            'title_description' => '',
            'icon' => 'fa fa-edit',
            'obj' => $emailTemplate,
            'arrayGroup' => $this->arrayGroup(),
            'url_action' => gc_route_admin('admin_email_template.edit', ['id' => $emailTemplate['id']]),
        ];
        return view($this->templatePathAdmin.'screen.email_template')
            ->with($data);
    }

    /**
     * update status
     */
    public function postEdit($id)
    {
        $emailTemplate = AdminEmailTemplate::getEmailTemplateAdmin($id);
        if (!$emailTemplate) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }
        $data = request()->all();
        $dataOrigin = request()->all();
        $validator = Validator::make($dataOrigin, [
            'name' => 'required',
            'group' => 'required',
            'text' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        //Edit
        $dataUpdate = [
            'name'     => $data['name'],
            'group'    => $data['group'],
            'text'     => $data['text'],
            'status'   => !empty($data['status']) ? 1 : 0,
            'store_id' => session('adminStoreId'),
        ];
        $dataUpdate = gc_clean($dataUpdate, ['text'], true);
        $emailTemplate->update($dataUpdate);

        return redirect()->route('admin_email_template.index')->with('success', gc_language_render('action.edit_success'));
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
            ShopEmailTemplate::destroy($arrID);
            return response()->json(['error' => 0, 'msg' => '']);
        }
    }

    /**
     * Get list variables support for email template
     *
     * @return json
     */
    public function listVariable()
    {
        $key = request('key');
        $list = [];
        switch ($key) {
            case 'order_success_to_customer':
            case 'order_success_to_admin':
                $list = [
                    '$title',
                    '$orderID',
                    '$toname',
                    '$firstName',
                    '$lastName',
                    '$address',
                    '$address1',
                    '$address2',
                    '$address3',
                    '$email',
                    '$phone',
                    '$comment',
                    '$orderDetail',
                    '$subtotal',
                    '$shipping',
                    '$otherFee',
                    '$discount',
                    '$total',
                ];
                break;

            case 'forgot_password':
                $list = [
                    '$title',
                    '$reason_sednmail',
                    '$note_sendmail',
                    '$note_access_link',
                    '$reset_link',
                    '$reset_button',
                ];
                break;

            case 'customer_verify':
                $list = [
                    '$title',
                    '$reason_sednmail',
                    '$note_sendmail',
                    '$note_access_link',
                    '$url_verify',
                    '$button',
                ];
                break;

            case 'contact_to_admin':
                $list = [
                    '$title',
                    '$name',
                    '$email',
                    '$phone',
                    '$content',
                ];
                break;
            case 'welcome_customer':
                    $list = [
                        '$title',
                        '$first_name',
                        '$last_name',
                        '$email',
                        '$phone',
                        '$password',
                        '$address1',
                        '$address2',
                        '$address3',
                        '$country',
                    ];
                    break;
            default:
                # code...
                break;
        }
        return response()->json($list);
    }

    public function arrayGroup()
    {
        return  [
            'order_success_to_admin' => gc_language_render('email.admin.order_success_to_admin'),
            'order_success_to_customer' => gc_language_render('email.admin.order_success_to_cutomer'),
            'forgot_password' => gc_language_render('email.admin.forgot_password'),
            'customer_verify' => gc_language_render('email.admin.customer_verify'),
            'welcome_customer' => gc_language_render('email.admin.welcome_customer'),
            'contact_to_admin' => gc_language_render('email.admin.contact_to_admin'),
            'other' => gc_language_render('email.admin.other'),
        ];
    }

    /**
     * Check permisison item
     */
    public function checkPermisisonItem($id)
    {
        return AdminEmailTemplate::getEmailTemplateAdmin($id);
    }
}
