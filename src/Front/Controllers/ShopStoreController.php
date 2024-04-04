<?php
namespace Glumbo\Gracart\Front\Controllers;

use Glumbo\Gracart\Front\Controllers\RootFrontController;
use Glumbo\Gracart\Front\Models\ShopProduct;
use Glumbo\Gracart\Front\Models\ShopBrand;
use Glumbo\Gracart\Front\Models\ShopCategory;

class ShopStoreController extends RootFrontController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Process front shop page
     *
     * @param [type] ...$params
     * @return void
     */
    public function shopProcessFront(...$params)
    {
        if (config('app.seoLang')) {
            $lang = $params[0] ?? '';
            gc_lang_switch($lang);
        }
        return $this->_shop();
    }

    /**
     * Shop page
     * @return [view]
     */
    private function _shop()
    {
        $filter_sort = gc_request('filter_sort','','string');
        
        $products = $this->processProductList();

        gc_check_view($this->templatePath . '.screen.shop_home');
        
        return view(
            $this->templatePath . '.screen.shop_home',
            array(
                'title'       => gc_language_render('front.shop'),
                'keyword'     => gc_store('keyword'),
                'description' => gc_store('description'),
                'products'    => $products,
                'layout_page' => 'shop_home',
                'filter_sort' => $filter_sort,
                'breadcrumbs'        => [
                    ['url'           => '', 'title' => gc_language_render('front.shop')],
                ],
            )
        );
    }

    /**
     * Process front search page
     *
     * @param [type] ...$params
     * @return void
     */
    public function searchProcessFront(...$params)
    {
        if (config('app.seoLang')) {
            $lang = $params[0] ?? '';
            gc_lang_switch($lang);
        }
        return $this->_search();
    }

    /**
     * search product
     * @return [view]
     */
    private function _search()
    {
        $searchMode = config('grakan.search_mode');
        $filter_sort = gc_request('filter_sort','','string');
        $keyword = gc_request('keyword','','string');
        
        if (strtoupper($searchMode) === 'PRODUCT') {
            $products = $this->processProductList();
            $view = $this->templatePath . '.screen.shop_product_list';

            if (view()->exists($this->templatePath . '.screen.shop_search')) {
                $view = $this->templatePath . '.screen.shop_search';
            }
            gc_check_view($view);
            return view(
                $view,
                array(
                    'title'       => gc_language_render('action.search') . ': ' . $keyword,
                    'products'    => $products,
                    'layout_page' => 'shop_search',
                    'filter_sort' => $filter_sort,
                    'breadcrumbs' => [
                        ['url'    => '', 'title' => gc_language_render('action.search')],
                    ],
                )
            );

        } else {

            $view = $this->templatePath . '.screen.shop_item_list';
            if (strtoupper($searchMode) === 'CMS' && gc_config('Content') && class_exists('\App\Plugins\Cms\Content\Models\CmsContent')) {
                $itemsList = (new \App\Plugins\Cms\Content\Models\CmsContent)
                ->setLimit(gc_config('news_list'))
                ->setKeyword($keyword)
                ->setPaginate()
                ->getData();
            } else {
                //Default use NEWS
                $itemsList = (new \Glumbo\Gracart\Front\Models\ShopNews)
                ->setLimit(gc_config('news_list'))
                ->setKeyword($keyword)
                ->setPaginate()
                ->getData();
            }
            if (view()->exists($this->templatePath . '.screen.cms_search')) {
                $view = $this->templatePath . '.screen.cms_search';
            }
    
            gc_check_view($view);
    
            return view(
                $view,
                array(
                    'title'       => gc_language_render('action.search') . ': ' . $keyword,
                    'itemsList'       => $itemsList,
                    'layout_page' => 'shop_search',
                    'breadcrumbs' => [
                        ['url'    => '', 'title' => gc_language_render('action.search')],
                    ],
                )
            );
        }
        

    }

    /**
     * Process product list
     *
     * @return  [type]  [return description]
     */
    protected function processProductList() {
        $sortBy = 'sort';
        $sortOrder = 'asc';
        $arrBrandId = [];
        $categoryId = '';
        $filter_sort = gc_request('filter_sort','','string');
        $filterArr = [
            'price_desc' => ['price', 'desc'],
            'price_asc' => ['price', 'asc'],
            'sort_desc' => ['sort', 'desc'],
            'sort_asc' => ['sort', 'asc'],
            'id_desc' => ['id', 'desc'],
            'id_asc' => ['id', 'asc'],
        ];
        if (array_key_exists($filter_sort, $filterArr)) {
            $sortBy = $filterArr[$filter_sort][0];
            $sortOrder = $filterArr[$filter_sort][1];
        }
        $keyword = gc_request('keyword','', 'string');
        $cid = gc_request('cid','', 'string');
        $bid = gc_request('bid','', 'string');
        $price = gc_request('price','', 'string');
        $brand = gc_request('brand','', 'string');
        $category = gc_request('category','', 'string');
        if ($bid) {
            $arrBrandId = explode(',', $bid);
        } else {
            if ($brand) {
                $arrAliasBrand = explode(',', $brand);
                $arrBrandId = ShopBrand::whereIn('alias', $arrAliasBrand)->pluck('id')->toArray();
            }
        }

        if ($cid) {
            $categoryId = trim($cid);
        } else {
            if ($category) {
                $categoryId = ShopCategory::where('alias', $category)->first();
                if ($categoryId) {
                    $categoryId = $categoryId->id;
                }
            }
        }

        $products = (new ShopProduct);

        if ($keyword) {
            $products = $products->setKeyword($keyword);
        }
        //Filter category
        if ($categoryId) {
            $arrCate = (new ShopCategory)->getListSub($categoryId);
            $products = $products->getProductToCategory($arrCate);
        }
        //filter brand
        if ($arrBrandId) {
            $products = $products->getProductToBrand($arrBrandId);
        }
        //Filter price
        if ($price) {
            $products = $products->setRangePrice($price);
        }

        $products = $products
            ->setLimit(gc_config('product_list'))
            ->setPaginate()
            ->setSort([$sortBy, $sortOrder])
            ->getData();

        return $products;
    }
}
