<?php

namespace App\Admin\Repositories;

use App\Models\Wash as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Wash extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
