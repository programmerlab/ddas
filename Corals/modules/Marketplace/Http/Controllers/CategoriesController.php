<?php

namespace Corals\Modules\Marketplace\Http\Controllers;

use Corals\Foundation\Http\Controllers\BaseController;
use Corals\Modules\Marketplace\DataTables\CategoriesDataTable;
use Corals\Modules\Marketplace\Http\Requests\CategoryRequest;
use Corals\Modules\Marketplace\Models\Category;

class CategoriesController extends BaseController
{
    public function __construct()
    {
        $this->resource_url = config('marketplace.models.category.resource_url');
        $this->title = 'Marketplace::module.category.title';
        $this->title_singular = 'Marketplace::module.category.title_singular';

        parent::__construct();
    }

    /**
     * @param CategoryRequest $request
     * @param CategoriesDataTable $dataTable
     * @return mixed
     */
    public function index(CategoryRequest $request, CategoriesDataTable $dataTable)
    {
        return $dataTable->render('Marketplace::categories.index');
    }

    /**
     * @param CategoryRequest $request
     * @return $this
     */
    public function create(CategoryRequest $request)
    {
        $category = new Category();

        $this->setViewSharedData(['title_singular' => trans('Corals::labels.create_title', ['title' => $this->title_singular])]);

        return view('Marketplace::categories.create_edit')->with(compact('category'));
    }

    /**
     * @param CategoryRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(CategoryRequest $request)
    {
        try {
            $data = $request->except('thumbnail', 'category_attributes');

            $category = Category::create($data);

            if ($request->hasFile('thumbnail')) {
                $category->addMedia($request->file('thumbnail'))
                    ->withCustomProperties(['root' => 'user_' . user()->hashed_id])
                    ->toMediaCollection($category->mediaCollectionName);
            }
            $category->categoryAttributes()->sync($request->get('category_attributes', []));

            flash(trans('Corals::messages.success.created', ['item' => $this->title_singular]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, Category::class, 'store');
        }

        return redirectTo($this->resource_url);
    }

    /**
     * @param CategoryRequest $request
     * @param Category $category
     * @return Category
     */
    public function show(CategoryRequest $request, Category $category)
    {
        return $category;
    }

    /**
     * @param CategoryRequest $request
     * @param Category $category
     * @return $this
     */
    public function edit(CategoryRequest $request, Category $category)
    {
        $this->setViewSharedData(['title_singular' => trans('Corals::labels.update_title', ['title' => $category->name])]);

        return view('Marketplace::categories.create_edit')->with(compact('category'));
    }

    /**
     * @param CategoryRequest $request
     * @param Category $category
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(CategoryRequest $request, Category $category)
    {
        try {
            $data = $request->except('thumbnail', 'clear', 'category_attributes');
            $data['is_featured'] = array_get($data, 'is_featured', false);

            $category->update($data);

            if ($request->has('clear') || $request->hasFile('thumbnail')) {
                $category->clearMediaCollection($category->mediaCollectionName);
            }
            $category->categoryAttributes()->sync($request->get('category_attributes', []));

            if ($request->hasFile('thumbnail') && !$request->has('clear')) {
                $category->addMedia($request->file('thumbnail'))
                    ->withCustomProperties(['root' => 'user_' . user()->hashed_id])
                    ->toMediaCollection($category->mediaCollectionName);
            }

            flash(trans('Corals::messages.success.updated', ['item' => $this->title_singular]))->success();
        } catch (\Exception $exception) {
            log_exception($exception, Category::class, 'update');
        }

        return redirectTo($this->resource_url);
    }

    /**
     * @param CategoryRequest $request
     * @param Category $category
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(CategoryRequest $request, Category $category)
    {
        try {
            $category->clearMediaCollection($category->mediaCollectionName);
            $category->delete();

            $message = ['level' => 'success', 'message' => trans('Corals::messages.success.deleted', ['item' => $this->title_singular])];
        } catch (\Exception $exception) {
            log_exception($exception, Category::class, 'destroy');
            $message = ['level' => 'error', 'message' => $exception->getMessage()];
        }

        return response()->json($message);
    }

    public function getCategoryAttributes(AttributeRequest $request, $modelId = null)
    {

        $categories_ids = request()->get('categories_ids', "[]");
        $categories_ids = json_decode(urldecode($categories_ids));
        $modelClass = $request->get('model_class', []);

        if (!is_array($categories_ids)) {
            return '';
        }

        $instance = null;


        $categories = Category::query()->whereIn('id', $categories_ids)->get();

        if (!is_null($modelId) && class_exists($modelClass)) {
            $instance = $modelClass::findByHash($modelId);
        }

        $fields = collect([]);

        foreach ($categories as $category) {
            if ($category->parent_id) {
                $fields = $fields->merge($category->parent->categoryAttributes);
            }
            $fields = $fields->merge($category->categoryAttributes);
        }

        $fields = $fields->unique('id');


        $input = '';
        foreach ($fields as $field) {
            $input .= \Category::renderAttribute($field, $instance, []);
        }

        return response()->json($input);
    }
}