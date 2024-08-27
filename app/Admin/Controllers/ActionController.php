<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Action;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class ActionController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Action(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('keyword');
            $grid->column('from');

            $grid->column('target');
            $grid->column('content');
            $grid->column('params');
            $grid->column('do_method');
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
                $filter->like('keyword');
                $filter->like('content');
                $filter->like('do_method');
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
        return Show::make($id, new Action(), function (Show $show) {
            $show->field('id');
            $show->field('keyword');
            $show->field('target');
            $show->field('content');
            $show->field('params');
            $show->field('do_method');
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
        return Form::make(new Action(), function (Form $form) {
            $form->display('id');
            $form->text('keyword')->required();
            $form->select('from')->options([
                'customer' => 'customer',
                'group' => 'group',
                'master' => 'master',
            ]);
            $form->select('target')->options([
                'customer' => 'customer',
                'group' => 'group',
                'master' => 'master',

            ]);
            $form->textarea('content');
            $form->select('type')->options([
                'reply' => 'reply',
                'push' => 'push',
            ])->default('reply');
            // $form->tags('params');


            $form->table('text_buttons', function ($table) {
                $table->text('label');
                $table->text('text');
            });

            $form->text('do_method');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
