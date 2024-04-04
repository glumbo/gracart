<?php
#S-Cart/Core/Front/Models/ShopBannerStore.php
namespace Glumbo\Gracart\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopBannerStore extends Model
{
    use \Glumbo\Gracart\Front\Models\ModelTrait;
    
    protected $primaryKey = ['store_id', 'banner_id'];
    public $incrementing  = false;
    protected $guarded    = [];
    public $timestamps    = false;
    public $table = GC_DB_PREFIX.'shop_banner_store';
    protected $connection = GC_CONNECTION;
}
