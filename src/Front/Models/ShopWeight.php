<?php
#S-Cart/Core/Front/Models/ShopWeight.php
namespace Glumbo\Gracart\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopWeight extends Model
{
    use \Glumbo\Gracart\Front\Models\ModelTrait;
    
    public $table = GC_DB_PREFIX.'shop_weight';
    protected $connection = GC_CONNECTION;
    protected $guarded           = [];
    protected static $getList = null;

    public static function getListAll()
    {
        if (!self::$getList) {
            self::$getList = self::pluck('description', 'name')->all();
        }
        return self::$getList;
    }
}
