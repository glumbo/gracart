<?php
namespace Glumbo\Gracart\Front\Controllers;

use App\Http\Controllers\Controller;

class RootFrontController extends Controller
{
    public $templatePath;
    public $templateFile;
    public function __construct()
    {
        $this->templatePath = 'templates.frontend.' . gc_store('frontend_template');
        $this->templateFile = 'templates/frontend/' . gc_store('frontend_template');
    }


    /**
     * Default page not found
     *
     * @return  [type]  [return description]
     */
    public function pageNotFound()
    {
        gc_check_view( $this->templatePath . '.notfound');
        return view(
             $this->templatePath . '.notfound',
            [
            'title' => gc_language_render('front.page_not_found_title'),
            'msg' => gc_language_render('front.page_not_found'),
            'description' => '',
            'keyword' => ''
            ]
        );
    }

    /**
     * Default item not found
     *
     * @return  [view]
     */
    public function itemNotFound()
    {
        gc_check_view( $this->templatePath . '.notfound');
        return view(
             $this->templatePath . '.notfound',
            [
                'title' => gc_language_render('front.data_not_found_title'),
                'msg' => gc_language_render('front.data_not_found'),
                'description' => '',
                'keyword' => '',
            ]
        );
    }
}
