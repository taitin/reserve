<?php

namespace App\Admin\Forms;

use Dcat\Admin\Widgets\Form;

class Mode extends SettingFrom
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = 'Mode';
    public $key = 'mode';

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->select('status', 'Mode')->options(config('wash.modes'))->default(0);
    }
}
