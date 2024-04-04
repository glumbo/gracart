<?php
#S-Cart/Core/Front/Models/ShopSubscribe.php
namespace Glumbo\Gracart\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopSubscribe extends Model
{
    use \Glumbo\Gracart\Front\Models\ModelTrait;
    use \Glumbo\Gracart\Front\Models\UuidTrait;

    public $table = GC_DB_PREFIX.'shop_subscribe';
    protected $guarded      = [];
    protected $connection = GC_CONNECTION;

    protected static function boot()
    {
        parent::boot();
        // before delete() method call this
        static::deleting(
            function ($model) {
            //
            }
        );
        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = gc_generate_id($type = 'shop_subscribe');
            }
        });
    }
}
