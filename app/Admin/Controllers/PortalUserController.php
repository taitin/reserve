<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\PortalUser;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class PortalUserController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new PortalUser(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('ip');
            $grid->column('agent');
            $grid->column('virefied');
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
        return Show::make($id, new PortalUser(), function (Show $show) {
            $show->field('id');
            $show->field('ip');
            $show->field('agent');
            $show->field('virefied');
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
        return Form::make(new PortalUser(), function (Form $form) {
            $form->display('id');
            $form->text('ip');
            $form->text('agent');
            $form->text('virefied');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
