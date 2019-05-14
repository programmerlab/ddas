<?php

namespace Corals\Modules\Marketplace\Http\Controllers;

use Corals\Foundation\DataTables\CoralsBuilder;
use Corals\Foundation\Http\Controllers\BaseController;
use Corals\Modules\Marketplace\Contracts\ShippingContract;
use Corals\Modules\Marketplace\DataTables\MyOrdersDataTable;
use Corals\Modules\Marketplace\DataTables\MyPrivatePagesDataTable;
use Corals\Modules\Marketplace\DataTables\MyStoreOrdersDataTable;
use Corals\Modules\Marketplace\DataTables\OrdersDataTable;
use Corals\Modules\Marketplace\Http\Requests\ProductRequest;
use Corals\Modules\Marketplace\Models\Order;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\Media;

class OrdersController extends BaseController
{

    protected $shipping;

    public function __construct()
    {
        $this->resource_url = config('marketplace.models.order.resource_url');
        $this->title = 'Marketplace::module.order.title';
        $this->title_singular = 'Marketplace::module.order.title_singular';

        $this->setViewSharedData(['hideCreate' => true]);

        parent::__construct();
    }

    protected function canAccess($order)
    {
        $canAccess = false;

        if (user()->hasPermissionTo('Marketplace::my_orders.access') && $order->user->id == user()->id) {
            $canAccess = true;
        } elseif (user()->hasPermissionTo('Marketplace::store_orders.access') && $order->store->user->id == user()->id) {
            $canAccess = true;
        } elseif (user()->hasPermissionTo('Marketplace::order.view')) {
            $canAccess = true;
        }

        if (!$canAccess) {
            abort(403);
        }
    }

    /**
     * @param Request $request
     * @param OrdersDataTable $dataTable
     * @return mixed
     */
    public function index(Request $request, OrdersDataTable $dataTable)
    {
        if (!user()->hasPermissionTo('Marketplace::order.view')) {
            abort(403);
        }

        return $dataTable->render('Marketplace::orders.index');
    }

    /**
     * @param Request $request
     * @param Order $order
     * @return $this
     */
    public function edit(Request $request, Order $order)
    {
        if (!user()->hasPermissionTo('Marketplace::order.update')) {
            abort(403);
        }

        $order_statuses = trans(config('marketplace.models.order.statuses'));
        $shippment_statuses = trans(config('marketplace.models.order.shippment_statuses'));
        $payment_statuses = trans(config('marketplace.models.order.payment_statuses'));

        $this->setViewSharedData(['title_singular' => trans('Marketplace::module.order.update')]);

        return view('Marketplace::orders.edit')->with(compact('order', 'order_statuses', 'shippment_statuses', 'payment_statuses'));
    }


    /**
     * @param Request $request
     * @param Order $order
     * @return $this
     */
    public function update(Request $request, Order $order)
    {
        if (!user()->hasPermissionTo('Marketplace::order.update')) {
            abort(403);
        }

        $this->validate($request, ['status' => 'required']);

        try {
            $data = $request->all();

            $shipping = $order->shipping ?? [];

            if ($request->has('shipping')) {
                $shipping = array_replace_recursive($shipping, $data['shipping']);
            }
            $billing = $order->billing ?? [];

            if (user()->hasPermissionTo('Marketplace::order.update_payment_details')) {
                if ($request->has('billing')) {
                    $billing = array_replace_recursive($billing, $data['billing']);
                }
            }

            $order->update([
                'status' => $data['status'],
                'shipping' => $shipping,
                'billing' => $billing,
            ]);

            if ($request->has('notify_buyer')) {
                event('notifications.marketplace.order.updated', ['order' => $order]);

            }
            $message = ['level' => 'success', 'message' => trans('Corals::messages.success.updated', ['item' => $this->title_singular])];

            flash(trans('Corals::messages.success.updated', ['item' => $this->title_singular]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, Order::class, 'update');
            $message = ['level' => 'error', 'message' => $exception->getMessage()];
        }

        return response()->json($message);
    }

    /**
     * @param Request $request
     * @param MyOrdersDataTable $dataTable
     * @return mixed
     */
    public function myOrders(Request $request, MyOrdersDataTable $dataTable)
    {
        if (!user()->hasPermissionTo('Marketplace::my_orders.access')) {
            abort(403);
        }

        return $dataTable->render('Marketplace::orders.index');
    }

    /**
     * @param Request $request
     * @param MyOrdersDataTable $dataTable
     * @return mixed
     */
    public function storeOrders(Request $request, MyStoreOrdersDataTable $dataTable)
    {
        if (!user()->hasPermissionTo('Marketplace::store_orders.access')) {
            abort(403);
        }

        return $dataTable->render('Marketplace::orders.index');
    }

    /**
     * @param Request $request
     * @param MyOrdersDataTable $dataTable
     * @return mixed
     */
    public function myPrivatePages(Request $request, MyPrivatePagesDataTable $dataTable)
    {
        if (!user()->hasPermissionTo('Marketplace::my_orders.access')) {
            abort(403);
        }

        return $dataTable->render('Marketplace::orders.private_pages');
    }


    /**
     * @param Request $request
     * @return mixed
     */
    public function myDownloads(Request $request)
    {
        CoralsBuilder::DataTableScripts();

        if (!user()->hasPermissionTo('Marketplace::my_orders.access')) {
            abort(403);
        }

        $orders = Order::myOrders()->get();

        return view('Marketplace::orders.downloads')->with(compact('orders'));
    }


    /**
     * @param Request $request
     * @param Order $order
     * @return $this
     */
    public function show(Request $request, Order $order)
    {
        $this->canAccess($order);

        return view('Marketplace::orders.show')->with(compact('order'));

    }


    public function downloadFile(Request $request, Order $order, $hashed_id)
    {
        $this->canAccess($order);

        $id = hashids_decode($hashed_id);

        $media = Media::findOrfail($id);

        return response()->download(storage_path($media->getUrl()));
    }

    /**
     * @param Request $request
     * @param Order $order
     * @return $this
     */
    public function track(Request $request, Order $order)
    {
        if (user()->hasPermissionTo('Marketplace::order.view') || user()->hasPermissionTo('Marketplace::my_orders.access')) {
            try {
                $tracking = \Shipping::track($order);
                return view('Marketplace::orders.track')->with(compact('order', 'tracking'));
            } catch
            (\Exception $exception) {
                log_exception($exception, 'OrderController', 'Track');
            }
        }

        abort(403);
    }

}
