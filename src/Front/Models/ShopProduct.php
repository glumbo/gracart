<?php
namespace Glumbo\Gracart\Front\Models;

use Glumbo\Gracart\Front\Models\ShopAttributeGroup;
use Glumbo\Gracart\Front\Models\ShopCategory;
use Glumbo\Gracart\Front\Models\ShopProductCategory;
use Glumbo\Gracart\Front\Models\ShopProductDescription;
use Glumbo\Gracart\Front\Models\ShopProductGroup;
use Glumbo\Gracart\Front\Models\ShopProductPromotion;
use Glumbo\Gracart\Front\Models\ShopTax;
use Glumbo\Gracart\Front\Models\ShopStore;
use Glumbo\Gracart\Front\Models\ShopProductStore;
use Glumbo\Gracart\Front\Models\ShopCustomFieldDetail;
use Illuminate\Database\Eloquent\Model;


class ShopProduct extends Model
{
    use \Glumbo\Gracart\Front\Models\ModelTrait;
    use \Glumbo\Gracart\Front\Models\UuidTrait;

    public $table = GC_DB_PREFIX.'shop_product';
    protected $guarded = [];

    protected $connection = GC_CONNECTION;

    protected $gc_kind = []; // 0:single, 1:bundle, 2:group
    protected $gc_property = 'all'; // 0:physical, 1:download, 2:only view, 3: Service
    protected $gc_promotion = 0; // 1: only produc promotion,
    protected $gc_store_id = 0;
    protected $gc_array_ID = []; // array ID product
    protected $gc_category = []; // array category id
    protected $gc_category_vendor = []; // array category id
    protected $gc_brand = []; // array brand id
    protected $gc_supplier = []; // array supplier id
    protected $gc_range_price = null; // min__max
    protected static $storeCode = null;

    
    public function brand()
    {
        return $this->belongsTo(ShopBrand::class, 'brand_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo(ShopSupplier::class, 'supplier_id', 'id');
    }

    public function categories()
    {
        return $this->belongsToMany(ShopCategory::class, ShopProductCategory::class, 'product_id', 'category_id');
    }
    public function groups()
    {
        return $this->hasMany(ShopProductGroup::class, 'group_id', 'id');
    }
    public function stores()
    {
        return $this->belongsToMany(ShopStore::class, ShopProductStore::class, 'product_id', 'store_id');
    }
    public function builds()
    {
        return $this->hasMany(ShopProductBuild::class, 'build_id', 'id');
    }
    public function images()
    {
        return $this->hasMany(ShopProductImage::class, 'product_id', 'id');
    }

    public function descriptions()
    {
        return $this->hasMany(ShopProductDescription::class, 'product_id', 'id');
    }

    public function promotionPrice()
    {
        return $this->hasOne(ShopProductPromotion::class, 'product_id', 'id');
    }
    public function attributes()
    {
        return $this->hasMany(ShopProductAttribute::class, 'product_id', 'id');
    }
    public function downloadPath()
    {
        return $this->hasOne(ShopProductDownload::class, 'product_id', 'id');
    }

    //Function get text description
    public function getText()
    {
        return $this->descriptions()->where('lang', gc_get_locale())->first();
    }
    public function getName()
    {
        return $this->getText()->name?? '';
    }
    public function getDescription()
    {
        return $this->getText()->description ?? '';
    }
    public function getKeyword()
    {
        return $this->getText()->keyword?? '';
    }
    public function getContent()
    {
        return $this->getText()->content ?? '';
    }
    //End  get text description

    /*
    *Get final price
    */
    public function getFinalPrice()
    {
        $promotion = $this->processPromotionPrice();
        if ($promotion != -1) {
            return $promotion;
        } else {
            return $this->price;
        }
    }

    /*
    *Get final price with tax
    */
    public function getFinalPriceTax()
    {
        return gc_tax_price($this->getFinalPrice(), $this->getTaxValue());
    }


    /**
     * [showPrice description]
     * @return [type]           [description]
     */
    public function showPrice()
    {
        if (!gc_config('product_price', config('app.storeId'))) {
            return false;
        }
        $price = $this->price;
        $priceFinal = $this->getFinalPrice();
        // Process with tax
        return  view(
            'templates.frontend.'.gc_store('frontend_template').'.common.show_price',
            [
                'price' => $price,
                'priceFinal' => $priceFinal,
                'kind' => $this->kind,
            ]
        )->render();
    }

    /**
     * [showPriceDetail description]
     *
     *
     * @return  [type]             [return description]
     */
    public function showPriceDetail()
    {
        if (!gc_config('product_price', config('app.storeId'))) {
            return false;
        }
        $price = $this->price;
        $priceFinal = $this->getFinalPrice();
        // Process with tax
        return  view(
            'templates.'.gc_store('template').'.common.show_price_detail',
            [
                'price' => $price,
                'priceFinal' => $priceFinal,
                'kind' => $this->kind,
            ]
        )->render();
    }

    /**
     * Get product detail
     * @param  [string] $key [description]
     * @param  [string] $type id, sku, alias
     * @return [int]  $checkActive
     */
    public function getDetail($key = null, $type = null, $storeId = null, $checkActive = 1)
    {
        if (empty($key)) {
            return null;
        }
        $storeId = empty($storeId) ? config('app.storeId') : $storeId;
        $tableStore = (new ShopStore)->getTable();
        $tableProductStore = (new ShopProductStore)->getTable();

        // Check store status  = 1
        $store = ShopStore::find($storeId);
        if (!$store || !$store->status) {
            return null;
        }

        if (config('app.storeId') != GC_ID_ROOT) {
            //If the store is not the primary store
            //Cannot view the product in another store
            $storeId = config('app.storeId');
        }

        $tableDescription = (new ShopProductDescription)->getTable();

        $dataSelect = $this->getTable().'.*, '.$tableDescription.'.*';

        $product = $this->leftJoin($tableDescription, $tableDescription . '.product_id', $this->getTable() . '.id');
        
        if (gc_check_multi_shop_installed()) {
            $dataSelect .= ', '.$tableProductStore.'.store_id';
            $product = $product->join($tableProductStore, $tableProductStore.'.product_id', $this->getTable() . '.id');
            $product = $product->join($tableStore, $tableStore . '.id', $tableProductStore.'.store_id');
            $product = $product->where($tableStore . '.status', '1');

            if (gc_check_multi_store_installed()
                || (
                    (gc_check_multi_vendor_installed())
                    && (!empty($this->gc_store_id) || config('app.storeId') != GC_ID_ROOT)
                    )
            ) {
                //store of vendor
                $product = $product->where($tableProductStore.'.store_id', $storeId);
            }
        }

        $product = $product->where($tableDescription . '.lang', gc_get_locale());

        if (empty($type)) {
            $product = $product->where($this->getTable().'.id', $key);
        } elseif ($type == 'alias') {
            $product = $product->where($this->getTable().'.alias', $key);
        } elseif ($type == 'sku') {
            $product = $product->where($this->getTable().'.sku', $key);
        } else {
            return null;
        }

        if ($checkActive) {
            $product = $product->where($this->getTable() .'.status', 1)->where($this->getTable() .'.approve', 1);
        }
        $product = $product->selectRaw($dataSelect);
        $product = $product
            ->with('images')
            ->with('stores')
            ->with('promotionPrice');
        $product = $product->first();
        return $product;
    }

    protected static function boot()
    {
        parent::boot();
        // before delete() method call this
        static::deleting(
            function ($product) {
                $product->images()->delete();
                $product->descriptions()->delete();
                $product->promotionPrice()->delete();
                $product->groups()->delete();
                $product->attributes()->delete();
                $product->downloadPath()->delete();
                $product->builds()->delete();
                $product->categories()->detach();
                $product->stores()->detach();

                //Delete custom field
                (new ShopCustomFieldDetail)
                ->join(GC_DB_PREFIX.'shop_custom_field', GC_DB_PREFIX.'shop_custom_field.id', GC_DB_PREFIX.'shop_custom_field_detail.custom_field_id')
                ->select('code', 'name', 'text')
                ->where(GC_DB_PREFIX.'shop_custom_field_detail.rel_id', $product->id)
                ->where(GC_DB_PREFIX.'shop_custom_field.type', 'shop_product')
                ->delete();
            }
        );

        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = gc_generate_id($type = 'shop_product');
            }
        });

    }

    /*
    *Get thumb
    */
    public function getThumb()
    {
        return gc_image_get_path_thumb($this->image);
    }

    /*
    *Get image
    */
    public function getImage()
    {
        return gc_image_get_path($this->image);
    }

    /**
     * [getUrl description]
     * @return [type] [description]
     */
    public function getUrl($lang = null)
    {
        return gc_route('product.detail', ['alias' => $this->alias, 'lang' => $lang ?? app()->getLocale()]);
    }

    /**
     * [getPercentDiscount description]
     * @return [type] [description]
     */
    public function getPercentDiscount()
    {
        return round((($this->price - $this->getFinalPrice()) / $this->price) * 100);
    }

    public function renderAttributeDetails()
    {
        return  view(
            'templates.'.gc_store('template').'.common.render_attribute',
            [
                'details' => $this->attributes()->get()->groupBy('attribute_group_id'),
                'groups' => ShopAttributeGroup::getListAll(),
            ]
        );
    }


    
    public function scopeSort($query, $sortBy = null, $sortOrder = 'asc')
    {
        $sortBy = $sortBy ?? 'id';
        return $query->orderBy($sortBy, $sortOrder);
    }

    /*
    *Condition:
    * -Active
    * -In of stock or allow order out of stock
    * -Date availabe
    * -Not GC_PRODUCT_GROUP
    */
    public function allowSale()
    {
        if (!gc_config('product_price', config('app.storeId'))) {
            return false;
        }
        if ($this->status &&
            (gc_config('product_preorder', config('app.storeId')) == 1 || $this->date_available === null || gc_time_now() >= $this->date_available)
            && (gc_config('product_buy_out_of_stock', config('app.storeId')) || $this->stock || empty(gc_config('product_stock', config('app.storeId'))))
            && $this->kind != GC_PRODUCT_GROUP
        ) {
            return true;
        } else {
            return false;
        }
    }

    /*
    Check promotion price
    */
    private function processPromotionPrice()
    {
        $promotion = $this->promotionPrice;
        if ($promotion) {
            if (($promotion['date_end'] >= date("Y-m-d") || $promotion['date_end'] === null)
                && ($promotion['date_start'] <= date("Y-m-d H:i:s") || $promotion['date_start'] === null)
                && $promotion['status_promotion'] = 1) {
                return $promotion['price_promotion'];
            }
        }

        return -1;
    }

    /*
    Upate stock, sold
    */
    public static function updateStock($product_id, $qty_change)
    {
        $item = self::find($product_id);
        if ($item) {
            $item->stock = $item->stock - $qty_change;
            $item->sold = $item->sold + $qty_change;
            $item->save();

            //Process build
            $product = self::find($product_id);
            if ($product->kind == GC_PRODUCT_BUILD) {
                foreach ($product->builds as $key => $build) {
                    $productBuild = $build->product;
                    $productBuild->stock -= $qty_change * $build->quantity;
                    $productBuild->sold += $qty_change * $build->quantity;
                    $productBuild->save();
                }
            }
        }
    }

    /**
     * Start new process get data
     *
     * @return  new model
     */
    public function start()
    {
        return new ShopProduct;
    }
    
    /**
     * Set product kind
     */
    private function setKind($kind)
    {
        if (is_array($kind)) {
            $this->gc_kind = $kind;
        } else {
            $this->gc_kind = array((int)$kind);
        }
        return $this;
    }

    /**
     * Set property product
     */
    private function setVirtual($property)
    {
        if ($property === 'all') {
            $this->gc_property = $property;
        } else {
            $this->gc_property = (int)$property;
        }
        return $this;
    }

    /**
     * Set array category
     *
     * @param   [array|int]  $category
     *
     */
    private function setCategory($category)
    {
        if (is_array($category)) {
            $this->gc_category = $category;
        } else {
            $this->gc_category = array($category);
        }
        return $this;
    }

    /**
     * Set array category store
     *
     * @param   [array|int]  $category
     *
     */
    private function setCategoryVendor($category)
    {
        if (is_array($category)) {
            $this->gc_category_vendor = $category;
        } else {
            $this->gc_category_vendor = array($category);
        }
        return $this;
    }

    /**
     * Set array brand
     *
     * @param   [array|int]  $brand
     *
     */
    private function setBrand($brand)
    {
        if (is_array($brand)) {
            $this->gc_brand = $brand;
        } else {
            $this->gc_brand = array($brand);
        }
        return $this;
    }

    /**
     * Set product promotion
     *
     */
    private function setPromotion()
    {
        $this->gc_promotion = 1;
        return $this;
    }

    /**
     * Set store id
     *
     */
    public function setStore($id)
    {
        $this->gc_store_id = $id;
        return $this;
    }

    /**
     * Set range price
     *
     */
    public function setRangePrice($price)
    {
        if ($price) {
            $this->gc_range_price = $price;
        }
        return $this;
    }

    /**
     * Set array ID product
     *
     * @param   [array|int]  $arrID
     *
     */
    private function setArrayID($arrID)
    {
        if (is_array($arrID)) {
            $this->gc_array_ID = $arrID;
        } else {
            $this->gc_array_ID = array($arrID);
        }
        return $this;
    }

    
    /**
     * Set array supplier
     *
     * @param   [array|int]  $supplier
     *
     */
    private function setSupplier($supplier)
    {
        if (is_array($supplier)) {
            $this->gc_supplier = $supplier;
        } else {
            $this->gc_supplier = array($supplier);
        }
        return $this;
    }

    /**
     * Product hot
     */
    public function getProductHot()
    {
        return $this->getProductPromotion();
    }

    /**
     * Product build
     */
    public function getProductBuild()
    {
        $this->setKind(GC_PRODUCT_BUILD);
        return $this;
    }

    /**
     * Product group
     */
    public function getProductGroup()
    {
        $this->setKind(GC_PRODUCT_GROUP);
        return $this;
    }

    /**
     * Product single
     */
    public function getProductSingle()
    {
        $this->setKind(GC_PRODUCT_SINGLE);
        return $this;
    }

    /**
     * Get product to array Catgory
     * @param   [array|int]  $arrCategory
     */
    public function getProductToCategory($arrCategory)
    {
        $this->setCategory($arrCategory);
        return $this;
    }

    /**
     * Get product to  Catgory store
     * @param   [int]  $category
     */
    public function getProductToCategoryStore($category)
    {
        $this->setCategoryVendor($category);
        return $this;
    }

    /**
     * Get product to array Brand
     * @param   [array|int]  $arrBrand
     */
    public function getProductToBrand($arrBrand)
    {
        $this->setBrand($arrBrand);
        return $this;
    }

    /**
     * Get product to array Supplier
     * @param   [array|int]  $arrSupplier
     */
    private function getProductToSupplier($arrSupplier)
    {
        $this->setSupplier($arrSupplier);
        return $this;
    }


    /**
     * Get product latest
     */
    public function getProductLatest()
    {
        $this->setLimit(10);
        $this->setSort(['id', 'desc']);
        return $this;
    }

    /**
     * Get product last view
     */
    public function getProductLastView()
    {
        $this->setLimit(10);
        $this->setSort(['date_available', 'desc']);
        return $this;
    }

    /**
     * Get product best sell
     */
    public function getProductBestSell()
    {
        $this->setLimit(10);
        $this->setSort(['sold', 'desc']);
        return $this;
    }

    /**
     * Get product promotion
     */
    public function getProductPromotion()
    {
        $this->setLimit(10);
        $this->setPromotion();
        return $this;
    }

    /**
     * Get product from list ID product
     *
     * @param   [array]  $arrID  array id product
     *
     * @return  [type]          [return description]
     */
    public function getProductFromListID($arrID)
    {
        if (is_array($arrID)) {
            $this->setArrayID($arrID);
        }
        return $this;
    }

    /**
     * build Query
     */
    public function buildQuery()
    {
        $tableDescription = (new ShopProductDescription)->getTable();
        $tableStore = (new ShopStore)->getTable();
        $tableProductStore = (new ShopProductStore)->getTable();
        $storeId = $this->gc_store_id ? $this->gc_store_id : config('app.storeId');
        //Select field
        $dataSelect = $this->getTable().'.*, '.$tableDescription.'.name, '.$tableDescription.'.keyword, '.$tableDescription.'.description';

        //description
        $query = $this
            //join description
            ->leftJoin($tableDescription, $tableDescription . '.product_id', $this->getTable() . '.id')
            ->where($tableDescription . '.lang', gc_get_locale());

        if (gc_check_multi_shop_installed()) {
            $dataSelect .= ', '.$tableProductStore.'.store_id';
            $query = $query->join($tableProductStore, $tableProductStore.'.product_id', $this->getTable() . '.id');
            $query = $query->join($tableStore, $tableStore . '.id', $tableProductStore.'.store_id');
            $query = $query->where($tableStore . '.status', '1');

            if (gc_check_multi_store_installed()
                || (
                    (gc_check_multi_vendor_installed())
                    && (!empty($this->gc_store_id) || config('app.storeId') != GC_ID_ROOT)
                    )
            ) {
                //store of vendor
                $query = $query->where($tableProductStore.'.store_id', $storeId);
            }

            if (count($this->gc_category_vendor) && gc_check_multi_vendor_installed()) {
                if (gc_config_global('MultiVendorPro')) {
                    $tablePTC = (new \App\Plugins\Other\MultiVendorPro\Models\VendorProductCategory)->getTable();
                }
                if (gc_config_global('MultiVendor')) {
                    $tablePTC = (new \App\Plugins\Other\MultiVendor\Models\VendorProductCategory)->getTable();
                }
                $query = $query->leftJoin($tablePTC, $tablePTC . '.product_id', $this->getTable() . '.id');
                $query = $query->whereIn($tablePTC . '.vendor_category_id', $this->gc_category_vendor);
            }
        }

        //search keyword
        if ($this->gc_keyword !='') {
            $query = $query->where(function ($sql) use ($tableDescription) {
                $sql->where($tableDescription . '.name', 'like', '%' . $this->gc_keyword . '%')
                    ->orWhere($tableDescription . '.keyword', 'like', '%' . $this->gc_keyword . '%')
                    ->orWhere($tableDescription . '.description', 'like', '%' . $this->gc_keyword . '%')
                    ->orWhere($this->getTable() . '.sku', 'like', '%' . $this->gc_keyword . '%');
            });
        }

        //Promotion
        if ($this->gc_promotion == 1) {
            $tablePromotion = (new ShopProductPromotion)->getTable();
            $query = $query->join($tablePromotion, $this->getTable() . '.id', '=', $tablePromotion . '.product_id')
                ->where($tablePromotion . '.status_promotion', 1)
                ->where(function ($query) use ($tablePromotion) {
                    $query->where($tablePromotion . '.date_end', '>=', date("Y-m-d"))
                        ->orWhereNull($tablePromotion . '.date_end');
                })
                ->where(function ($query) use ($tablePromotion) {
                    $query->where($tablePromotion . '.date_start', '<=', date("Y-m-d"))
                        ->orWhereNull($tablePromotion . '.date_start');
                });
        }
        $query = $query->selectRaw($dataSelect);
        $query = $query->with('promotionPrice');
        $query = $query->with('stores');
            

        if (count($this->gc_category)) {
            $tablePTC = (new ShopProductCategory)->getTable();
            $query = $query->leftJoin($tablePTC, $tablePTC . '.product_id', $this->getTable() . '.id');
            $query = $query->whereIn($tablePTC . '.category_id', $this->gc_category);
        }


        if (count($this->gc_array_ID)) {
            $query = $query->whereIn($this->getTable().'.id', $this->gc_array_ID);
        }

        $query = $query->where($this->getTable().'.status', 1);
        $query = $query->where($this->getTable().'.approve', 1);

        if ($this->gc_kind !== []) {
            $query = $query->whereIn($this->getTable().'.kind', $this->gc_kind);
        }

        //Filter with property
        if ($this->gc_property !== 'all') {
            $query = $query->where($this->getTable().'.property', $this->gc_property);
        }
        //Filter with brand
        if (count($this->gc_brand)) {
            $query = $query->whereIn($this->getTable().'.brand_id', $this->gc_brand);
        }
        //Filter with range price
        if ($this->gc_range_price) {
            $price = explode('__', $this->gc_range_price);
            $rangePrice['min'] = $price[0] ?? 0;
            $rangePrice['max'] = $price[1] ?? 0;
            if ($rangePrice['max']) {
                $query = $query->whereBetween($this->getTable().'.price', $rangePrice);
            }
        }
        //Filter with supplier
        if (count($this->gc_supplier)) {
            $query = $query->whereIn($this->getTable().'.supplier_id', $this->gc_supplier);
        }

        $query = $this->processMoreQuery($query);
        

        if ($this->gc_random) {
            $query = $query->inRandomOrder();
        } else {
            $checkSort = false;
            $ckeckId = false;
            if (is_array($this->gc_sort) && count($this->gc_sort)) {
                foreach ($this->gc_sort as  $rowSort) {
                    if (is_array($rowSort) && count($rowSort) == 2) {
                        if ($rowSort[0] == 'sort') {
                            //Process sort with sort value
                            $query = $query->orderBy($this->getTable().'.sort', $rowSort[1]);
                            $checkSort = true;
                        } elseif ($rowSort[0] == 'id') {
                            //Process sort with product id
                            $query = $query->orderBy($this->getTable().'.id', $rowSort[1]);
                            $ckeckId = true;
                        } else {
                            $query = $query->orderBy($rowSort[0], $rowSort[1]);
                        }
                    }
                }
            }
            //Use field "sort" if haven't above
            if (empty($checkSort)) {
                $query = $query->orderBy($this->getTable().'.sort', 'asc');
            }
            //Default, will sort id
            if (!$ckeckId) {
                $query = $query->orderBy($this->getTable().'.id', 'desc');
            }
        }

        //Hidden product out of stock
        if (empty(gc_config('product_display_out_of_stock', $storeId)) && !empty(gc_config('product_stock', $storeId))) {
            $query = $query->where($this->getTable().'.stock', '>', 0);
        }

        return $query;
    }

    /**
     * Get tax ID
     *
     * @return  [type]  [return description]
     */
    public function getTaxId()
    {
        if (!ShopTax::checkStatus()) {
            return 0;
        }
        if ($this->tax_id == 'auto') {
            return ShopTax::checkStatus();
        } else {
            $arrTaxList = ShopTax::getListAll();
            if ($this->tax_id == 0 || !$arrTaxList->has($this->tax_id)) {
                return 0;
            }
        }
        return $this->tax_id;
    }

    /**
     * Get value tax (%)
     *
     * @return  [type]  [return description]
     */
    public function getTaxValue()
    {
        $taxId = $this->getTaxId();
        if ($taxId) {
            $arrValue = ShopTax::getArrayValue();
            return $arrValue[$taxId] ?? 0;
        } else {
            return 0;
        }
    }

    /**
     * Go to shop vendor
     *
     * @return  [type]  [return description]
     */
    public function goToShop($code = null)
    {
        if (!$code) {
            $code = $this->stores()->first()->code;
        }
        return url(gc_path_vendor().'/'.$code);
    }

    /**
     * Show link to vendor
     *
     * @return void
     */
    public function displayVendor()
    {
        if ((gc_check_multi_vendor_installed()) && config('app.storeId') == GC_ID_ROOT) {
            $view = 'templates.'.gc_store('template'). '.vendor.display_vendor';
            if (!view()->exists($view)) {
                return;
            }
            $vendorCode = $this->stores()->first()->code;
            $vendorUrl = $this->goToShop($vendorCode);
            return  view(
                $view,
                [
                    'vendorCode' => $vendorCode,
                    'vendorUrl' => $vendorUrl,
                ]
            )->render();
        }
    }
}
