<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Addition;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class AdditionController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Addition(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('name');
            $grid->column('description');
            $grid->column('use_time');
            $grid->column('price');
            $grid->column('discount_price');
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
        return Show::make($id, new Addition(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('description');
            $show->field('price');
            $show->field('discount_price');
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
        return Form::make(new Addition(), function (Form $form) {
            $form->display('id');
            $form->text('name')->required();
            $form->text('description');
            // $form->decimal('use_time');

            $form->embeds('use_times', function (Form\EmbeddedForm $form) {
                foreach (config('wash.car_types') as $key => $value) {
                    $form->decimal($key, $value);
                }
            });
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
