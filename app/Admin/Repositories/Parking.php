<?php

namespace App\Admin\Repositories;

use App\Models\Parking as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Parking extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
