<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Master extends Model
{
    use HasDateTimeFormatter;
    use SoftDeletes;

    public function getNameAttribute()
    {
        $message = Message::where('social_id', $this->social_id)->orderBy('id', 'desc')->first();
        return $message->name;
    }
}
