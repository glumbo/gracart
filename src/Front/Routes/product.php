<?php
$prefixProduct = gc_config('PREFIX_PRODUCT')??'product';
if (file_exists(app_path('Http/Controllers/ShopProductController.php'))) {
    $nameSpaceFrontProduct = 'App\Http\Controllers';
} else {
    $nameSpaceFrontProduct = 'Glumbo\Gracart\Front\Controllers';
}

Route::group(['prefix' => $langUrl.$prefixProduct], function ($router) use ($suffix, $nameSpaceFrontProduct) {
    $router->get('/', $nameSpaceFrontProduct.'\ShopProductController@allProductsProcessFront')
        ->name('product.all');
    $router->get('/{alias}'.$suffix, $nameSpaceFrontProduct.'\ShopProductController@productDetailProcessFront')
        ->name('product.detail');
});
