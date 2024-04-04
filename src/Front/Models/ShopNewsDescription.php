<?php
#S-Cart/Core/Front/Models/ShopNewsDescription.php
namespace Glumbo\Gracart\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopNewsDescription extends Model
{
    use \Glumbo\Gracart\Front\Models\ModelTrait;
    
    protected $primaryKey = ['lang', 'news_id'];
    public $incrementing = false;
    protected $guarded = [];
    public $timestamps = false;
    public $table = GC_DB_PREFIX.'shop_news_description';
    protected $connection = GC_CONNECTION;
}
