<?php

namespace Glumbo\Gracart\Api\Controllers;

use Glumbo\Gracart\Front\Controllers\RootFrontController;
use Illuminate\Http\Request;
use Glumbo\Gracart\Front\Models\ShopOrder;

class MemberController extends RootFrontController
{

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function getInfo(Request $request)
    {
        return response()->json($request->user());
    }
}
