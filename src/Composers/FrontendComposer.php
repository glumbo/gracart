<?php

namespace Glumbo\Gracart\Composers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class FrontendComposer {

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('gc_templatePath', 'templates.frontend.' . gc_store('frontend_template'));
        $view->with('gc_templateFile', 'templates/frontend/' . gc_store('frontend_template'));
    }

}