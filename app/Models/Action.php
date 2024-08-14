<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    use HasDateTimeFormatter;
    use SoftDeletes;
    protected $casts = ['params' => 'json', 'text_buttons' => 'json'];
}
