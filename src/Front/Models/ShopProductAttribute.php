<?php
#S-Cart/Core/Front/Models/ShopProductAttribute.php
namespace Glumbo\Gracart\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopProductAttribute extends Model
{
    use \Glumbo\Gracart\Front\Models\ModelTrait;
    public $timestamps    = false;
    public $table = GC_DB_PREFIX.'shop_product_attribute';
    protected $guarded = [];
    protected $connection = GC_CONNECTION;
    public function attGroup()
    {
        return $this->belongsTo(ShopAttributeGroup::class, 'attribute_group_id', 'id');
    }
}
