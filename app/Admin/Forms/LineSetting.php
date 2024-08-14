<?php

namespace App\Admin\Forms;

use Dcat\Admin\Widgets\Form;

class LineSetting extends SettingFrom
{
    /**
     * The form title.
     *
     * @var string
     */
    public $title = 'LineMeaage';
    public $key = 'line_message';

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->text('login_channel_id', __('LINE_CHANNEL_ID'))->required();
        $this->text('login_secret', __('LINE_SECRECT'))->required();
        $this->text('redirect', __('LINE REDIRECT'))->required();
        $this->text('bot_channel_id', __('BOT_LINE_CHANNEL_ID'))->required();
        $this->text('bot_secret', __('BOT_LINE_SECRECT'))->required();
        $this->text('bot_access_token', __('BOT_ACCESS_TOKEN'))->required();
    }
}
