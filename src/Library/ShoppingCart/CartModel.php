<?php
namespace Glumbo\Gracart\Library\ShoppingCart;

use Illuminate\Database\Eloquent\Model;

class CartModel extends Model
{
    protected $primaryKey = null;
    public $incrementing  = false;
    public $table = GC_DB_PREFIX.'shop_shoppingcart';
    protected $guarded = [];
    protected $connection = GC_CONNECTION;
}
