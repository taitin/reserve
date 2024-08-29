<?php

namespace App\Admin\Repositories;

use App\Models\Except as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Except extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
