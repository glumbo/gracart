<?php
#S-Cart/Core/Front/Models/ShopLayoutPosition.php
namespace Glumbo\Gracart\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopLayoutPosition extends Model
{
    use \Glumbo\Gracart\Front\Models\ModelTrait;
    
    public $table = GC_DB_PREFIX.'shop_layout_position';
    protected $connection = GC_CONNECTION;
    
    public static function getPositions()
    {
        return self::pluck('name', 'key')->all();
    }
}
