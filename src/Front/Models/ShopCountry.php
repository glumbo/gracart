<?php
namespace Glumbo\Gracart\Front\Models;

use Illuminate\Database\Eloquent\Model;
use Cache;

class ShopCountry extends Model
{
    use \Glumbo\Gracart\Front\Models\ModelTrait;
    
    public $table = GC_DB_PREFIX.'shop_country';
    public $timestamps               = false;
    private static $getListCountries = null;
    private static $getCodeAll = null;
    protected $connection = GC_CONNECTION;

    public static function getListAll()
    {
        if (self::$getListCountries === null) {
            self::$getListCountries = self::get()->keyBy('code');
        }
        return self::$getListCountries;
    }

    public static function getCodeAll()
    {
        if (gc_config_global('cache_status') && gc_config_global('cache_country')) {
            if (!Cache::has('cache_country')) {
                if (self::$getCodeAll === null) {
                    self::$getCodeAll = self::pluck('name', 'code')->all();
                }
                gc_set_cache('cache_country', self::$getCodeAll);
            }
            return Cache::get('cache_country');
        } else {
            if (self::$getCodeAll === null) {
                self::$getCodeAll = self::pluck('name', 'code')->all();
            }
            return self::$getCodeAll;
        }
    }
}
