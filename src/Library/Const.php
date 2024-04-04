<?php

//Product kind
define('GC_PRODUCT_SINGLE', 0);
define('GC_PRODUCT_BUILD', 1);
define('GC_PRODUCT_GROUP', 2);
//Product property
define('GC_PROPERTY_PHYSICAL', 'physical');
define('GC_PROPERTY_DOWNLOAD', 'download');
// list ID admin guard
define('GC_GUARD_ADMIN', ['1']); // admin
// list ID language guard
define('GC_GUARD_LANGUAGE', ['1', '2']); // vi, en
// list ID currency guard
define('GC_GUARD_CURRENCY', ['1', '2']); // vndong , usd
// list ID ROLES guard
define('GC_GUARD_ROLES', ['1', '2']); // admin, only view

/**
 * Admin define
 */
define('GC_ADMIN_MIDDLEWARE', ['web', 'admin']);
define('GC_FRONT_MIDDLEWARE', ['web', 'front']);
define('GC_API_MIDDLEWARE', ['api', 'api.extend']);
define('GC_CONNECTION', 'mysql');
define('GC_CONNECTION_LOG', 'mysql');
//Prefix url admin
define('GC_ADMIN_PREFIX', config('const.ADMIN_PREFIX'));
//Prefix database
define('GC_DB_PREFIX', config('const.DB_PREFIX'));
// Root ID store
define('GC_ID_ROOT', 1);
define('GC_ID_GLOBAL', 0);
