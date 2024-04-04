<?php
#S-Cart/Core/Front/Models/ShopPageDescription.php
namespace Glumbo\Gracart\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopPageDescription extends Model
{
    use \Glumbo\Gracart\Front\Models\ModelTrait;
    
    protected $primaryKey = ['lang', 'page_id'];
    public $incrementing  = false;
    protected $guarded    = [];
    public $timestamps    = false;
    public $table = GC_DB_PREFIX.'shop_page_description';
    protected $connection = GC_CONNECTION;
}
