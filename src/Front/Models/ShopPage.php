<?php
#S-Cart/Core/Front/Models/ShopPage.php
namespace Glumbo\Gracart\Front\Models;

use Illuminate\Database\Eloquent\Model;
use Cache;
use Glumbo\Gracart\Front\Models\ShopStore;


class ShopPage extends Model
{
    
    use \Glumbo\Gracart\Front\Models\ModelTrait;
    use \Glumbo\Gracart\Front\Models\UuidTrait;

    public $table          = GC_DB_PREFIX.'shop_page';
    protected $connection  = GC_CONNECTION;
    protected $guarded     = [];

    public function stores()
    {
        return $this->belongsToMany(ShopStore::class, ShopPageStore::class, 'page_id', 'store_id');
    }

    public function descriptions()
    {
        return $this->hasMany(ShopPageDescription::class, 'page_id', 'id');
    }

    //Function get text description
    public function getText()
    {
        return $this->descriptions()->where('lang', gc_get_locale())->first();
    }
    public function getTitle()
    {
        return $this->getText()->title ?? '';
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
        return $this->getText()->content;
    }
    //End  get text description


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

    public function getUrl($lang = null)
    {
        return gc_route('page.detail', ['alias' => $this->alias, 'lang' => $lang ?? app()->getLocale()]);
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
        $tableDescription = (new ShopPageDescription)->getTable();

        $dataSelect = $this->getTable().'.*, '.$tableDescription.'.*';
        $page = $this->selectRaw($dataSelect)
            ->leftJoin($tableDescription, $tableDescription . '.page_id', $this->getTable() . '.id')
            ->where($tableDescription . '.lang', gc_get_locale());

        $storeId = config('app.storeId');
        if (gc_check_multi_shop_installed()) {
            $tablePageStore = (new ShopPageStore)->getTable();
            $tableStore = (new ShopStore)->getTable();
            $page = $page->join($tablePageStore, $tablePageStore.'.page_id', $this->getTable() . '.id');
            $page = $page->join($tableStore, $tableStore . '.id', $tablePageStore.'.store_id');
            $page = $page->where($tableStore . '.status', '1');
            $page = $page->where($tablePageStore.'.store_id', $storeId);
        }

        if ($type === null) {
            $page = $page->where($this->getTable() .'.id', $key);
        } else {
            $page = $page->where($type, $key);
        }
        if ($checkActive) {
            $page = $page->where($this->getTable() .'.status', 1);
        }

        return $page->first();
    }

    protected static function boot()
    {
        parent::boot();
        // before delete() method call this
        static::deleting(
            function ($page) {
                $page->descriptions()->delete();
                $page->stores()->detach();

                //Delete custom field
                (new ShopCustomFieldDetail)
                ->join(GC_DB_PREFIX.'shop_custom_field', GC_DB_PREFIX.'shop_custom_field.id', GC_DB_PREFIX.'shop_custom_field_detail.custom_field_id')
                ->where(GC_DB_PREFIX.'shop_custom_field_detail.rel_id', $page->id)
                ->where(GC_DB_PREFIX.'shop_custom_field.type', 'shop_page')
                ->delete();
            }
        );
        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = gc_generate_id($type = 'shop_page');
            }
        });
    }


    /**
     * Start new process get data
     *
     * @return  new model
     */
    public function start()
    {
        return new ShopPage;
    }

    /**
     * build Query
     */
    public function buildQuery()
    {
        $tableDescription = (new ShopPageDescription)->getTable();

        $dataSelect = $this->getTable().'.*, '.$tableDescription.'.*';
        $query = $this->selectRaw($dataSelect)
            ->leftJoin($tableDescription, $tableDescription . '.page_id', $this->getTable() . '.id')
            ->where($tableDescription . '.lang', gc_get_locale());

        $storeId = config('app.storeId');
        if (gc_check_multi_shop_installed()) {
            $tablePageStore = (new ShopPageStore)->getTable();
            $tableStore = (new ShopStore)->getTable();
            $query = $query->join($tablePageStore, $tablePageStore.'.page_id', $this->getTable() . '.id');
            $query = $query->join($tableStore, $tableStore . '.id', $tablePageStore.'.store_id');
            $query = $query->where($tableStore . '.status', '1');
            $query = $query->where($tablePageStore.'.store_id', $storeId);
        }

        //search keyword
        if ($this->gc_keyword !='') {
            $query = $query->where(function ($sql) use ($tableDescription) {
                $sql->where($tableDescription . '.title', 'like', '%' . $this->gc_keyword . '%')
                ->orWhere($tableDescription . '.keyword', 'like', '%' . $this->gc_keyword . '%')
                ->orWhere($tableDescription . '.description', 'like', '%' . $this->gc_keyword . '%');
            });
        }

        $query = $query->where($this->getTable() .'.status', 1);

        $query = $this->processMoreQuery($query);
        

        if ($this->random) {
            $query = $query->inRandomOrder();
        } else {
            if (is_array($this->gc_sort) && count($this->gc_sort)) {
                foreach ($this->gc_sort as  $rowSort) {
                    if (is_array($rowSort) && count($rowSort) == 2) {
                        $query = $query->sort($rowSort[0], $rowSort[1]);
                    }
                }
            }
        }

        return $query;
    }
}
