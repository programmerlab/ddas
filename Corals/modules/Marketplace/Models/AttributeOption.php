<?php

namespace Corals\Modules\Marketplace\Models;

use Corals\Foundation\Models\BaseModel;
use Corals\Foundation\Transformers\PresentableTrait;
use Corals\Traits\Node\SimpleNode;
use Spatie\Activitylog\Traits\LogsActivity;

class AttributeOption extends BaseModel
{
    use PresentableTrait, LogsActivity, SimpleNode;

    public $timestamps = false;

    protected $table = 'marketplace_attribute_options';


    /**
     *  Model configuration.
     * @var string
     */
    public $config = 'marketplace.models.attribute_option';


    protected $guarded = [];

    public function attribute()
    {
        return $this->belongsToMany(Attribute::class);
    }

}
