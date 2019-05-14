<?php

namespace Corals\Modules\Marketplace\Http\Controllers;

use Corals\Foundation\Http\Controllers\BaseController;
use Corals\Modules\Marketplace\DataTables\StoresDataTable;
use Corals\Modules\Marketplace\Http\Requests\EnrollRequest;
use Corals\Modules\Marketplace\Http\Requests\StoreRequest;
use Corals\Modules\Marketplace\Models\Store;
use Corals\Modules\Subscriptions\Models\Product;
use Illuminate\Http\Request;

class StoresController extends BaseController
{
    protected $excludedRequestParams = ['thumbnail', 'cover_photo', 'clear_cover_photo', 'clear_logo'];

    public function __construct()
    {
        $this->resource_url = config('marketplace.models.store.resource_url');

        $this->title = 'Marketplace::module.store.title';
        $this->title_singular = 'Marketplace::module.store.title_singular';

        parent::__construct();
    }

    /**
     * @param StoreRequest $request
     * @param StoresDataTable $dataTable
     * @return mixed
     */
    public function index(StoreRequest $request, StoresDataTable $dataTable)
    {
        return $dataTable->render('Marketplace::stores.index');
    }

    /**
     * @param StoreRequest $request
     * @return $this
     */
    public function create(StoreRequest $request)
    {
        $store = new Store();

        $this->setViewSharedData(['title_singular' => trans('Corals::labels.create_title', ['title' => $this->title_singular])]);

        return view('Marketplace::stores.create_edit')->with(compact('store'));
    }

    /**
     * @param StoreRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(StoreRequest $request)
    {
        try {
            $data = $request->except($this->excludedRequestParams);

            $store = Store::create($data);

            flash(trans('Corals::messages.success.created', ['item' => $this->title_singular]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, Store::class, 'store');
        }

        return redirectTo($this->resource_url);
    }

    /**
     * @param StoreRequest $request
     * @param Store $store
     * @return Store
     */
    public function show(StoreRequest $request, Store $store)
    {
        $this->setViewSharedData(['title_singular' => trans('Corals::labels.show_title', ['title' => $store->getIdentifier()])]);

        $this->setViewSharedData(['edit_url' => $this->resource_url . '/' . $store->hashed_id . '/edit']);

        return view('Marketplace::stores.show')->with(compact('store'));
    }

    /**
     * @param StoreRequest $request
     * @param Store $store
     * @return $this
     */
    public function edit(StoreRequest $request, Store $store)
    {
        $this->setViewSharedData(['title_singular' => trans('Corals::labels.update_title', ['title' => $store->getIdentifier()])]);

        return view('Marketplace::stores.create_edit')->with(compact('store'));
    }

    /**
     * @param StoreRequest $request
     * @param Store $store
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(StoreRequest $request, Store $store)
    {
        try {
            $data = $request->except($this->excludedRequestParams);

            if (!\Store::isStoreAdmin()) {

                $data['is_featured'] = array_get($data, 'is_featured', false);
            }
            $store->update($data);

            if ($request->has('clear_logo') || $request->hasFile('thumbnail')) {
                $store->clearMediaCollection($store->mediaCollectionName);
            }

            if ($request->has('clear_cover_photo') || $request->hasFile('cover_photo')) {
                $store->clearMediaCollection($store->coverPhotoMediaCollectionName);
            }

            if ($request->hasFile('thumbnail')) {
                $store->addMedia($request->file('thumbnail'))
                    ->withCustomProperties(['root' => 'user_' . user()->hashed_id])
                    ->toMediaCollection($store->mediaCollectionName);
            }

            if ($request->has('clear_cover') || $request->hasFile('cover_photo')) {
                $store->clearMediaCollection($store->coverPhotoMediaCollectionName);
            }

            if ($request->hasFile('cover_photo')) {
                $store->addMedia($request->file('cover_photo'))
                    ->withCustomProperties(['root' => 'user_' . user()->hashed_id])
                    ->toMediaCollection($store->coverPhotoMediaCollectionName);
            }

            flash(trans('Corals::messages.success.updated', ['item' => $this->title_singular]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, Store::class, 'update');
        }
        return redirectTo(\URL::previous());

    }

    /**
     * @param StoreRequest $request
     * @param Store $store
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(StoreRequest $request, Store $store)
    {
        try {
            $store->delete();

            $message = ['level' => 'success', 'message' => trans('Corals::messages.success.deleted', ['item' => $this->title_singular])];
        } catch (\Exception $exception) {
            log_exception($exception, Store::class, 'destroy');
            $message = ['level' => 'error', 'message' => $exception->getMessage()];
        }

        return response()->json($message);
    }


    public function enroll(Request $request)
    {
        try {
            $subscription_required = \Settings::get('marketplace_general_vendor_require_subscription', false);
            if ($subscription_required) {
                $marketplace_subscription_product = \Settings::get('marketplace_general_subscription_product', '');
                if ($marketplace_subscription_product) {
                    $product = Product::find($marketplace_subscription_product);
                    if ($product) {
                        return redirectTo('subscriptions/products/' . $product->hashed_id);
                    }

                }
                flash(trans('Marketplace::exception.store.invalid_subscription_product'))->error();
                return redirectTo('dashboard');

            } else {
                $this->setViewSharedData(['title_singular' => trans('Marketplace::labels.store.enroll')]);

                return view('Marketplace::stores.enroll');
            }
        } catch (\Exception $exception) {
            log_exception($exception, Store::class, 'update');
        }

    }

    public function doEnroll(EnrollRequest $request)
    {
        try {

            \Store::createStore(user());
            flash(trans('Marketplace::messages.vendor.success.enroll'))->success();

        } catch (\Exception $exception) {

            log_exception($exception, Store::class, 'Enroll');
        }

        return redirectTo('marketplace/store/settings');


    }


    public function settings(Request $request)
    {

        $this->setViewSharedData(['title_singular' => 'Store Settings']);

        $settings = config('marketplace.store_settings');

        $store = \Store::getVendorStore();

        return view('Marketplace::stores.settings')->with(compact('settings', 'store'));
    }

    public function saveSettings(Request $request)
    {
        try {

            $settings = $request->except('_token');
            $store = \Store::getVendorStore();

            foreach ($settings as $key => $value) {
                list($setting_key, $cast) = explode('|', $key);
                $store->updateSetting($setting_key, $value, $cast);
            }

            flash(trans('Corals::messages.success.saved', ['item' => $this->title_singular]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, 'marketplaceSettings', 'savedSettings');
        }

        return redirectTo('marketplace/store/settings');
    }

    /**
     * Set locale if it's allowed.
     * @param Request $request
     * @param $locale
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setStore(Request $request, $store)
    {
        session()->put('current_store', $store);
        return redirect()->back();
    }
}