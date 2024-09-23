<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Except;
use Dcat\Admin\Admin;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class ExceptController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Except(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('date');
            $grid->column('time')->display(function ($time) {
                return implode(',', $time);
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
        return Show::make($id, new Except(), function (Show $show) {
            $show->field('id');
            $show->field('date');
            $show->field('time');
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
        return Form::make(new Except(), function (Form $form) {
            $form->display('id');
            //日期只能是唯一值
            $form->date('date')->creationRules('unique:excepts,date')
                ->updateRules('unique:excepts,date,{{id}}');



            //把 value 變成key
            $times = config('wash.business_times');
            $times = array_combine($times, $times);

            $form->html('
    <button type="button" id="select-all">Select All</button>
    <button type="button" id="deselect-all">Deselect All</button>
');
            $form->multipleSelect('time')->options(
                $times
            )->attribute(['id' => 'multiple-select']);

            $form->display('created_at');
            $form->display('updated_at');
            Admin::script('
    Dcat.ready(function () {
        $("#select-all").click(function () {
        alert("select-all");
            $("#multiple-select").val(' . json_encode(array_keys($times)) . ').trigger("change");
        });
        $("#deselect-all").click(function () {
            $("#multiple-select").val([]).trigger("change");
        });
    });
');
        });
    }
}
