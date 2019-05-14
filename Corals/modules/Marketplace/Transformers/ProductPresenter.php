<?php

namespace Corals\Modules\Marketplace\Transformers;

use Corals\Foundation\Transformers\FractalPresenter;

class ProductPresenter extends FractalPresenter
{

    /**
     * @return ProductTransformer
     */
    public function getTransformer()
    {
        return new ProductTransformer();
    }
}