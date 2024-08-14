<?php

namespace App\Models;

use App\Services\LineService;
use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Message extends Model
{
    use HasDateTimeFormatter;
    use SoftDeletes;

    protected $guarded = ['id'];


    public function reply($message)
    {
        $line = new LineService();
        $this->replied_at = now();
        $this->save();
        return  $line->replyMessage($this->reply_token, $message);
    }
}
