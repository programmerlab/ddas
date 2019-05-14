<?php

namespace Corals\Modules\Marketplace\DataTables;

use Corals\Foundation\DataTables\BaseDataTable;
use Corals\Modules\Marketplace\Models\Store;
use Corals\Modules\Marketplace\Transformers\StoreTransformer;
use Yajra\DataTables\EloquentDataTable;

class StoresDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $this->setResourceUrl(config('marketplace.models.store.resource_url'));

        $dataTable = new EloquentDataTable($query);

        return $dataTable->setTransformer(new StoreTransformer());
    }

    /**
     * Get query source of dataTable.
     * @param Store $model
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function query(Store $model)
    {
        return $model->newQuery();
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            'id' => ['visible' => true],
            'name' => ['title' => trans('Marketplace::attributes.store.name')],
            'slug' => ['title' => trans('Marketplace::attributes.store.slug')],
            'status' => ['title' => trans('Corals::attributes.status'), 'sorting' => false],
            //'parking_domain' => ['title' => trans('Marketplace::attributes.store.parking_domain')],
            'is_featured' => ['title' => trans('Marketplace::attributes.store.is_featured')],
            'user_id' => ['title' => trans('Marketplace::attributes.store.owner')],
            'created_at' => ['title' => trans('Corals::attributes.created_at')],
            'updated_at' => ['title' => trans('Corals::attributes.updated_at')],
        ];
    }
}
