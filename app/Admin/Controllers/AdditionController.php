<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Addition;
use Dcat\Admin\Admin;
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
            // $grid->column('id')->sortable();
            $grid->sortable('order');
            $grid->model()->orderBy('order', 'asc');
            $grid->column('order');
            $grid->column('status')->switch();

            $grid->column('name');
            $grid->column('description');
            $grid->column('use_time');
            $grid->column('price')->display(function ($value) {
                return showTypeContent($value, '元');
            })->setAttributes(['style' => 'width:220px']);
            $grid->column('discount_price')->display(function ($value) {
                return showTypeContent($value, '元');
            })->setAttributes(['style' => 'width:220px']);
            $grid->column('addition_date')->display(function ($value) {
                return $this->addition_start . ' ~ ' . $this->addition_end;
            });

            $grid->column('projects', '指定方案')->display(function ($value) {
                return $this->projects->pluck('name');
            })->label();
            // $grid->column('created_at');
            // $grid->column('updated_at')->sortable();

            $grid->filter(function (Grid\Filter $filter) {
                // $filter->equal('id');
                $filter->like('name');
            });
            $grid->setActionClass(Grid\Displayers\Actions::class);
            $grid->actions(function ($actions) {
                $actions->disableView();
                $actions->disableEdit();
                $actions->disableDelete();
                $actions->quickEdit();

                // $actions->append((new \App\Admin\Actions\ActionRedirect('/' . Request()->segment(2) . '/' . $this->getKey() . '/edit', '編輯', '', $this->getKey(), 'primary', 'pencil'))->render());

                $actions->append((new \App\Admin\Actions\MyGridDelete()));
            });
            $btn_style = 'primary';
            $icon = 'pencil';
            $name = '編輯';

            $btn = '<button style="margin:1px" class="btn btn-' . $btn_style . '"><i class="fa fa-' . $icon . '"></i>' . $name . '</button>';
            $script = <<<JS
            $(function(){
                $('.quick-edit').html('{$btn}');
            })

JS;
            Admin::script($script);
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
        return Form::make(new Addition(['projects']), function (Form $form) {
            $form->display('id');
            $form->text('name')->required();
            $form->text('description');
            $form->decimal('use_time');

            // $form->embeds('use_times', function (Form\EmbeddedForm $form) {
            //     foreach (config('wash.car_types') as $key => $value) {
            //         $form->decimal($key, $value);
            //     }
            // });
            $form->embeds('price', function (Form\EmbeddedForm $form) {
                foreach (config('wash.car_types') as $key => $value) {
                    $form->number($key, $value);
                }
            });
            $form->switch('use_discount')->default(1);


            $form->embeds('discount_price', function (Form\EmbeddedForm $form) {
                $form->html('<h5 class="title">折扣價及折扣%請擇一輸入即可</h5>');

                foreach (config('wash.car_types') as $key => $value) {

                    $form->html('<h4>' . $value . '</h4>');
                    $form->html('<h5></h5>');

                    $form->text($key, '折扣價')->width(7, 5);
                    $form->decimal($key . '_discount', '折扣%')->width(7, 5);
                }
            });


            $script = <<<JS
            $(function(){
                $('.embed-discount_price-form').addClass('row');
                $('.embed-discount_price-form .form-group').addClass('col-6');
                $('.embed-discount_price-form .title').closest('.form-group').removeClass('col-6').addClass('col-12');
            })
JS;
            Admin::script($script);


            $form->switch('status')->default(1);
            $form->dateRange('addition_start', 'addition_end', '方案執行時間');


            $form->multipleSelect('projects', '搭配洗車方案')->options(\App\Models\Project::where('status', 1)
                ->orderBy('order', 'asc')
                ->pluck('name', 'id'))
                ->customFormat(function ($v) {
                    if (! $v) {
                        return [];
                    }

                    // 从数据库中查出的二维数组中转化成ID
                    return array_column($v, 'id');
                })
                ->help('空白表示全方案皆可搭配');

            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
