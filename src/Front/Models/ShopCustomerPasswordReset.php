<?php
#S-Cart/Core/Front/Models/ShopCustomerPasswordReset.php
namespace Glumbo\Gracart\Front\Models;

use Illuminate\Database\Eloquent\Model;

class ShopCustomerPasswordReset extends Model
{
    use \Glumbo\Gracart\Front\Models\ModelTrait;
    
    protected $primaryKey = ['token'];
    public $incrementing  = false;
    protected $guarded    = [];
    public $timestamps    = false;
    public $table = GC_DB_PREFIX.'shop_password_resets';
    protected $connection = GC_CONNECTION;
}
