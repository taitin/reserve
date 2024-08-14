<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Message;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class MessageController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Message(), function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');
            $grid->column('id')->sortable();
            $grid->column('name');
            // $grid->column('social_id');
            // $grid->column('group_id');
            $grid->column('group_name');
            $grid->column('group_type');
            $grid->column('date');
            $grid->column('text');
            $grid->column('message_type');
            $grid->column('keyword');
            $grid->column('value');
            $grid->column('reply_token');
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
        return Show::make($id, new Message(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('social_id');
            $show->field('group_id');
            $show->field('group_name');
            $show->field('group_type');
            $show->field('date');
            $show->field('text');
            $show->field('message_type');
            $show->field('keyword');
            $show->field('value');
            $show->field('reply_token');
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
        return Form::make(new Message(), function (Form $form) {
            $form->display('id');
            $form->text('name');
            $form->text('social_id');
            $form->text('group_id');
            $form->text('group_name');
            $form->text('group_type');
            $form->text('date');
            $form->text('text');
            $form->text('message_type');
            $form->text('keyword');
            $form->text('value');
            $form->text('reply_token');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
