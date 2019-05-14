<?php

namespace Corals\Modules\Marketplace\Transformers;

use Corals\Foundation\Transformers\FractalPresenter;

class BrandPresenter extends FractalPresenter
{

    /**
     * @return BrandTransformer
     */
    public function getTransformer()
    {
        return new BrandTransformer();
    }
}