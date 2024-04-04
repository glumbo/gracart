<?php
#S-Cart/Core/Front/Models/ShopProductProperty.php
namespace Glumbo\Gracart\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopProductProperty extends Model
{
    use \Glumbo\Gracart\Front\Models\ModelTrait;
    
    public $table = GC_DB_PREFIX.'shop_product_property';
    protected $guarded   = [];
    protected $connection = GC_CONNECTION;
}
