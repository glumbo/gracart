<?php
#S-Cart/Core/Front/Models/ShopProductPromotion.php
namespace Glumbo\Gracart\Front\Models;

use Illuminate\Database\Eloquent\Model;
use Glumbo\Gracart\Front\Models\ShopProduct;

class ShopProductPromotion extends Model
{
    use \Glumbo\Gracart\Front\Models\ModelTrait;
    
    public $table = GC_DB_PREFIX.'shop_product_promotion';
    protected $guarded    = [];
    protected $primaryKey = 'product_id';
    public $incrementing  = false;
    protected $connection = GC_CONNECTION;

    public function product()
    {
        return $this->belongsTo(ShopProduct::class, 'product_id', 'id');
    }
}
