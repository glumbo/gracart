<?php
#S-Cart/Core/Front/Models/ShopLinkGroup.php
namespace Glumbo\Gracart\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopLinkGroup extends Model
{
    use \Glumbo\Gracart\Front\Models\ModelTrait;
    
    public $table = GC_DB_PREFIX.'shop_link_group';
    protected $guarded   = [];
    protected $connection = GC_CONNECTION;
}
