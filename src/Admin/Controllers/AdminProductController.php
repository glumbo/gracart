<?php
namespace Glumbo\Gracart\Admin\Controllers;

use Glumbo\Gracart\Admin\Controllers\RootAdminController;
use Glumbo\Gracart\Front\Models\ShopAttributeGroup;
use Glumbo\Gracart\Front\Models\ShopBrand;
use Glumbo\Gracart\Front\Models\ShopTax;
use Glumbo\Gracart\Front\Models\ShopLanguage;
use Glumbo\Gracart\Front\Models\ShopWeight;
use Glumbo\Gracart\Front\Models\ShopLength;
use Glumbo\Gracart\Front\Models\ShopProductAttribute;
use Glumbo\Gracart\Front\Models\ShopProductBuild;
use Glumbo\Gracart\Front\Models\ShopProductGroup;
use Glumbo\Gracart\Front\Models\ShopProductImage;
use Glumbo\Gracart\Front\Models\ShopProductDescription;
use Glumbo\Gracart\Front\Models\ShopSupplier;
use Glumbo\Gracart\Front\Models\ShopProductStore;
use Glumbo\Gracart\Front\Models\ShopProductCategory;
use Glumbo\Gracart\Front\Models\ShopProductDownload;
use Glumbo\Gracart\Front\Models\ShopProductProperty;
use Glumbo\Gracart\Front\Models\ShopCustomField;
use Glumbo\Gracart\Admin\Models\AdminProduct;
use Glumbo\Gracart\Admin\Models\AdminStore;
use Glumbo\Gracart\Admin\Models\AdminCategory;
use Illuminate\Support\Facades\Validator;
use DB;

class AdminProductController extends RootAdminController
{
    public $languages;
    public $properties;
    public $attributeGroup;
    public $listWeight;
    public $listLength;

    public function __construct()
    {
        parent::__construct();
        $this->languages       = ShopLanguage::getListActive();
        $this->listWeight      = ShopWeight::getListAll();
        $this->listLength      = ShopLength::getListAll();
        $this->attributeGroup  = ShopAttributeGroup::getListAll();
        $this->properties = (new ShopProductProperty)->pluck('name', 'code')->toArray();
    }

    public function kinds()
    {
        return [
            GC_PRODUCT_SINGLE => gc_language_render('product.kind_single'),
            GC_PRODUCT_BUILD  => gc_language_render('product.kind_bundle'),
            GC_PRODUCT_GROUP  => gc_language_render('product.kind_group'),
        ];
    }

    public function index()
    {
        $categoriesTitle = AdminCategory::getListTitleAdmin();
        $data = [
            'title'         => gc_language_render('product.admin.list'),
            'subTitle'      => '',
            'icon'          => 'fa fa-indent',
            'urlDeleteItem' => gc_route_admin('admin_product.delete'),
            'removeList'    => 1, // Enable function delete list item
            'buttonRefresh' => 1, // 1 - Enable button refresh
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
            'image'     => gc_language_render('product.image'),
            'name'     => gc_language_render('product.name'),
            'category' => gc_language_render('product.category'),
        ];
        if (gc_config_admin('product_cost')) {
            $listTh['cost'] = gc_language_render('product.cost');
        }
        if (gc_config_admin('product_price')) {
            $listTh['price'] = gc_language_render('product.price');
        }
        if (gc_config_admin('product_kind')) {
            $listTh['kind'] = gc_language_render('product.kind');
        }

        $listTh['status'] = gc_language_render('product.status');
        $listTh['approve'] = gc_language_render('product.approve');

        if ((gc_check_multi_shop_installed() && session('adminStoreId') == GC_ID_ROOT)) {
            // Only show store info if store is root
            $listTh['shop_store'] = gc_language_render('front.store_list');
        }

        $listTh['action'] = gc_language_render('action.title');

        $keyword     = gc_clean(request('keyword') ?? '');
        $category_id = gc_clean(request('category_id') ?? '');
        $sort_order  = gc_clean(request('sort_order') ?? 'id_desc');

        $arrSort = [
            'id__desc'   => gc_language_render('filter_sort.id_desc'),
            'id__asc'    => gc_language_render('filter_sort.id_asc'),
            'name__desc' => gc_language_render('filter_sort.name_desc'),
            'name__asc'  => gc_language_render('filter_sort.name_asc'),
        ];
        $dataSearch = [
            'keyword'     => $keyword,
            'category_id' => $category_id,
            'sort_order'  => $sort_order,
            'arrSort'     => $arrSort,
        ];

        $dataTmp = (new AdminProduct)->getProductListAdmin($dataSearch);
        $arrProductId = $dataTmp->pluck('id')->toArray();
        $categoriesTmp = (new AdminProduct)->getListCategoryIdFromProductId($arrProductId);

        if (gc_check_multi_shop_installed() && session('adminStoreId') == GC_ID_ROOT) {
            // Only show store info if store is root
            $tableStore = (new AdminStore)->getTable();
            $tableProductStore = (new ShopProductStore)->getTable();
            $dataStores =  ShopProductStore::select($tableStore.'.code', $tableStore.'.id', 'product_id')
                ->join($tableStore, $tableStore.'.id', $tableProductStore.'.store_id')
                ->whereIn('product_id', $arrProductId)
                ->get()
                ->groupBy('product_id');
        }

        $dataTr = [];

        foreach ($dataTmp as $key => $row) {
            $kind = $this->kinds()[$row['kind']] ?? $row['kind'];
            if ($row['kind'] == GC_PRODUCT_BUILD) {
                $kind = '<span class="badge badge-light-success">' . $kind . '</span>';
            } elseif ($row['kind'] == GC_PRODUCT_GROUP) {
                $kind = '<span class="badge badge-light-danger">' . $kind . '</span>';
            }
            $arrName = [];
            $categoriesTmpRow = $categoriesTmp[$row['id']] ?? [];
            if ($categoriesTmpRow) {
            }
            foreach ($categoriesTmpRow as $category) {
                $arrName[] = $categoriesTitle[$category->category_id] ?? '';
            }

            $dataMap = [
                'image' => gc_image_render($row->getThumb(), '50px', '50px', $row['name']),
                'name' => $row['name'].'<br><b>SKU:</b> '.$row['sku'],
                'category' => implode(';<br>', $arrName),
                
            ];
            if (gc_config_admin('product_cost')) {
                $dataMap['cost'] = $row['cost'];
            }
            if (gc_config_admin('product_price')) {
                $dataMap['price'] = $row['price'];
            }
            if (gc_config_admin('product_kind')) {
                $dataMap['kind'] = $kind;
            }

            $dataMap['status'] = $row['status'] ? '<span class="badge badge-light-success">ON</span>' : '<span class="badge badge-light-danger">OFF</span>';
            $dataMap['approve'] = $row['approve'] ? '<span class="badge badge-light-success">ON</span>' : '<span class="badge badge-light-danger">OFF</span>';

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
            $htmlAction = '
            <a href="' . gc_route_admin('admin_product.edit', ['id' => $row['id'] ? $row['id'] : 'not-found-id']) . '">
            <span title="' . gc_language_render('product.admin.edit') . '" type="button" class="btn btn-sm btn-light btn-active-primary">
            <i class="fa fa-edit"></i>
            </span>
            </a>';
            if ($row['kind'] == GC_PRODUCT_SINGLE) {
                $htmlAction .= '
                <span onclick="cloneProduct(\'' . $row['id'] . '\');" title="' . gc_language_render('product.admin.clone') . '" type="button" class="btn btn-sm btn-light btn-active-info">
                <i class="fa fa-clipboard"></i>
                </span>';
            }
            $htmlAction .='<span onclick="deleteItem(\'' . $row['id'] . '\');"  title="' . gc_language_render('action.delete') . '" class="btn btn-sm btn-light btn-active-danger">
            <i class="fas fa-trash-alt"></i>
            </span>
            <a target=_new href="' . gc_route('product.detail', ['alias' => $row['alias']]) . '"><span title="Link" type="button" class="btn btn-sm btn-light btn-active-warning"><i class="fas fa-external-link-alt"></i></a>';

            $dataMap['action'] = $htmlAction;
            $dataTr[$row['id']] = $dataMap;
        }

        $data['listTh'] = $listTh;
        $data['dataTr'] = $dataTr;
        $data['pagination'] = $dataTmp->appends(request()->except(['_token', '_pjax']))->links($this->templatePathAdmin.'component.pagination');
        $data['resultItems'] = gc_language_render('admin.result_item', ['item_from' => $dataTmp->firstItem(), 'item_to' => $dataTmp->lastItem(), 'total' =>  $dataTmp->total()]);

        //menuRight
        $data['menuRight'][] = '<a href="' . gc_route_admin('admin_product.create') . '" class="btn btn-icon btn-bg-light btn-active-color-primary me-1" title="'.gc_language_render('product.admin.add_new_title').'" id="button_create_new">
        <i class="la la-plus"></i>
        </a>';
        if (gc_config_admin('product_kind')) {
            $data['menuRight'][] = '<a href="' . gc_route_admin('admin_product.build_create') . '" class="btn btn-icon btn-bg-light btn-active-color-primary me-1" title="'.gc_language_render('product.admin.add_new_title_build').'" id="button_create_new">
            <i class="fas fa-puzzle-piece"></i>
            </a>';
            $data['menuRight'][] = '<a href="' . gc_route_admin('admin_product.group_create') . '" class="btn btn-icon btn-bg-light btn-active-color-primary me-1" title="'.gc_language_render('product.admin.add_new_title_group').'" id="button_create_new">
            <i class="fas fa-network-wired"></i>
            </a>';
        }
        //=menuRight

        //menuSort
        $optionSort = '';
        foreach ($arrSort as $key => $sort) {
            $optionSort .= '<option  ' . (($sort_order == $key) ? "selected" : "") . ' value="' . $key . '">' . $sort . '</option>';
        }
        //=menuSort

        //Search with category
        $optionCategory = '';
        $categories = (new AdminCategory)->getTreeCategoriesAdmin();
        if ($categories) {
            foreach ($categories as $k => $v) {
                $optionCategory .= "<option value='{$k}' ".(($category_id == $k) ? 'selected' : '').">{$v}</option>";
            }
        }

        //topMenuRight
        $data['topMenuRight'][] ='
                <form action="' . gc_route_admin('admin_product.index') . '" id="button_search">
                <div class="input-group input-group float-start">
                    <select class="form-control rounded-0 select2" name="sort_order" id="sort_order">
                    '.$optionSort.'
                    </select> &nbsp;

                    <select class="form-control rounded-0 select2" name="category_id" id="category_id">
                    <option value="">'.gc_language_render('product.admin.select_category').'</option>
                    '.$optionCategory.'
                    </select> &nbsp;
                    <input type="text" name="keyword" class="form-control rounded-0 float-end" placeholder="' . gc_language_render('product.admin.search_place') . '" value="' . $keyword . '">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                </form>';
        //=topMenuRight

        return view($this->templatePathAdmin.'screen.list')
            ->with($data);
    }

    /**
     * Form create new item in admin
     * @return [type] [description]
     */
    public function create()
    {
        $categories = (new AdminCategory)->getTreeCategoriesAdmin();
        // html add more images
        $htmlMoreImage = '<div class="input-group"><input type="text" id="id_sub_image" name="sub_image[]" value="image_value" class="form-control form-control-solid rounded-0 input-sm sub_image" placeholder=""  /><span class="input-group-btn"><a data-input="id_sub_image" data-preview="preview_sub_image" data-type="product" class="btn btn-primary lfm"><i class="fa fa-picture-o"></i> Choose</a></span></div><div id="preview_sub_image" class="img_holder"></div>';
        //end add more images

        // html select attribute
        $htmlProductAtrribute = '<tr><td><br><input type="text" name="attribute[attribute_group][name][]" value="attribute_value" class="form-control form-control-solid rounded-0 input-sm" placeholder="' . gc_language_render('product.admin.add_attribute_place') . '" /></td><td><br><input type="number" step="0.01" name="attribute[attribute_group][add_price][]" value="add_price_value" class="form-control form-control-solid rounded-0 input-sm" placeholder="' . gc_language_render('product.admin.add_price_place') . '"></td><td><br><span title="Remove" class="btn btn-danger btn-active-color-light ms-3 me-3 border-2 border removeAttribute"><i class="fa fa-times"></i></span></td></tr>';
        //end select attribute

        $data = [
            'title'                => gc_language_render('product.admin.add_new_title'),
            'subTitle'             => '',
            'title_description'    => gc_language_render('product.admin.add_new_des'),
            'icon'                 => 'fa fa-plus',
            'languages'            => $this->languages,
            'categories'           => $categories,
            'brands'               => (new ShopBrand)->getListAll(),
            'suppliers'            => (new ShopSupplier)->getListAll(),
            'taxs'                 => (new ShopTax)->getListAll(),
            'taxs_options'         => (new ShopTax)->getListOptions(),
            'properties'           => $this->properties,
            'kinds'                => $this->kinds(),
            'attributeGroup'       => $this->attributeGroup,
            'htmlMoreImage'        => $htmlMoreImage,
            'htmlProductAtrribute' => $htmlProductAtrribute,
            'listWeight'           => $this->listWeight,
            'listLength'           => $this->listLength,
            'customFields'         => (new ShopCustomField)->getCustomField($type = 'shop_product'),
        ];

        return view($this->templatePathAdmin.'screen.product_add')
            ->with($data);
    }

    /**
     * Form create new item in admin
     * @return [type] [description]
     */
    public function createProductBuild()
    {
        $categories = (new AdminCategory)->getTreeCategoriesAdmin();

        $listProductSingle = (new AdminProduct)->getProductSelectAdmin(['kind' => [GC_PRODUCT_SINGLE]]);

        // html select product build
        $htmlSelectBuild = '<div class="select-product">';
        $htmlSelectBuild .= '<table width="100%"><tr><td width="70%"><select class="form-select form-select-solid rounded-0 productInGroup" data-placeholder="' . gc_language_render('product.admin.select_product_in_build') . '" style="width: 100%;" name="productBuild[]" >';
        $htmlSelectBuild .= '';
        foreach ($listProductSingle as $k => $v) {
            $htmlSelectBuild .= '<option value="' . $k . '">' . $v['name'] . '</option>';
        }
        $htmlSelectBuild .= '</select></td><td style="width:100px"><input class="form-control form-control-solid ms-2 rounded-0"  type="number" name="productBuildQty[]" value="1" min=1></td><td><span title="Remove" class="btn btn-danger btn-active-color-light ms-3 me-3 border-2 border removeproductBuild"><i class="fa fa-times"></i></span></td></tr></table>';
        $htmlSelectBuild .= '</div>';
        //end select product build

        // html select attribute
        $htmlProductAtrribute = '<tr><td><br><input type="text" name="attribute[attribute_group][name][]" value="attribute_value" class="form-control rounded-0 input-sm" placeholder="' . gc_language_render('product.admin.add_attribute_place') . '" /></td><td><br><input type="number" step="0.01" name="attribute[attribute_group][add_price][]" value="add_price_value" class="form-control rounded-0 input-sm" placeholder="' . gc_language_render('product.admin.add_price_place') . '"></td><td><br><span title="Remove" class="btn btn-sm btn-light btn-active-danger removeAttribute"><i class="fa fa-times"></i></span></td></tr>';
        //end select attribute

        // html add more images
        $htmlMoreImage = '<div class="input-group"><input type="text" id="id_sub_image" name="sub_image[]" value="image_value" class="form-control rounded-0 input-sm sub_image" placeholder=""  /><span class="input-group-btn"><a data-input="id_sub_image" data-preview="preview_sub_image" data-type="product" class="btn btn-primary lfm"><i class="fa fa-picture-o"></i> Choose</a></span></div><div id="preview_sub_image" class="img_holder"></div>';
        //end add more images


        $data = [
        'title'                => gc_language_render('product.admin.add_new_title_build'),
        'subTitle'             => '',
        'title_description'    => gc_language_render('product.admin.add_new_des'),
        'icon'                 => 'fa fa-plus',
        'languages'            => $this->languages,
        'categories'           => $categories,
        'brands'               => (new ShopBrand)->getListAll(),
        'suppliers'            => (new ShopSupplier)->getListAll(),
        'taxs'                 => (new ShopTax)->getListAll(),
        'taxs_options'         => (new ShopTax)->getListOptions(),
        'properties'           => $this->properties,
        'kinds'                => $this->kinds(),
        'attributeGroup'       => $this->attributeGroup,
        'htmlSelectBuild'      => $htmlSelectBuild,
        'listProductSingle'    => $listProductSingle,
        'htmlProductAtrribute' => $htmlProductAtrribute,
        'htmlMoreImage'        => $htmlMoreImage,
        'listWeight'           => $this->listWeight,
        'listLength'           => $this->listLength,
    ];

        return view($this->templatePathAdmin.'screen.product_add_build')
        ->with($data);
    }


    /**
     * Form create new item in admin
     * @return [type] [description]
     */
    public function createProductGroup()
    {
        $categories = (new AdminCategory)->getTreeCategoriesAdmin();

        $listProductSingle = (new AdminProduct)->getProductSelectAdmin(['kind' => [GC_PRODUCT_SINGLE]]);

        // html select product group
        $htmlSelectGroup = '<div class="select-product">';
        $htmlSelectGroup .= '<table width="100%"><tr><td width="80%"><select class="form-control form-control-solid rounded-0 productInGroup select2" data-placeholder="' . gc_language_render('product.admin.select_product_in_group') . '" style="width: 100%;" name="productInGroup[]" >';
        $htmlSelectGroup .= '';
        foreach ($listProductSingle as $k => $v) {
            $htmlSelectGroup .= '<option value="' . $k . '">' . $v['name'] . '</option>';
        }
        $htmlSelectGroup .= '</select></td><td><span title="Remove" class="btn btn-danger btn-active-color-light ms-2 me-3 border-2 border removeproductInGroup"><i class="fa fa-times"></i></span></td></tr></table>';
        $htmlSelectGroup .= '</div>';
        //End select product group


        $data = [
        'title'                => gc_language_render('product.admin.add_new_title_group'),
        'subTitle'             => '',
        'title_description'    => gc_language_render('product.admin.add_new_des'),
        'icon'                 => 'fa fa-plus',
        'languages'            => $this->languages,
        'categories'           => $categories,
        'brands'               => (new ShopBrand)->getListAll(),
        'suppliers'            => (new ShopSupplier)->getListAll(),
        'taxs'                 => (new ShopTax)->getListAll(),
        'taxs_options'         => (new ShopTax)->getListOptions(),
        'properties'           => $this->properties,
        'kinds'                => $this->kinds(),
        'attributeGroup'       => $this->attributeGroup,
        'listProductSingle'    => $listProductSingle,
        'htmlSelectGroup'      => $htmlSelectGroup,
        'listWeight'           => $this->listWeight,
        'listLength'           => $this->listLength,
    ];

        return view($this->templatePathAdmin.'screen.product_add_group')
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
        $data['alias'] = !empty($data['alias'])?$data['alias']:$data['descriptions'][$langFirst]['name'];
        $data['alias'] = gc_word_format_url($data['alias']);
        $data['alias'] = gc_word_limit($data['alias'], 100);

        switch ($data['kind']) {
            case GC_PRODUCT_SINGLE: // product single
                $arrValidation = [
                    'kind'                       => 'required',
                    'sort'                       => 'numeric|min:0',
                    'minimum'                    => 'numeric|min:0',
                    'descriptions.*.name'        => 'required|string|max:100',
                    'descriptions.*.keyword'     => 'nullable|string|max:100',
                    'descriptions.*.description' => 'nullable|string|max:100',
                    'descriptions.*.content'     => 'required|string',
                    'category'                   => 'required',
                    'sku'                        => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|product_sku_unique',
                    'alias'                      => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|string|max:120|product_alias_unique',
                ];

                //Custom fields
                $customFields = (new ShopCustomField)->getCustomField($type = 'shop_product');
                if ($customFields) {
                    foreach ($customFields as $field) {
                        if ($field->required) {
                            $arrValidation['fields.'.$field->code] = 'required';
                        }
                    }
                }

                $arrValidation = $this->validateAttribute($arrValidation);
                
                $arrMsg = [
                    'descriptions.*.name.required'    => gc_language_render('validation.required', ['attribute' => gc_language_render('product.name')]),
                    'descriptions.*.content.required' => gc_language_render('validation.required', ['attribute' => gc_language_render('product.content')]),
                    'category.required'               => gc_language_render('validation.required', ['attribute' => gc_language_render('product.category')]),
                    'sku.regex'                       => gc_language_render('product.sku_validate'),
                    'sku.product_sku_unique'          => gc_language_render('product.sku_unique'),
                    'alias.regex'                     => gc_language_render('product.alias_validate'),
                    'alias.product_alias_unique'      => gc_language_render('product.alias_unique'),
                ];
                break;

            case GC_PRODUCT_BUILD: //product build
                $arrValidation = [
                    'kind'                       => 'required',
                    'sort'                       => 'numeric|min:0',
                    'minimum'                    => 'numeric|min:0',
                    'descriptions.*.name'        => 'required|string|max:100',
                    'descriptions.*.keyword'     => 'nullable|string|max:100',
                    'descriptions.*.description' => 'nullable|string|max:100',
                    'category'                   => 'required',
                    'sku'                        => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|product_sku_unique',
                    'alias'                      => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|string|max:120|product_alias_unique',
                    'productBuild'               => 'required',
                    'productBuildQty'            => 'required',
                ];

                $arrValidation = $this->validateAttribute($arrValidation);

                $arrMsg = [
                    'descriptions.*.name.required' => gc_language_render('validation.required', ['attribute' => gc_language_render('product.name')]),
                    'category.required'            => gc_language_render('validation.required', ['attribute' => gc_language_render('product.category')]),
                    'sku.regex'                    => gc_language_render('product.sku_validate'),
                    'sku.product_sku_unique'       => gc_language_render('product.sku_unique'),
                    'alias.regex'                  => gc_language_render('product.alias_validate'),
                    'alias.product_alias_unique'   => gc_language_render('product.alias_unique'),
                ];
                break;

            case GC_PRODUCT_GROUP: //product group
                $arrValidation = [
                    'kind'                       => 'required',
                    'productInGroup'             => 'required',
                    'sku'                        => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|product_sku_unique',
                    'alias'                      => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|string|max:120|product_alias_unique',
                    'sort'                       => 'numeric|min:0',
                    'category'                   => 'required',
                    'descriptions.*.name'        => 'required|string|max:200',
                    'descriptions.*.keyword'     => 'nullable|string|max:200',
                    'descriptions.*.description' => 'nullable|string|max:500',
                ];
                $arrMsg = [
                    'descriptions.*.name.required' => gc_language_render('validation.required', ['attribute' => gc_language_render('product.name')]),
                    'sku.regex'                    => gc_language_render('product.sku_validate'),
                    'category.required'            => gc_language_render('validation.required', ['attribute' => gc_language_render('product.category')]),
                    'sku.product_sku_unique'       => gc_language_render('product.sku_unique'),
                    'alias.regex'                  => gc_language_render('product.alias_validate'),
                    'alias.product_alias_unique'   => gc_language_render('product.alias_unique'),
                ];
                break;

            default:
                $arrValidation = [
                    'kind' => 'required',
                ];
                break;
        }

        $validator = Validator::make($data, $arrValidation, $arrMsg ?? []);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($data);
        }

        $category        = $data['category'] ?? [];
        $attribute       = $data['attribute'] ?? [];
        $descriptions    = $data['descriptions'];
        $productInGroup  = $data['productInGroup'] ?? [];
        $productBuild    = $data['productBuild'] ?? [];
        $productBuildQty = $data['productBuildQty'] ?? [];
        $subImages       = $data['sub_image'] ?? [];
        $downloadPath    = $data['download_path'] ?? '';
        $dataCreate = [
            'brand_id'       => $data['brand_id'] ?? "",
            'supplier_id'    => $data['supplier_id'] ?? "",
            'price'          => $data['price'] ?? 0,
            'sku'            => $data['sku'],
            'cost'           => $data['cost'] ?? 0,
            'stock'          => $data['stock'] ?? 0,
            'weight_class'   => $data['weight_class'] ?? '',
            'length_class'   => $data['length_class'] ?? '',
            'weight'         => $data['weight'] ?? 0,
            'height'         => $data['height'] ?? 0,
            'length'         => $data['length'] ?? 0,
            'width'          => $data['width'] ?? 0,
            'kind'           => $data['kind'] ?? GC_PRODUCT_SINGLE,
            'alias'          => $data['alias'],
            'property'       => $data['property'] ?? GC_PROPERTY_PHYSICAL,
            'image'          => $data['image'] ?? '',
            'tax_id'         => $data['tax_id'] ?? "",
            'status'         => (!empty($data['status']) ? 1 : 0),
            'approve'         => (!empty($data['approve']) ? 1 : 0),
            'sort'           => (int) $data['sort'],
            'minimum'        => (int) ($data['minimum'] ?? 0),
        ];

        if (!empty($data['date_available'])) {
            $dataCreate['date_available'] = $data['date_available'];
        }
        //insert product
        $dataCreate = gc_clean($dataCreate, [], true);
        $product = AdminProduct::createProductAdmin($dataCreate);

        //Promoton price
        if (isset($data['price_promotion']) && in_array($data['kind'], [GC_PRODUCT_SINGLE, GC_PRODUCT_BUILD])) {
            $arrPromotion['price_promotion'] = $data['price_promotion'];
            $arrPromotion['date_start'] = $data['price_promotion_start'] ? $data['price_promotion_start'] : null;
            $arrPromotion['date_end'] = $data['price_promotion_end'] ? $data['price_promotion_end'] : null;
            $arrPromotion = gc_clean($arrPromotion, [], true);
            $product->promotionPrice()->create($arrPromotion);
        }

        //Insert category
        if ($category) {
            $product->categories()->attach($category);
        }

        $shopStore        = $data['shop_store'] ?? [session('adminStoreId')];
        $product->stores()->detach();
        if ($shopStore) {
            $product->stores()->attach($shopStore);
        }

        //Insert group
        if ($productInGroup && $data['kind'] == GC_PRODUCT_GROUP) {
            $arrDataGroup = [];
            foreach ($productInGroup as $pID) {
                if ($pID) {
                    $arrDataGroup[$pID] = new ShopProductGroup(['product_id' => $pID]);
                }
            }
            $product->groups()->saveMany($arrDataGroup);
        }

        //Insert Build
        if ($productBuild && $data['kind'] == GC_PRODUCT_BUILD) {
            $arrDataBuild = [];
            foreach ($productBuild as $key => $pID) {
                if ($pID) {
                    $arrDataBuild[$pID] = new ShopProductBuild(['product_id' => $pID, 'quantity' => $productBuildQty[$key]]);
                }
            }
            $product->builds()->saveMany($arrDataBuild);
        }

        //Insert attribute
        if ($attribute && $data['kind'] == GC_PRODUCT_SINGLE) {
            $arrDataAtt = [];
            foreach ($attribute as $group => $rowGroup) {
                if (count($rowGroup)) {
                    foreach ($rowGroup['name'] as $key => $nameAtt) {
                        if ($nameAtt) {
                            $dataAtt = gc_clean(['name' => $nameAtt, 'add_price' => $rowGroup['add_price'][$key],  'attribute_group_id' => $group], [], true);
                            $arrDataAtt[] = new ShopProductAttribute($dataAtt);
                        }
                    }
                }
            }
            $product->attributes()->saveMany($arrDataAtt);
        }

        //Insert path download
        if (!empty($data['property']) && $data['property'] == GC_PROPERTY_DOWNLOAD && $downloadPath) {
            $dataDownload = gc_clean(['product_id' => $product->id, 'path' => $downloadPath], [], true);
            (new ShopProductDownload)->insert($dataDownload);
        }

        //Insert custom fields
        $fields = $data['fields'] ?? [];
        gc_update_custom_field($fields, $product->id, 'shop_product');

        //Insert description
        $dataDes = [];
        $languages = $this->languages;
        foreach ($languages as $code => $value) {
            $dataDes[] = gc_clean([
                'product_id'  => $product->id,
                'lang'        => $code,
                'name'        => $descriptions[$code]['name'],
                'keyword'     => $descriptions[$code]['keyword'],
                'description' => $descriptions[$code]['description'],
                'content'     => $descriptions[$code]['content'] ?? '',
            ], ['content'], true);
        }

        AdminProduct::insertDescriptionAdmin($dataDes);

        //Insert sub mages
        if ($subImages && in_array($data['kind'], [GC_PRODUCT_SINGLE, GC_PRODUCT_BUILD])) {
            $arrSubImages = [];
            foreach ($subImages as $key => $image) {
                if ($image) {
                    $arrSubImages[] = new ShopProductImage(gc_clean(['image' => $image], [], true));
                }
            }
            $product->images()->saveMany($arrSubImages);
        }

        gc_clear_cache('cache_product');

        return redirect()->route('admin_product.index')->with('success', gc_language_render('product.admin.create_success'));
    }

    /*
    * Form edit
    */
    public function edit($id)
    {
        $product = (new AdminProduct)->getProductAdmin($id);
        
        if ($product === null) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }
        
        $categories = (new AdminCategory)->getTreeCategoriesAdmin();
        
        $listProductSingle = (new AdminProduct)->getProductSelectAdmin(['kind' => [GC_PRODUCT_SINGLE]]);

        // html select product group
        $htmlSelectGroup = '<div class="select-product">';
        $htmlSelectGroup .= '<table width="100%"><tr><td width="80%"><select class="form-control form-control-solid rounded-0 productInGroup select2" data-placeholder="' . gc_language_render('product.admin.select_product_in_group') . '" style="width: 100%;" name="productInGroup[]" >';
        $htmlSelectGroup .= '';
        foreach ($listProductSingle as $k => $v) {
            $htmlSelectGroup .= '<option value="' . $k . '">' . $v['name'] . '</option>';
        }
        $htmlSelectGroup .= '</select></td><td><span title="Remove" class="btn btn-sm btn-light btn-active-danger removeproductInGroup"><i class="fa fa-times"></i></span></td></tr></table>';
        $htmlSelectGroup .= '</div>';
        //End select product group

        // html select product build
        $htmlSelectBuild = '<div class="select-product">';
        $htmlSelectBuild .= '<table width="100%"><tr><td width="70%"><select class="form-control form-control-solid rounded-0 productInGroup select2" data-placeholder="' . gc_language_render('product.admin.select_product_in_build') . '" style="width: 100%;" name="productBuild[]" >';
        $htmlSelectBuild .= '';
        foreach ($listProductSingle as $k => $v) {
            $htmlSelectBuild .= '<option value="' . $k . '">' . $v['name'] . '</option>';
        }
        $htmlSelectBuild .= '</select></td><td style="width:100px"><input class="form-control form-control-solid rounded-0"  type="number" name="productBuildQty[]" value="1" min=1></td><td><span title="Remove" class="btn btn-light btn-active-color-primary me-3 border-2 border removeproductBuild"><i class="fa fa-times"></i></span></td></tr></table>';
        $htmlSelectBuild .= '</div>';
        //end select product build

        // html select attribute
        $htmlProductAtrribute = '<tr><td><br><input type="text" name="attribute[attribute_group][name][]" value="attribute_value" class="form-control form-control-solid rounded-0 input-sm me-3" placeholder="' . gc_language_render('product.admin.add_attribute_place') . '" /></td><td><br><input type="number" step="0.01" name="attribute[attribute_group][add_price][]" value="add_price_value" class="form-control form-control-solid rounded-0 input-sm border ms-2 me-3" placeholder="' . gc_language_render('product.admin.add_price_place') . '"></td><td><br><span title="Remove" class="btn btn-danger btn-active-color-light ms-3 me-3 border-2 border removeAttribute"><i class="fa fa-times"></i></span></td></tr>';
        //end select attribute

        $data = [
            'title'                => gc_language_render('product.admin.edit'),
            'subTitle'             => '',
            'title_description'    => '',
            'icon'                 => 'fa fa-edit',
            'languages'            => $this->languages,
            'product'              => $product,
            'categories'           => $categories,
            'brands'               => (new ShopBrand)->getListAll(),
            'suppliers'            => (new ShopSupplier)->getListAll(),
            'taxs'                 => (new ShopTax)->getListAll(),
            'taxs_options'         => (new ShopTax)->getListOptions(),
            'properties'           => $this->properties,
            'kinds'                => $this->kinds(),
            'attributeGroup'       => $this->attributeGroup,
            'htmlSelectGroup'      => $htmlSelectGroup,
            'htmlSelectBuild'      => $htmlSelectBuild,
            'listProductSingle'    => $listProductSingle,
            'htmlProductAtrribute' => $htmlProductAtrribute,
            'listWeight'           => $this->listWeight,
            'listLength'           => $this->listLength,

        ];

        //Only prduct single have custom field
        if ($product->kind == GC_PRODUCT_SINGLE) {
            $data['customFields'] = (new ShopCustomField)->getCustomField($type = 'shop_product');
        } else {
            $data['customFields'] = [];
        }
        return view($this->templatePathAdmin.'screen.product_edit')
            ->with($data);
    }


    public function postEdit($id)
    {
        $product = (new AdminProduct)->getProductAdmin($id);
        if ($product === null) {
            return redirect()->route('admin.data_not_found')->with(['url' => url()->full()]);
        }
        $data = request()->all();
        $langFirst = array_key_first(gc_language_all()->toArray()); //get first code language active
        $data['alias'] = !empty($data['alias'])?$data['alias']:$data['descriptions'][$langFirst]['name'];
        $data['alias'] = gc_word_format_url($data['alias']);
        $data['alias'] = gc_word_limit($data['alias'], 100);

        switch ($product['kind']) {
            case GC_PRODUCT_SINGLE: // product single
                $arrValidation = [
                    'sort' => 'numeric|min:0',
                    'minimum' => 'numeric|min:0',
                    'descriptions.*.name' => 'required|string|max:200',
                    'descriptions.*.keyword' => 'nullable|string|max:200',
                    'descriptions.*.description' => 'nullable|string|max:500',
                    'descriptions.*.content' => 'required|string',
                    'category' => 'required',
                    'sku' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|product_sku_unique:'.$id,
                    'alias' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|string|max:120|product_alias_unique:'.$id,
                ];

                //Custom fields
                $customFields = (new ShopCustomField)->getCustomField($type = 'shop_product');
                if ($customFields) {
                    foreach ($customFields as $field) {
                        if ($field->required) {
                            $arrValidation['fields.'.$field->code] = 'required';
                        }
                    }
                }

                $arrValidation = $this->validateAttribute($arrValidation);

                $arrMsg = [
                    'descriptions.*.name.required'    => gc_language_render('validation.required', ['attribute' => gc_language_render('product.name')]),
                    'descriptions.*.content.required' => gc_language_render('validation.required', ['attribute' => gc_language_render('product.content')]),
                    'category.required'               => gc_language_render('validation.required', ['attribute' => gc_language_render('product.category')]),
                    'sku.regex'                       => gc_language_render('product.sku_validate'),
                    'sku.product_sku_unique'          => gc_language_render('product.sku_unique'),
                    'alias.regex'                     => gc_language_render('product.alias_validate'),
                    'alias.product_alias_unique'      => gc_language_render('product.alias_unique'),
                ];
                break;
            case GC_PRODUCT_BUILD: //product build
                $arrValidation = [
                    'sort' => 'numeric|min:0',
                    'minimum' => 'numeric|min:0',
                    'descriptions.*.name' => 'required|string|max:200',
                    'descriptions.*.keyword' => 'nullable|string|max:200',
                    'descriptions.*.description' => 'nullable|string|max:500',
                    'category' => 'required',
                    'sku' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|product_sku_unique:'.$id,
                    'alias' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|string|max:120|product_alias_unique:'.$id,
                    'productBuild' => 'required',
                    'productBuildQty' => 'required',
                ];

                $arrValidation = $this->validateAttribute($arrValidation);
                
                $arrMsg = [
                    'descriptions.*.name.required' => gc_language_render('validation.required', ['attribute' => gc_language_render('product.name')]),
                    'category.required'            => gc_language_render('validation.required', ['attribute' => gc_language_render('product.category')]),
                    'sku.regex'                    => gc_language_render('product.sku_validate'),
                    'sku.product_sku_unique'       => gc_language_render('product.sku_unique'),
                    'alias.regex'                  => gc_language_render('product.alias_validate'),
                    'alias.product_alias_unique'   => gc_language_render('product.alias_unique'),
                ];
                break;

            case GC_PRODUCT_GROUP: //product group
                $arrValidation = [
                    'sku' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|product_sku_unique:'.$id,
                    'alias' => 'required|regex:/(^([0-9A-Za-z\-_]+)$)/|string|max:120|product_alias_unique:'.$id,
                    'productInGroup' => 'required',
                    'category' => 'required',
                    'sort' => 'numeric|min:0',
                    'descriptions.*.name' => 'required|string|max:200',
                    'descriptions.*.keyword' => 'nullable|string|max:200',
                    'descriptions.*.description' => 'nullable|string|max:500',
                ];
                $arrMsg = [
                    'sku.regex'                    => gc_language_render('product.sku_validate'),
                    'sku.product_sku_unique'       => gc_language_render('product.sku_unique'),
                    'category.required'            => gc_language_render('validation.required', ['attribute' => gc_language_render('product.category')]),
                    'alias.regex'                  => gc_language_render('product.alias_validate'),
                    'alias.product_alias_unique'   => gc_language_render('product.alias_unique'),
                    'descriptions.*.name.required' => gc_language_render('validation.required', ['attribute' => gc_language_render('product.name')]),
                ];
                break;

            default:
                break;
        }

        $validator = Validator::make($data, $arrValidation, $arrMsg ?? []);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($data);
        }
        //Edit

        $category        = $data['category'] ?? [];
        $attribute       = $data['attribute'] ?? [];
        $productInGroup  = $data['productInGroup'] ?? [];
        $productBuild    = $data['productBuild'] ?? [];
        $productBuildQty = $data['productBuildQty'] ?? [];
        $subImages       = $data['sub_image'] ?? [];
        $downloadPath    = $data['download_path'] ?? '';
        $dataUpdate = [
            'image'        => $data['image'] ?? '',
            'tax_id'       => $data['tax_id'] ?? "",
            'brand_id'     => $data['brand_id'] ?? "",
            'supplier_id'  => $data['supplier_id'] ?? "",
            'price'        => $data['price'] ?? 0,
            'cost'         => $data['cost'] ?? 0,
            'stock'        => $data['stock'] ?? 0,
            'weight_class' => $data['weight_class'] ?? '',
            'length_class' => $data['length_class'] ?? '',
            'weight'       => $data['weight'] ?? 0,
            'height'       => $data['height'] ?? 0,
            'length'       => $data['length'] ?? 0,
            'width'        => $data['width'] ?? 0,
            'property'     => $data['property'] ?? GC_PROPERTY_PHYSICAL,
            'sku'          => $data['sku'],
            'alias'        => $data['alias'],
            'status'       => (!empty($data['status']) ? 1 : 0),
            'approve'       => (!empty($data['approve']) ? 1 : 0),
            'sort'         => (int) $data['sort'],
            'minimum'      => (int) ($data['minimum'] ?? 0)
        ];
        if (!empty($data['date_available'])) {
            $dataUpdate['date_available'] = $data['date_available'];
        }
        $dataUpdate = gc_clean($dataUpdate, [], true);
        $product->update($dataUpdate);

        $shopStore        = $data['shop_store'] ?? [session('adminStoreId')];
        $product->stores()->detach();
        if ($shopStore) {
            $product->stores()->attach($shopStore);
        }

        //Update custom field
        $fields = $data['fields'] ?? [];
        gc_update_custom_field($fields, $product->id, 'shop_product');


        //Promoton price
        $product->promotionPrice()->delete();
        if (isset($data['price_promotion']) && in_array($product['kind'], [GC_PRODUCT_SINGLE, GC_PRODUCT_BUILD])) {
            $arrPromotion['price_promotion'] = $data['price_promotion'];
            $arrPromotion['date_start'] = $data['price_promotion_start'] ? $data['price_promotion_start'] : null;
            $arrPromotion['date_end'] = $data['price_promotion_end'] ? $data['price_promotion_end'] : null;
            $arrPromotion = gc_clean($arrPromotion, [], true);
            $product->promotionPrice()->create($arrPromotion);
        }

        $product->descriptions()->delete();
        $dataDes = [];
        foreach ($data['descriptions'] as $code => $row) {
            $dataDes[] = gc_clean([
                'product_id' => $id,
                'lang' => $code,
                'name' => $row['name'],
                'keyword' => $row['keyword'],
                'description' => $row['description'],
                'content' => $row['content'] ?? '',
            ], ['content'], true);
        }
        AdminProduct::insertDescriptionAdmin($dataDes);

        $product->categories()->detach();
        if (count($category)) {
            $product->categories()->attach($category);
        }

        //Update group
        if ($product['kind'] == GC_PRODUCT_GROUP) {
            $product->groups()->delete();
            if (count($productInGroup)) {
                $arrDataGroup = [];
                foreach ($productInGroup as $pID) {
                    if ($pID) {
                        $arrDataGroup[$pID] = new ShopProductGroup(['product_id' => $pID]);
                    }
                }
                $product->groups()->saveMany($arrDataGroup);
            }
        }

        //Update Build
        if ($product['kind'] == GC_PRODUCT_BUILD) {
            $product->builds()->delete();
            if (count($productBuild)) {
                $arrDataBuild = [];
                foreach ($productBuild as $key => $pID) {
                    if ($pID) {
                        $arrDataBuild[$pID] = new ShopProductBuild(['product_id' => $pID, 'quantity' => $productBuildQty[$key]]);
                    }
                }
                $product->builds()->saveMany($arrDataBuild);
            }
        }

        //Update path download
        (new ShopProductDownload)->where('product_id', $product->id)->delete();
        if ($product['property'] == GC_PROPERTY_DOWNLOAD && $downloadPath) {
            $dataDownload = gc_clean(['product_id' => $product->id, 'path' => $downloadPath], [], true);
            (new ShopProductDownload)->insert($dataDownload);
        }


        //Update attribute
        if ($product['kind'] == GC_PRODUCT_SINGLE) {
            $product->attributes()->delete();
            if (count($attribute)) {
                $arrDataAtt = [];
                foreach ($attribute as $group => $rowGroup) {
                    if (count($rowGroup)) {
                        foreach ($rowGroup['name'] as $key => $nameAtt) {
                            if ($nameAtt) {
                                $dataAtt = gc_clean(['name' => $nameAtt, 'add_price' => $rowGroup['add_price'][$key], 'attribute_group_id' => $group], [], true);
                                $arrDataAtt[] = new ShopProductAttribute($dataAtt);
                            }
                        }
                    }
                }
                $product->attributes()->saveMany($arrDataAtt);
            }
        }

        //Update sub mages
        if (in_array($product['kind'], [GC_PRODUCT_SINGLE, GC_PRODUCT_BUILD])) {
            $product->images()->delete();
            if ($subImages) {
                $arrSubImages = [];
                foreach ($subImages as $key => $image) {
                    if ($image) {
                        $arrSubImages[] = new ShopProductImage(gc_clean(['image' => $image], [], true));
                    }
                }
                $product->images()->saveMany($arrSubImages);
            }
        }


        gc_clear_cache('cache_product');

        return redirect()->route('admin_product.index')->with('success', gc_language_render('product.admin.edit_success'));
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
            $arrCantDelete = [];
            $arrDontPermission = [];
            $arrDelete = [];
            foreach ($arrID as $key => $id) {
                if (!$this->checkPermisisonItem($id)) {
                    $arrDontPermission[] = $id;
                } elseif (ShopProductBuild::where('product_id', $id)->first() || ShopProductGroup::where('product_id', $id)->first()) {
                    $arrCantDelete[] = $id;
                } else {
                    $arrDelete[] = $id;
                }
            }
            if ($arrDelete) {
                AdminProduct::destroy($arrDelete);
                gc_clear_cache('cache_product');
            }

            if (count($arrDontPermission)) {
                return response()->json(['error' => 1, 'msg' => gc_language_render('admin.remove_dont_permisison') . ': ' . json_encode($arrDontPermission)]);
            } elseif (count($arrCantDelete)) {
                return response()->json(['error' => 1, 'msg' => gc_language_render('product.admin.cant_remove_child') . ': ' . json_encode($arrCantDelete)]);
            } else {
                return response()->json(['error' => 0, 'msg' => '']);
            }
        }
    }

    /**
     * Validate attribute product
     */
    public function validateAttribute(array $arrValidation)
    {
        if (gc_config_admin('product_brand')) {
            if (gc_config_admin('product_brand_required')) {
                $arrValidation['brand_id'] = 'required';
            } else {
                $arrValidation['brand_id'] = 'nullable';
            }
        }

        if (gc_config_admin('product_supplier')) {
            if (gc_config_admin('product_supplier_required')) {
                $arrValidation['supplier_id'] = 'required';
            } else {
                $arrValidation['supplier_id'] = 'nullable';
            }
        }

        if (gc_config_admin('product_price')) {
            if (gc_config_admin('product_price_required')) {
                $arrValidation['price'] = 'required|numeric|min:0';
            } else {
                $arrValidation['price'] = 'nullable|numeric|min:0';
            }
        }

        if (gc_config_admin('product_cost')) {
            if (gc_config_admin('product_cost_required')) {
                $arrValidation['cost'] = 'required|numeric|min:0';
            } else {
                $arrValidation['cost'] = 'nullable|numeric|min:0';
            }
        }

        if (gc_config_admin('product_promotion')) {
            if (gc_config_admin('product_promotion_required')) {
                $arrValidation['price_promotion'] = 'required|numeric|min:0';
            } else {
                $arrValidation['price_promotion'] = 'nullable|numeric|min:0';
            }
        }

        if (gc_config_admin('product_stock')) {
            if (gc_config_admin('product_stock_required')) {
                $arrValidation['stock'] = 'required|numeric';
            } else {
                $arrValidation['stock'] = 'nullable|numeric';
            }
        }

        if (gc_config_admin('product_property')) {
            if (gc_config_admin('product_property_required')) {
                $arrValidation['property'] = 'required|string';
            } else {
                $arrValidation['property'] = 'nullable|string';
            }
        }

        if (gc_config_admin('product_available')) {
            if (gc_config_admin('product_available_required')) {
                $arrValidation['date_available'] = 'required|date';
            } else {
                $arrValidation['date_available'] = 'nullable|date';
            }
        }

        if (gc_config_admin('product_weight')) {
            if (gc_config_admin('product_weight_required')) {
                $arrValidation['weight'] = 'required|numeric';
                $arrValidation['weight_class'] = 'required|string';
            } else {
                $arrValidation['weight'] = 'nullable|numeric';
                $arrValidation['weight_class'] = 'nullable|string';
            }
        }

        if (gc_config_admin('product_length')) {
            if (gc_config_admin('product_length_required')) {
                $arrValidation['length_class'] = 'required|string';
                $arrValidation['length'] = 'required|numeric|min:0';
                $arrValidation['width'] = 'required|numeric|min:0';
                $arrValidation['height'] = 'required|numeric|min:0';
            } else {
                $arrValidation['length_class'] = 'nullable|string';
                $arrValidation['length'] = 'nullable|numeric|min:0';
                $arrValidation['width'] = 'nullable|numeric|min:0';
                $arrValidation['height'] = 'nullable|numeric|min:0';
            }
        }
        return $arrValidation;
    }

    /**
     * Check permisison item
     */
    public function checkPermisisonItem($id)
    {
        return (new AdminProduct)->getProductAdmin($id);
    }

    /**
     * Clone product
     * Only clone single product
     * @return  [type]  [return description]
     */
    public function cloneProduct() {

        if (!request()->ajax()) {
            return response()->json(['error' => 1, 'msg' => gc_language_render('admin.method_not_allow')]);
        }
        $pId = request('pId');
        $product = AdminProduct::find($pId);
        if (!$product) {
            return response()->json(['error' => 1, 'msg' => 'Product not found']);
       }
        if ($product->kind != GC_PRODUCT_SINGLE) {
            return response()->json(['error' => 1, 'msg' => 'Only clone product single']);
        }
        try {
            DB::connection(GC_CONNECTION)->beginTransaction();
            //Product info
            $dataProduct = \Illuminate\Support\Arr::except($product->toArray(), ['id', 'created_at', 'updated_at']);
            $dataProduct['sku'] = $dataProduct['sku'].'-'.time();
            $dataProduct['alias'] = $dataProduct['alias'].'-'.time();
            $newProduct = AdminProduct::create($dataProduct);

            //Product description
            $productDescription = $product->descriptions->toArray();
            $newDescription = [];
            foreach ($productDescription as $key => $row) {
                $row['product_id'] = $newProduct->id;
                $newDescription[] = $row;
            }
            ShopProductDescription::insert($newDescription);

            //Product category
            $productCategory = (new ShopProductCategory)->where('product_id', $product->id)->get()->toArray();
            $newCategory = [];
            foreach ($productCategory as $key => $row) {
                $row['product_id'] = $newProduct->id;
                $newCategory[] = $row;
            }
            ShopProductCategory::insert($newCategory);


            //Product store
            $productStore = (new ShopProductStore)->where('product_id', $product->id)->get()->toArray();
            $newStore = [];
            foreach ($productStore as $key => $row) {
                $row['product_id'] = $newProduct->id;
                $newStore[] = $row;
            }
            ShopProductStore::insert($newStore);

            DB::connection(GC_CONNECTION)->commit();
            return response()->json(['error' => 0, 'msg' => gc_language_render('product.admin.clone_success')]);
        } catch (\Throwable $e) {
            DB::connection(GC_CONNECTION)->rollBack();
            return response()->json(['error' => 1, 'msg' => $e->getMessage()]);
        }
       
    }
}
