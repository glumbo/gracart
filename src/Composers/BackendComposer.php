<?php

namespace Glumbo\Gracart\Composers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class BackendComposer {

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with('gc_templatePath', 'templates.backend.' . gc_store('backend_template'));
        $view->with('gc_templateFile', 'templates/backend/' . gc_store('backend_template'));
    }

}