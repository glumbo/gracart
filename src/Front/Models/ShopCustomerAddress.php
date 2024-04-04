<?php
#S-Cart/Core/Front/Models/ShopCustomerAddress.php
namespace Glumbo\Gracart\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopCustomerAddress extends Model
{
    use \Glumbo\Gracart\Front\Models\ModelTrait;
    use \Glumbo\Gracart\Front\Models\UuidTrait;

    protected $guarded    = [];
    public $table = GC_DB_PREFIX.'shop_customer_address';
    protected $connection = GC_CONNECTION;

    protected static function boot()
    {
        parent::boot();
        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = gc_generate_id($type = 'shop_customer_address');
            }
        });
    }
}
