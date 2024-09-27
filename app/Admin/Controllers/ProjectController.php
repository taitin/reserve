<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Project;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class ProjectController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Project(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('name');
            $grid->column('description');
            $grid->column('use_time');
            $grid->column('price');
            $grid->column('discount_price');
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
        return Show::make($id, new Project(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('description');
            $show->field('use_time');
            $show->field('price');
            $show->field('discount_price');
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
        return Form::make(new Project(), function (Form $form) {
            $form->display('id');
            $form->text('name');
            $form->textarea('description');
            $form->decimal('use_time');
            $form->embeds('price', function (Form\EmbeddedForm $form) {
                foreach (config('wash.car_types') as $key => $value) {
                    $form->number($key, $value);
                }
            });

            $form->embeds('discount_price', function (Form\EmbeddedForm $form) {
                foreach (config('wash.car_types') as $key => $value) {
                    $form->number($key, $value);
                }
            });

            $form->switch('status')->default(1);




            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
