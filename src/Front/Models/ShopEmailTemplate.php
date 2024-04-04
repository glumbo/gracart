<?php
#S-Cart/Core/Front/Models/ShopEmailTemplate.php
namespace Glumbo\Gracart\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopEmailTemplate extends Model
{
    use \Glumbo\Gracart\Front\Models\ModelTrait;
    use \Glumbo\Gracart\Front\Models\UuidTrait;
    
    public $table = GC_DB_PREFIX.'shop_email_template';
    protected $guarded = [];
    protected $connection = GC_CONNECTION;

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
                $model->{$model->getKeyName()} = gc_generate_id($type = 'shop_email_template');
            }
        });
    }
}
