<?php
#S-Cart/Core/Front/Models/ShopLength.php
namespace Glumbo\Gracart\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopLength extends Model
{
    use \Glumbo\Gracart\Front\Models\ModelTrait;
    
    public $table = GC_DB_PREFIX.'shop_length';
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
