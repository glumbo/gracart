<?php
#S-Cart/Core/Front/Models/ShopPaymentStatus.php
namespace Glumbo\Gracart\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopPaymentStatus extends Model
{
    use \Glumbo\Gracart\Front\Models\ModelTrait;
    
    public $table = GC_DB_PREFIX.'shop_payment_status';
    protected $guarded   = [];
    protected $connection = GC_CONNECTION;
    protected static $listStatus = null;
    public static function getIdAll()
    {
        if (!self::$listStatus) {
            self::$listStatus = self::pluck('name', 'id')->all();
        }
        return self::$listStatus;
    }
}
