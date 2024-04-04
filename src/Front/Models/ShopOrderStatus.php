<?php
#S-Cart/Core/Front/Models/ShopOrderStatus.php
namespace Glumbo\Gracart\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopOrderStatus extends Model
{
    use \Glumbo\Gracart\Front\Models\ModelTrait;
    
    public $table = GC_DB_PREFIX.'shop_order_status';
    protected $connection = GC_CONNECTION;
    protected $guarded           = [];
    protected static $listStatus = null;

    public static function getIdAll()
    {
        if (!self::$listStatus) {
            self::$listStatus = self::pluck('name', 'id')->all();
        }
        return self::$listStatus;
    }
}
