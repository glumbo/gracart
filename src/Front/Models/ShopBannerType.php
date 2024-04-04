<?php
#S-Cart/Core/Front/Models/ShopBannerType.php
namespace Glumbo\Gracart\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopBannerType extends Model
{
    use \Glumbo\Gracart\Front\Models\ModelTrait;
    
    public $table = GC_DB_PREFIX.'shop_banner_type';
    protected $guarded   = [];
    protected $connection = GC_CONNECTION;
}
