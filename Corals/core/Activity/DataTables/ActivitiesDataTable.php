<?php

namespace Corals\Activity\DataTables;

use Corals\Activity\Models\Activity;
use Corals\Activity\Transformers\ActivityTransformer;
use Corals\Foundation\DataTables\BaseDataTable;
use Yajra\DataTables\EloquentDataTable;

class ActivitiesDataTable extends BaseDataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $this->setResourceUrl(config('activity.models.activity.resource_url'));

        $dataTable = new EloquentDataTable($query);

        return $dataTable->setTransformer(new ActivityTransformer());
    }

    /**
     * @param Activity $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Activity $model)
    {
        return $model->with('causer')->select('activity_log.*');
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            'id' => ['visible' => false],
            'log_name' => ['title' => trans('Activity::attributes.activity.log_name')],
            'subject_type' => ['title' => trans('Activity::attributes.activity.subject_type')],
            'subject_id' => ['title' => trans('Activity::attributes.activity.subject_id')],
            'causer_id' => ['title' => trans('Activity::attributes.activity.causer_id')],
            'description' => ['title' => trans('Activity::attributes.activity.description')],
            'properties' => ['title' => trans('Activity::attributes.activity.properties')],
            'created_at' => ['title' => trans('Corals::attributes.created_at')],
            'updated_at' => ['title' => trans('Corals::attributes.updated_at')],
        ];
    }
}
