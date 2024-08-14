<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\PushMessage;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class PushMessageController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new PushMessage(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('reply_token');
            $grid->column('content');
            $grid->column('result');
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
        return Show::make($id, new PushMessage(), function (Show $show) {
            $show->field('id');
            $show->field('reply_token');
            $show->field('content');
            $show->field('result');
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
        return Form::make(new PushMessage(), function (Form $form) {
            $form->display('id');
            $form->text('reply_token');
            $form->text('content');
            $form->text('result');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
