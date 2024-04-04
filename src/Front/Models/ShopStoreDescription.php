<?php
#S-Cart/Core/Front/Models/ShopStoreDescription.php
namespace Glumbo\Gracart\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopStoreDescription extends Model
{
    use \Glumbo\Gracart\Front\Models\ModelTrait;
    
    protected $primaryKey = ['lang', 'store_id'];
    public $incrementing = false;
    protected $guarded = [];
    public $timestamps = false;
    public $table = GC_DB_PREFIX.'admin_store_description';
    protected $connection = GC_CONNECTION;
}
