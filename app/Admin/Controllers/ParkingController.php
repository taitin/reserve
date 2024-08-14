<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Parking;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class ParkingController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Parking(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('name');
            $grid->column('address');
            $grid->column('city');
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
        return Show::make($id, new Parking(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('address');
            $show->field('city');
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
        return Form::make(new Parking(), function (Form $form) {
            $form->display('id');
            $form->text('name');
            $form->text('address');
            $form->text('city');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
