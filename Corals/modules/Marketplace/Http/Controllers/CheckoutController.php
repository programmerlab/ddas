<?php

namespace Corals\Modules\Marketplace\Http\Controllers;

use Corals\Foundation\Http\Controllers\BaseController;
use Corals\Modules\Marketplace\Traits\CheckoutControllerCommonFunctions;
use Illuminate\Http\Request;

class CheckoutController extends BaseController
{
    use CheckoutControllerCommonFunctions;

    public $urlPrefix = 'marketplace/';

    /**
     * CartController constructor.
     */

    public function __construct()
    {
        $this->title = 'Marketplace::module.checkout.title';
        $this->title_singular = 'Marketplace::module.checkout.title_singular';
        $this->setViewSharedData(['urlPrefix' => $this->urlPrefix]);
        parent::__construct();
    }

    /**
     * @return mixed
     */
    private function canAccessCheckout()
    {
        if (!user()->hasPermissionTo('Marketplace::checkout.access')) {
            abort(403);
        }
    }

    public function index(Request $request)
    {
        $cart_items = \ShoppingCart::getItems();

        if (sizeof($cart_items) == 0) {
            return redirectTo('cart');
        }

        $enable_shipping = false;

        if (\Shipping::hasShippableItems($cart_items)) {
            $enable_shipping = true;
        }

        \ShoppingCart::get('default')->setAttribute('enable_shipping', $enable_shipping);

        $this->canAccessCheckout();

        \Assets::add(asset('assets/corals/plugins/smartwizard/css/smart_wizard.min.css'));
        \Assets::add(asset('assets/corals/plugins/smartwizard/css/smart_wizard_theme_arrows.css'));
        \Assets::add(asset('assets/corals/plugins/smartwizard/js/jquery.smartWizard.min.js'));

        $this->setViewSharedData(['title', 'Checkout']);

        return view('Marketplace::checkout.checkout')->with(compact('enable_shipping'));
    }

    public function showOrderSuccessPage()
    {
        $this->setViewSharedData(['title', 'Congratulations !']);
        return view('Marketplace::orders.order-success');
    }

}