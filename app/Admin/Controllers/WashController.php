<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Wash;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class WashController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Wash(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('social_id');
            $grid->column('name');
            $grid->column('phone');
            $grid->column('license');
            $grid->column('model');
            $grid->column('parking');
            $grid->column('entry_time');
            $grid->column('exit_time');
            $grid->column('status');
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
        return Show::make($id, new Wash(), function (Show $show) {
            $show->field('id');
            $show->field('social_id');
            $show->field('name');
            $show->field('phone');
            $show->field('license');
            $show->field('model');
            $show->field('parking');
            $show->field('entry_time');
            $show->field('exit_time');
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
        return Form::make(new Wash(), function (Form $form) {
            $form->display('id');
            $form->text('social_id');
            $form->text('name');
            $form->text('phone');
            $form->text('license');
            $form->text('model');
            $form->text('parking');
            $form->text('entry_time');
            $form->text('exit_time');
            $form->text('status');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
