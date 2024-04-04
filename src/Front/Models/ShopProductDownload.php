<?php
#S-Cart/Core/Front/Models/ShopProductDownload.php
namespace Glumbo\Gracart\Front\Models;

use Illuminate\Database\Eloquent\Model;
use Glumbo\Gracart\Front\Models\ShopProduct;

class ShopProductDownload extends Model
{
    use \Glumbo\Gracart\Front\Models\ModelTrait;
    use \Glumbo\Gracart\Front\Models\UuidTrait;

    protected $primaryKey = ['download_path', 'product_id'];
    public $incrementing  = false;
    protected $guarded    = [];
    public $timestamps    = false;
    public $table = GC_DB_PREFIX.'shop_product_download';
    protected $connection = GC_CONNECTION;
    
    public function product()
    {
        return $this->belongsTo(ShopProduct::class, 'product_id', 'id');
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
                $model->{$model->getKeyName()} = gc_generate_id($type = 'shop_product_download');
            }
        });
    }
}
