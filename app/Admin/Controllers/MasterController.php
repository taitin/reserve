<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Master;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class MasterController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Master(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('social_id');
            $grid->column('name');
            $grid->column('status')->switch();
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new Master(), function (Show $show) {
            $show->field('id');
            $show->field('social_id');
            $show->field('name');
            $show->field('status');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Master(), function (Form $form) {
            $form->display('id');

            //從 Messsage 中找出 distinct 的 social_id
            $form->select('social_id')->options(function ($id) {
                $messages = \App\Models\Message::query()->select('social_id', 'name')->distinct()->get();
                $options = [];
                foreach ($messages as $message) {
                    $options[$message->social_id] = $message->name;
                }
                return $options;
            });
            $form->hidden('name');
            $form->switch('status')->default(1);


            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
