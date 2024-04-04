<?php
#S-Cart/Core/Front/Models/ShopProductDescription.php
namespace Glumbo\Gracart\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopProductDescription extends Model
{
    use \Glumbo\Gracart\Front\Models\ModelTrait;
    
    protected $primaryKey = ['lang', 'product_id'];
    public $incrementing  = false;
    protected $guarded    = [];
    public $timestamps    = false;
    public $table = GC_DB_PREFIX.'shop_product_description';
    protected $connection = GC_CONNECTION;
}
