<?php

namespace Glumbo\Gracart\Front\Middleware;

use Glumbo\Gracart\Front\Models\ShopCurrency;
use Closure;
use Session;

class Currency
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $currency = session('currency') ?? gc_store('currency');
        if (!array_key_exists($currency, gc_currency_all_active())) {
            $currency = array_key_first(gc_currency_all_active());
        }
        ShopCurrency::setCode($currency);
        return $next($request);
    }
}
