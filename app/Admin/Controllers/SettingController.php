<?php

namespace App\Admin\Controllers;

use App\Admin\Forms\LineSetting;
use App\Admin\Repositories\Setting;
use App\Http\Controllers\Controller;
use Dcat\Admin\Form;
use Dcat\Admin\Widgets\Tab;

use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Layout\Content;

class SettingController extends Controller
{
    public function web(Content $content)
    {

        $tab = Tab::make();
        // $tab->add('Ｍetadata', Metadata::make(), request()->metadata, 'metadata');
        $tab->add('LineSetting', LineSetting::make(), request()->lineSetting, 'lineSetting');


        $tab->add('Mode', Mode::make(), request()->mode, 'mode');

        return $content
            ->title(__('setting.labels.Setting'))
            ->body($tab->withCard());
    }
}
