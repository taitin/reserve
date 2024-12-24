<?php

namespace App\Admin\Actions;

use Dcat\Admin\Grid\RowAction;
use Illuminate\Database\Eloquent\Model;

class ActionRedirect extends RowAction
{
    public $name = '按鈕名稱';
    public function __construct($url, $name, $arg = '', $key = '', $btn_style = 'primary', $icon = 'circle-thin')
    {
        $this->name  = '<button style="margin:1px" class="btn btn-' . $btn_style . '"><i class="fa fa-' . $icon . '"></i>' . $name . '</button>';
        $this->url   = $url;
        $this->arg   = $arg;
        $this->key   = $key;
    }
    public function href()
    {
        $query = '';
        if ($this->arg != '') {

            if (strpos($this->url, '?') !== false) $needle = '&';
            else $needle = '?';
            $query = $needle . $this->arg . '=' . $this->key;
        }

        if (strpos($this->url, 'http') !== false) return $this->url . $query;
        return admin_base_path($this->url . $query);
    }

    public function render($target = '_self', $attribute = '')
    {
        if ($href = $this->href()) {
            return "<a href='{$href}' target='{$target}' {$attribute}>{$this->name}</a>";
        }
    }



    public function handle(Model $model)
    {
        // $model ...

    }
}
