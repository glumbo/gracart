<?php
namespace Glumbo\Gracart\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopCategoryDescription extends Model
{
    use \Glumbo\Gracart\Front\Models\ModelTrait;
    
    protected $primaryKey = ['category_id', 'lang'];
    public $incrementing  = false;
    public $timestamps    = false;
    public $table = GC_DB_PREFIX.'shop_category_description';
    protected $connection = GC_CONNECTION;
    protected $guarded    = [];
}
