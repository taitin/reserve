<?php

namespace App\Models;

use App\Services\LineService;
use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasDateTimeFormatter;
    use SoftDeletes;
    protected $guarded = ['id'];

    /*
         * @param  mixed $data
     * [
     * 'message'=>'send message',
     * 'url' =>'link url',
     * 'image' =>'image',
     * 'text_buttons'=>[
     * ['label'=>'button label','text'=>'button text'],
     * ['label'=>'button label','text'=>'button text'],
     * ]
     * ]
     */
    public function pushMessage($data)
    {
        $line = new LineService();
        $line->pushMessage($this->group_id, $data);
    }
}
