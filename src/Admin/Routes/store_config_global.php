<?php
if (file_exists(app_path('Admin/Controllers/AdminConfigGlobalController.php'))) {
    $nameSpaceAdminStoreConfigGlobal = 'App\Admin\Controllers';
} else {
    $nameSpaceAdminStoreConfigGlobal = 'Glumbo\Gracart\Admin\Controllers';
}
Route::group(['prefix' => 'config'], function () use ($nameSpaceAdminStoreConfigGlobal) {
    Route::get('/webhook', $nameSpaceAdminStoreConfigGlobal.'\AdminConfigGlobalController@webhook')->name('admin_config_global.webhook');
    Route::post('/update', $nameSpaceAdminStoreConfigGlobal.'\AdminConfigGlobalController@update')->name('admin_config_global.update');
});