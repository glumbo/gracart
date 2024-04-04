<?php
#Glumbo\Gracart\Front\Models\ShopShippingStatus.php
namespace Glumbo\Gracart\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopShippingStatus extends Model
{
    use \Glumbo\Gracart\Front\Models\ModelTrait;
    
    public $table = GC_DB_PREFIX.'shop_shipping_status';
    protected $guarded           = [];
    protected static $listStatus = null;
    protected $connection = GC_CONNECTION;
    public static function getIdAll()
    {
        if (!self::$listStatus) {
            self::$listStatus = self::pluck('name', 'id')->all();
        }
        return self::$listStatus;
    }
}
