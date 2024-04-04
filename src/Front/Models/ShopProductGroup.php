<?php
#S-Cart/Core/Front/Models/ShopProductGroup.php
namespace Glumbo\Gracart\Front\Models;

use Illuminate\Database\Eloquent\Model;
use Glumbo\Gracart\Front\Models\ShopProduct;

class ShopProductGroup extends Model
{
    use \Glumbo\Gracart\Front\Models\ModelTrait;
    
    protected $primaryKey = ['group_id', 'product_id'];
    public $incrementing  = false;
    protected $guarded    = [];
    public $timestamps    = false;
    public $table = GC_DB_PREFIX.'shop_product_group';
    protected $connection = GC_CONNECTION;

    public function product()
    {
        return $this->belongsTo(ShopProduct::class, 'product_id', 'id');
    }
}
