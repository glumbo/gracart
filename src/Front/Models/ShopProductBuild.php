<?php
#S-Cart/Core/Front/Models/ShopProductBuild.php
namespace Glumbo\Gracart\Front\Models;

use Illuminate\Database\Eloquent\Model;
use Glumbo\Gracart\Front\Models\ShopProduct;

class ShopProductBuild extends Model
{
    use \Glumbo\Gracart\Front\Models\ModelTrait;
    
    protected $primaryKey = ['build_id', 'product_id'];
    public $incrementing  = false;
    protected $guarded    = [];
    public $timestamps    = false;
    public $table = GC_DB_PREFIX.'shop_product_build';
    protected $connection = GC_CONNECTION;
    public function product()
    {
        return $this->belongsTo(ShopProduct::class, 'product_id', 'id');
    }
}
