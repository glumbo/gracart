<?php
namespace Glumbo\Gracart\Front\Controllers;

use Glumbo\Gracart\Front\Controllers\RootFrontController;
use Glumbo\Gracart\Front\Models\ShopCategory;
use Glumbo\Gracart\Front\Models\ShopBrand;
use Glumbo\Gracart\Front\Models\ShopProduct;

class ShopCategoryController extends RootFrontController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Process front get category all
     *
     * @param [type] ...$params
     * @return void
     */
    public function allCategoriesProcessFront(...$params)
    {
        if (config('app.seoLang')) {
            $lang = $params[0] ?? '';
            gc_lang_switch($lang);
        }
        return $this->_allCategories();
    }

    /**
     * display list category root (parent = 0)
     * @return [view]
     */
    private function _allCategories()
    {
        $sortBy = 'sort';
        $sortOrder = 'asc';
        $filter_sort = gc_request('filter_sort','','string');
        $filterArr = [
            'sort_desc' => ['sort', 'desc'],
            'sort_asc' => ['sort', 'asc'],
            'id_desc' => ['id', 'desc'],
            'id_asc' => ['id', 'asc'],
        ];
        if (array_key_exists($filter_sort, $filterArr)) {
            $sortBy = $filterArr[$filter_sort][0];
            $sortOrder = $filterArr[$filter_sort][1];
        }

        $itemsList = (new ShopCategory)
            ->getCategoryRoot()
            ->setSort([$sortBy, $sortOrder])
            ->setPaginate()
            ->setLimit(gc_config('item_list'))
            ->getData();

        gc_check_view($this->templatePath . '.screen.shop_item_list');
        return view(
            $this->templatePath . '.screen.shop_item_list',
            array(
                'title'       => gc_language_render('front.categories'),
                'itemsList'   => $itemsList,
                'keyword'     => '',
                'description' => '',
                'layout_page' => 'shop_item_list',
                'filter_sort' => $filter_sort,
                'breadcrumbs' => [
                    ['url'    => '', 'title' => gc_language_render('front.categories')],
                ],
            )
        );
    }

    /**
     * Process front get category detail
     *
     * @param [type] ...$params
     * @return void
     */
    public function categoryDetailProcessFront(...$params)
    {
        if (config('app.seoLang')) {
            $lang = $params[0] ?? '';
            $alias = $params[1] ?? '';
            gc_lang_switch($lang);
        } else {
            $alias = $params[0] ?? '';
        }
        return $this->_categoryDetail($alias);
    }


    /**
     * Category detail: list category child + product list
     * @param  [string] $alias
     * @return [view]
     */
    private function _categoryDetail($alias)
    {
        $sortBy = 'sort';
        $sortOrder = 'asc';
        $arrBrandId = [];
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
        $bid = gc_request('bid','', 'string');
        $price = gc_request('price','', 'string');
        $brand = gc_request('brand','', 'string');

        if ($bid) {
            $arrBrandId = explode(',', $bid);
        } else {
            if ($brand) {
                $arrAliasBrand = explode(',', $brand);
                $arrBrandId = ShopBrand::whereIn('alias', $arrAliasBrand)->pluck('id')->toArray();
            }
        }

        $category = (new ShopCategory)->getDetail($alias, $type = 'alias');

        if ($category) {
            
            $products = (new ShopProduct);

            if ($keyword) {
                $products = $products->setKeyword($keyword);
            }
            //Filter category
            $arrCate = (new ShopCategory)->getListSub($category->id);
            $products = $products->getProductToCategory($arrCate);

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

            $subCategory = (new ShopCategory)
                ->setParent($category->id)
                ->setLimit(gc_config('item_list'))
                ->setPaginate()
                ->getData();

            gc_check_view($this->templatePath . '.screen.shop_product_list');
            return view(
                $this->templatePath . '.screen.shop_product_list',
                array(
                    'title'       => $category->title,
                    'categoryId'  => $category->id,
                    'description' => $category->description,
                    'keyword'     => $category->keyword,
                    'products'    => $products,
                    'category'    => $category,
                    'subCategory' => $subCategory,
                    'layout_page' => 'shop_product_list',
                    'og_image'    => gc_file($category->getImage()),
                    'filter_sort' => $filter_sort,
                    'breadcrumbs' => [
                        ['url'    => gc_route('category.all'), 'title' => gc_language_render('front.categories')],
                        ['url'    => '', 'title' => $category->title],
                    ],
                )
            );
        } else {
            return $this->itemNotFound();
        }
    }
}
