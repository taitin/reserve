<?php

namespace App\Admin\Repositories;

use App\Models\PushMessage as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class PushMessage extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
