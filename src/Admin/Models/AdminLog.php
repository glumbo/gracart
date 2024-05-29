<?php

namespace Glumbo\Gracart\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class AdminLog extends Model
{
    protected $guarded = [];
    public $table = GC_DB_PREFIX.'admin_log';
    public static $methodColors = [
        'GET' => 'green',
        'POST' => 'yellow',
        'PUT' => 'blue',
        'DELETE' => 'red',
    ];

    public static $methods = [
        'GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH',
        'LINK', 'UNLINK', 'COPY', 'HEAD', 'PURGE',
    ];

    /**
     * Log belongs to users.
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(AdminUser::class);
    }

    public static function getCountLogs()
    {
        return self::where('user_id', admin()->user()->id)
            ->count();
    }
    /**
     * Get count notice new
     *
     * @return  [type]  [return description]
     */
    public static function getTopLogs()
    {
        return self::where('user_id', admin()->user()->id)
            ->orderBy('id','desc')
            ->limit(10)
            ->get();
    }
}
