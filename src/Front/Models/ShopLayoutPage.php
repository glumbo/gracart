<?php
#S-Cart/Core/Front/Models/ShopLayoutPage.php
namespace Glumbo\Gracart\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopLayoutPage extends Model
{
    use \Glumbo\Gracart\Front\Models\ModelTrait;

    public $table = GC_DB_PREFIX.'shop_layout_page';
    protected $connection = GC_CONNECTION;

    public static function getPages()
    {
        return self::pluck('name', 'key')->all();
    }

    //Function get text description
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
