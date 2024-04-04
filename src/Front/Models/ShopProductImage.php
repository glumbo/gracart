<?php
#S-Cart/Core/Front/Models/ShopProductImage.php
namespace Glumbo\Gracart\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopProductImage extends Model
{
    use \Glumbo\Gracart\Front\Models\ModelTrait;
    use \Glumbo\Gracart\Front\Models\UuidTrait;

    public $timestamps = false;
    public $table = GC_DB_PREFIX.'shop_product_image';
    protected $guarded = [];
    protected $connection = GC_CONNECTION;

    /*
    Get thumb
     */
    public function getThumb()
    {
        return gc_image_get_path_thumb($this->image);
    }

    /*
    Get image
     */
    public function getImage()
    {
        return gc_image_get_path($this->image);
    }

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
                $model->{$model->getKeyName()} = gc_generate_id($type = 'shop_product_image');
            }
        });
    }
}
