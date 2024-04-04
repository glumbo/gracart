<?php
#S-Cart/Core/Front/Models/ShopCustomFieldDetail.php
namespace Glumbo\Gracart\Front\Models;

use Illuminate\Database\Eloquent\Model;
use Cache;

class ShopCustomFieldDetail extends Model
{
    use \Glumbo\Gracart\Front\Models\ModelTrait;
    use \Glumbo\Gracart\Front\Models\UuidTrait;
    
    public $table          = GC_DB_PREFIX.'shop_custom_field_detail';
    protected $connection  = GC_CONNECTION;
    protected $guarded     = [];

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

        //Uuid
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = gc_generate_id($type = 'CFD');
            }
        });
    }
}
