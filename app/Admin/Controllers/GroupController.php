<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Group;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class GroupController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Group(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('name');
            $grid->column('group_id');
            $grid->column('type')->display(function ($type) {
                return config('wash.modes')[$type] ?? '';
            });
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
        return Show::make($id, new Group(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('group_id');
            $show->field('type');
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
        return Form::make(new Group(), function (Form $form) {
            $form->display('id');
            $form->text('name');
            $form->text('group_id');

            $form->select('type')->options(config('wash.modes'));

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
