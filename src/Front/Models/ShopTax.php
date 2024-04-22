<?php
#S-Cart/Core/Front/Models/ShopTax.php
namespace Glumbo\Gracart\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopTax extends Model
{
    use \Glumbo\Gracart\Front\Models\ModelTrait;
    
    public $table = GC_DB_PREFIX.'shop_tax';
    protected $guarded = [];
    protected $connection = GC_CONNECTION;

    private static $getList = null;
    private static $status = null;
    private static $arrayId = null;
    private static $arrayValue = null;

    /**
     * Get list item
     *
     * @return  [type]  [return description]
     */
    public static function getListAll()
    {
        if (self::$getList === null) {
            self::$getList = self::get()->keyBy('id');
        }
        return self::$getList;
    }

    /**
     * Get item
     *
     * @return  [type]  [return description]
     */
    public static function getAuto()
    {
        $auto = new (self::class);
        $auto->id = 'auto';
        $auto->tax_id = 'auto';
        $auto->name = gc_language_render('admin.tax.auto');
        $auto->value = gc_language_render('admin.tax.auto');
        return $auto;
    }

    /**
     * Get list item
     *
     * @return  [type]  [return description]
     */
    public static function getListOptions()
    {
        $getListOptions = collect(self::getListAll());
        $getListOptions->prepend(self::getAuto());
        $getListOptions->prepend(self::getZero());

        return $getListOptions;
    }



    /**
     * Get list item
     *
     * @return  [type]  [return description]
     */
    public static function getZero()
    {
        $zero = new (self::class);
        $zero->id = 0;
        $zero->tax_id = 0;
        $zero->name = gc_language_render('admin.tax.non_tax');
        $zero->value = 0;
        return $zero;
    }

    /**
     * Get array ID
     *
     * @return  [type]  [return description]
     */
    public static function getArrayId()
    {
        if (self::$arrayId === null) {
            self::$arrayId = self::pluck('id')->all();
        }
        return self::$arrayId;
    }

    /**
     * Get array value
     *
     * @return  [type]  [return description]
     */
    public static function getArrayValue()
    {
        if (self::$arrayValue === null) {
            self::$arrayValue = self::pluck('value', 'id')->all();
        }
        return self::$arrayValue;
    }


    /**
     * Check status tax
     *
     * @return  [type]  [return description]
     */
    public static function checkStatus()
    {
        $arrTaxId = self::getArrayId();
        if (self::$status === null) {
            if (!gc_config('product_tax')) {
                $status = 0;
            } else {
                if (!in_array(gc_config('product_tax'), $arrTaxId)) {
                    $status = 0;
                } else {
                    $status = gc_config('product_tax');
                }
            }
            self::$status = $status;
        }
        return self::$status;
    }
}
