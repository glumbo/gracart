<?php
#S-Cart/Core/Front/Models/ShopStoreCss.php
namespace Glumbo\Gracart\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopStoreCss extends Model
{
    use \Glumbo\Gracart\Front\Models\ModelTrait;

    protected $primaryKey = 'store_id';
    public $incrementing  = false;
    protected $guarded    = [];
    public $table = GC_DB_PREFIX.'shop_store_css';
    protected $connection = GC_CONNECTION;

    protected static function boot()
    {
        parent::boot();
        // before delete() method call this
        static::deleting(
            function ($obj) {
            //
            }
        );
    }
}
