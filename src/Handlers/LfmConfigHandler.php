<?php

namespace Glumbo\Gracart\Handlers;

class LfmConfigHandler extends \UniSharp\LaravelFilemanager\Handlers\ConfigHandler
{
    public function userField()
    {
        // If domain is root, dont split folder
        if (session('adminStoreId') == GC_ID_ROOT) {
            return ;
        }

        if (gc_check_multi_vendor_installed()) {
            return session('adminStoreId');
        } else {
            return;
        }
    }
}
