<?php
#S-Cart/Core/Front/Models/ShopOrderHistory.php
namespace Glumbo\Gracart\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopOrderHistory extends Model
{
    use \Glumbo\Gracart\Front\Models\ModelTrait;
    use \Glumbo\Gracart\Front\Models\UuidTrait;

    public $table = GC_DB_PREFIX.'shop_order_history';
    protected $connection = GC_CONNECTION;
    const CREATED_AT = 'add_date';
    const UPDATED_AT = null;
    protected $guarded           = [];

    protected static function boot()
    {
        parent::boot();
        // before delete() method call this
        static::deleting(
            function ($obj) {
                //
            }
        );

        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = gc_generate_id($type = 'shop_order_detail');
            }
        });
    }
}
