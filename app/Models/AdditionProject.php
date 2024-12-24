<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;

use Illuminate\Database\Eloquent\Model;

class AdditionProject extends Model
{
	use HasDateTimeFormatter;
    protected $table = 'addition_projects';
    public $timestamps = false;

}
