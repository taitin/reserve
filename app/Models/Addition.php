<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Addition extends Model
{
    use HasDateTimeFormatter;
    use SoftDeletes;
    protected $casts = [
        'price' => 'json',
        'discount_price' => 'json'
    ];
}
