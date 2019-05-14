<?php

namespace Corals\Modules\Marketplace\Http\Controllers;

use Corals\Modules\CMS\Traits\SEOTools;
use Corals\Foundation\Http\Controllers\PublicBaseController;
use Corals\Modules\Marketplace\Facades\Shop;
use Corals\Modules\Marketplace\Models\Product;
use Corals\Modules\Marketplace\Models\Store;
use Illuminate\Http\Request;

class ShopPublicController extends PublicBaseController
{
    use SEOTools;

    /**
     * @param Request $request
     * @return $this
     */
    public function index(Request $request)
    {
        $item = [
            'title' => 'Shop',
            'meta_description' => 'Marketplace Shop',
            'url' => url('shop'),
            'type' => 'shop',
            'image' => \Settings::get('site_logo'),
            'meta_keywords' => 'shop,marketplace,products'
        ];
        $this->setSEO((object)$item);

        $result = $this->showPorducts($request);
        return view('templates.shop')->with($result);
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function showStore(Request $request, $slug)
    {
        $store = Store::where('slug', $slug)->first();
        if (!$store) {
            abort(404);
        }

        $item = [
            'title' => 'Shop',
            'meta_description' => 'Marketplace Shop',
            'url' => url('shop'),
            'type' => 'shop',
            'image' => \Settings::get('site_logo'),
            'meta_keywords' => 'shop,marketplace,products'
        ];
        $this->setSEO((object)$item);

        $result = $this->showPorducts($request, $store);
        $result['store'] = $store;
        return view('templates.store')->with($result);
    }


    private function showPorducts($request, $store = null)
    {
        $layout = $request->get('layout', 'grid');

        $products = Shop::getProducts($request, $store);


        $shopText = null;

        if ($request->has('search')) {
            $shopText = trans('Marketplace::labels.shop.search_results_for', ['search' => $request->get('search')]);
        }

        $sortOptions = trans(config('marketplace.models.shop.sort_options'));


        if (\Settings::get('marketplace_rating_enable') == "true") {
            $sortOptions['average_rating'] = trans('Marketplace::attributes.product.average_rating');
        }

        return compact('layout', 'products', 'shopText', 'sortOptions');
    }

    public function show(Request $request, $slug)
    {

        $product = Product::where('slug', $slug)->first();
        if (!$product) {
            abort(404);

        }
        $categories = join(',', $product->activeCategories->pluck('name')->toArray());
        $tags = join(',', $product->activeTags->pluck('name')->toArray());

        $item = [
            'title' => $product->name,
            'meta_description' => str_limit(strip_tags($product->description), 500),
            'url' => url('shop/' . $product->slug),
            'type' => 'product',
            'image' => $product->image,
            'meta_keywords' => $categories . ',' . $tags
        ];

        $this->setSEO((object)$item);

        return view('templates/product_single')->with(compact('product'));
    }

}