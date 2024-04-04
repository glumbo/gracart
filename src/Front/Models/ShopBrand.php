<?php
namespace Glumbo\Gracart\Front\Models;

use Glumbo\Gracart\Front\Models\ShopProduct;
use Illuminate\Database\Eloquent\Model;
use Glumbo\Gracart\Front\Models\ShopStore;


class ShopBrand extends Model
{
    use \Glumbo\Gracart\Front\Models\ModelTrait;
    use \Glumbo\Gracart\Front\Models\UuidTrait;

    public $table = GC_DB_PREFIX.'shop_brand';
    protected $guarded = [];
    private static $getList = null;
    protected $connection = GC_CONNECTION;


    public static function getListAll()
    {
        if (self::$getList === null) {
            self::$getList = self::get()->keyBy('id');
        }
        return self::$getList;
    }

    public function products()
    {
        return $this->hasMany(ShopProduct::class, 'brand_id', 'id');
    }

    public function stores()
    {
        return $this->belongsToMany(ShopStore::class, ShopBrandStore::class, 'brand_id', 'store_id');
    }

    protected static function boot()
    {
        parent::boot();
        // before delete() method call this
        static::deleting(function ($brand) {
            $brand->stores()->detach();

            //Delete custom field
            (new ShopCustomFieldDetail)
            ->join(GC_DB_PREFIX.'shop_custom_field', GC_DB_PREFIX.'shop_custom_field.id', GC_DB_PREFIX.'shop_custom_field_detail.custom_field_id')
            ->where(GC_DB_PREFIX.'shop_custom_field_detail.rel_id', $brand->id)
            ->where(GC_DB_PREFIX.'shop_custom_field.type', 'shop_brand')
            ->delete();
        });

        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = gc_generate_id($type = 'shop_brand');
            }
        });
    }

    /**
     * [getUrl description]
     * @return [type] [description]
     */
    public function getUrl($lang = null)
    {
        return gc_route('brand.detail', ['alias' => $this->alias, 'lang' => $lang ?? app()->getLocale()]);
    }

    /*
    Get thumb
    */
    public function getThumb()
    {
        return gc_image_get_path_thumb($this->image);
    }

    /*
    Get image
    */
    public function getImage()
    {
        return gc_image_get_path($this->image);
    }


    public function scopeSort($query, $sortBy = null, $sortOrder = 'asc')
    {
        $sortBy = $sortBy ?? 'sort';
        return $query->orderBy($sortBy, $sortOrder);
    }
    

    /**
     * Get page detail
     *
     * @param   [string]  $key     [$key description]
     * @param   [string]  $type  [id, alias]
     * @param   [int]  $checkActive
     *
     */
    public function getDetail($key, $type = null, $checkActive = 1)
    {
        if (empty($key)) {
            return null;
        }
        $storeId = config('app.storeId');
        $dataSelect = $this->getTable().'.*';
        $data = $this->selectRaw($dataSelect);
        if ($type === null) {
            $data = $data->where($this->getTable().'.id', $key);
        } else {
            $data = $data->where($type, $key);
        }
        if (gc_check_multi_shop_installed()) {
            $tableBrandStore = (new ShopBrandStore)->getTable();
            $tableStore = (new ShopStore)->getTable();
            $data = $data->join($tableBrandStore, $tableBrandStore.'.brand_id', $this->getTable() . '.id');
            $data = $data->join($tableStore, $tableStore . '.id', $tableBrandStore.'.store_id');
            $data = $data->where($tableStore . '.status', '1');
            $data = $data->where($tableBrandStore.'.store_id', $storeId);
        }
        if ($checkActive) {
            $data = $data->where($this->getTable() .'.status', 1);
        }
        return $data->first();
    }


    /**
     * Start new process get data
     *
     * @return  new model
     */
    public function start()
    {
        return new ShopBrand;
    }

    /**
     * build Query
     */
    public function buildQuery()
    {
        $storeId = config('app.storeId');
        $dataSelect = $this->getTable().'.*';
        $query = $this->selectRaw($dataSelect)
            ->where($this->getTable().'.status', 1);

        if (gc_check_multi_shop_installed()) {
            $tableBrandStore = (new ShopBrandStore)->getTable();
            $tableStore = (new ShopStore)->getTable();
            $query = $query->join($tableBrandStore, $tableBrandStore.'.brand_id', $this->getTable() . '.id');
            $query = $query->join($tableStore, $tableStore . '.id', $tableBrandStore.'.store_id');
            $query = $query->where($tableStore . '.status', '1');
            $query = $query->where($tableBrandStore.'.store_id', $storeId);
        }

        $query = $this->processMoreQuery($query);
        
        if ($this->gc_random) {
            $query = $query->inRandomOrder();
        } else {
            $checkSort = false;
            if (is_array($this->gc_sort) && count($this->gc_sort)) {
                foreach ($this->gc_sort as  $rowSort) {
                    if (is_array($rowSort) && count($rowSort) == 2) {
                        if ($rowSort[0] == 'sort') {
                            $checkSort = true;
                        }
                        $query = $query->sort($rowSort[0], $rowSort[1]);
                    }
                }
            }
            //Use field "sort" if haven't above
            if (empty($checkSort)) {
                $query = $query->orderBy($this->getTable().'.sort', 'asc');
            }
            //Default, will sort id
            $query = $query->orderBy($this->getTable().'.id', 'desc');
        }

        return $query;
    }
}
