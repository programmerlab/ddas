<?php

namespace Corals\Modules\Marketplace\Transformers;

use Corals\Foundation\Transformers\FractalPresenter;

class ShippingPresenter extends FractalPresenter
{

    /**
     * @return ShippingTransformer
     */
    public function getTransformer()
    {
        return new ShippingTransformer();
    }
}