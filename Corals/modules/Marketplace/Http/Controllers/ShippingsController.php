<?php

namespace Corals\Modules\Marketplace\Http\Controllers;

use Carbon\Carbon;
use Corals\Foundation\Http\Controllers\BaseController;
use Corals\Modules\Marketplace\DataTables\ShippingsDataTable;
use Corals\Modules\Marketplace\Http\Requests\ShippingRequest;
use Corals\Modules\Marketplace\Models\Shipping;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class ShippingsController extends BaseController
{


    public function __construct()
    {


        $this->resource_url = config('marketplace.models.shipping.resource_url');
        $this->title = 'Marketplace::module.shipping.title';
        $this->title_singular = 'Marketplace::module.shipping.title_singular';
        parent::__construct();
    }

    /**
     * @param ShippingRequest $request
     * @param ShippingsDataTable $dataTable
     * @return mixed
     */
    public function index(ShippingRequest $request, ShippingsDataTable $dataTable)
    {
        return $dataTable->render('Marketplace::shippings.index');
    }

    /**
     * @param ShippingRequest $request
     * @return $this
     */
    public function create(ShippingRequest $request)
    {
        $shipping = new Shipping();

        $this->setViewSharedData(['title_singular' => trans('Corals::labels.create_title', ['title' => $this->title_singular])]);

        return view('Marketplace::shippings.create_edit')->with(compact('shipping'));
    }

    /**
     * @param ShippingRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(ShippingRequest $request)
    {
        try {
            $data = $request->except('users', 'products');

            if (!\Store::isStoreAdmin()) {
                $store = \Store::getVendorStore();
                if (!$store) {
                    $validator = \Validator::make([], []); // Empty data and rules fields
                    $validator->errors()->add('store_id', trans('Marketplace::exception.store.invalid_store'));
                    return response()->json(['message' => trans('validation.message'), 'errors' => $validator->getMessageBag()], 422);

                }
                $data['store_id'] = $store->id;
            }

            Shipping::create($data);

            flash(trans('Corals::messages.success.created', ['item' => $this->title_singular]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, Shipping::class, 'store');
        }

        return redirectTo($this->resource_url);
    }

    /**
     * @param ShippingRequest $request
     * @param Shipping $shipping
     * @return Shipping
     */
    public function show(ShippingRequest $request, Shipping $shipping)
    {
        return $shipping;
    }

    /**
     * @param ShippingRequest $request
     * @param Shipping $shipping
     * @return $this
     */
    public function edit(ShippingRequest $request, Shipping $shipping)
    {
        $this->setViewSharedData(['title_singular' => trans('Corals::labels.update_title', ['title' => $this->title_singular])]);

        return view('Marketplace::shippings.create_edit')->with(compact('shipping'));
    }

    /**
     * @param ShippingRequest $request
     * @param Shipping $shipping
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(ShippingRequest $request, Shipping $shipping)
    {


        try {
            $data = $request->except('users', 'products');

            $shipping->update($data);

            flash(trans('Corals::messages.success.updated', ['item' => trans('Marketplace::module.shipping.index_title')]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, Shipping::class, 'update');
        }

        return redirectTo($this->resource_url);
    }

    /**
     * @param ShippingRequest $request
     * @param Shipping $shipping
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ShippingRequest $request, Shipping $shipping)
    {
        try {
            $shipping->delete();

            $message = ['level' => 'success', 'message' => trans('Corals::messages.success.deleted', ['item' => trans('Marketplace::module.shipping.index_title')])];
        } catch (\Exception $exception) {
            log_exception($exception, Shipping::class, 'destroy');
            $message = ['level' => 'error', 'message' => $exception->getMessage()];
        }

        return response()->json($message);
    }

    /**
     * @return $this
     */
    public function upload()
    {

        if (!user()->hasPermissionTo('Marketplace::shipping.upload')) {
            abort(403);
        }

        $this->setViewSharedData(['title_singular' => trans('Corals::labels.upload_title', ['title' => $this->title_singular])]);

        return view('Marketplace::shippings.upload');
    }


    /**
     * @param Request $request
     * @param Shipping $shipping
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function doUpload(Request $request)
    {

        if (!user()->hasPermissionTo('Marketplace::shipping.upload')) {
            abort(403);
        }

        $wrongCounter = 0;
        $successCounter = 0;
        $store = null;
        if (!\Store::isStoreAdmin()) {
            $store = \Store::getVendorStore();
            if (!$store) {
                $validator = \Validator::make([], []); // Empty data and rules fields
                $validator->errors()->add('store_id', trans('Marketplace::exception.store.invalid_store'));
                return response()->json(['message' => trans('validation.message'), 'errors' => $validator->getMessageBag()], 422);

            }
        }


        try {
            Excel::load($request->file('shipping_import_file'), function ($reader) use (&$wrongCounter, &$successCounter, $store) {
                $wrongData = [];
                if ($store) {
                    Shipping::where('store_id', $store->id)->delete();

                } else {
                    Shipping::whereNull('store_id')->delete();
                }
                foreach ($reader->toArray() as $row) {


                    $validator = Validator::make($row, [
                        'name' => 'required',
                        'shipping_method' => 'required',
                        'priority' => 'required|numeric',
                        'rate' => 'numeric|required_if:shipping_method,FlatRate',
                    ]);

                    if ($validator->fails()) {
                        $errors = $validator->errors()->all();

                        $row['errors'] = '[' . implode(", ", $errors) . ']';

                        $wrongData[] = $row;

                        $wrongCounter++;
                    } else {
                        $row['min_order_total'] = $row['min_order_total'] ?? 0.0;
                        $row['store_id'] = $store ? $store->id : null;

                        Shipping::create($row);

                        $successCounter++;
                    }
                }

                if (count($wrongData) > 0) {
                    $reportFileName = 'shipping_rules_errors_' . Carbon::now()->format('Y-m-d_h-m-s');

                    \Excel::create($reportFileName, function ($excel) use ($wrongData) {
                        $excel->sheet('sheet', function ($sheet) use ($wrongData) {
                            $sheet->setOrientation('landscape');
                            $sheet->fromArray($wrongData);
                        });
                    })->store('xls', storage_path('errors'));

                    session()->put('shipping-rules-report', storage_path('errors/' . $reportFileName . '.xls'));
                }
            });

            flash(trans('Marketplace::messages.shipping.success.import',
                ['successCount' => $successCounter, 'wrongCount' => $wrongCounter]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, Shipping::class, 'importShipping');
        }
        return redirectTo($this->resource_url);
    }

    public function importShippingReport($action)
    {

        if (!user()->hasPermissionTo('Marketplace::shipping.upload')) {
            abort(403);
        }
        switch ($action) {
            case 'download':
                $file = session('shipping-rules-report');

                if (\File::exists($file)) {
                    return response()->download($file);
                }

                flash(trans('Marketplace::exception.shipping.no_report_file'))->warning();

                return redirectTo($this->resource_url);
                break;
            case 'clear':
                @unlink(session('shipping-rules-report'));
                session()->forget('shipping-rules-report');
                return redirectTo($this->resource_url);
                break;

        }
    }
}