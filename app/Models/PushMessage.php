<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PushMessage extends Model
{
    use HasDateTimeFormatter;
    use SoftDeletes;

    protected $table = 'push_messages';
    protected $casts = ['content' => 'json'];
    protected $guarded = ['id'];
}
