<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Project;
use Dcat\Admin\Admin;
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
            $grid->sortable('order');
            $grid->model()->orderBy('order', 'asc');
            // $grid->column('id')->sortable();
            $grid->column('order', '頁面排序');

            $grid->column('status')->switch();
            $grid->column('name')->editable();
            $grid->column('description')
                ->display(function ($value) {
                    return nl2br($value);
                })->setAttributes(['style' => 'min-width:200px']);

            $grid->column('use_times')->display(function ($value) {
                return showTypeContent($value, 'hr');
            })->setAttributes(['style' => 'width:180px']);
            $grid->column('price')->display(function ($value) {
                return showTypeContent($value, '元');
            })->setAttributes(['style' => 'width:220px']);
            $grid->column('use_discount')->switch();
            $grid->column('discount_price')->display(function ($value) {
                return showTypeContent($value, '元');
            })->setAttributes(['style' => 'width:220px']);
            // $grid->column('created_at');

            $grid->column('discount_date')->display(function ($value) {
                return $this->discount_start . ' ~ ' . $this->discount_end;
            });


            $grid->column('project_date')->display(function ($value) {
                return $this->project_start . ' ~ ' . $this->project_end;
            });

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
            $grid->tools('<a class="btn btn-primary"  href="' . url('wash/preview') . '" target="_blank"><i class="fa fa-eye"></i>預覽設定</a>');
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

            $form->switch('use_discount')->default(1);

            $form->select('by_discount_user', '可使用優惠的使用者')->options(config('wash.discount_user'));

            $form->dateRange('discount_start', 'discount_end', '折扣時間');


            $form->embeds('discount_price', function (Form\EmbeddedForm $form) {
                $form->html('<h5 class="title">折扣價」或「折扣 %」請擇一填入</h5>');

                foreach (config('wash.car_types') as $key => $value) {

                    $form->html('<h4>' . $value . '</h4>');
                    $form->html('<h5></h5>');

                    $form->text($key, '折扣價')->width(8, 4)->rules();
                    $form->decimal($key . '_discount', '折扣%')->width(9, 3)->placeholder('數字 10 = 定價 x 10%');
                }
            });


            $script = <<<JS
            $(function(){
                $('.embed-discount_price-form').addClass('row');
               //奇數項
               $('.embed-discount_price-form .form-group:odd').addClass('col-6');
                $('.embed-discount_price-form .form-group:even').addClass('col-6');
                $('.embed-discount_price-form .title').closest('.form-group').removeClass('col-6').addClass('col-12');
            })
JS;
            Admin::script($script);



            $form->switch('status')->default(0);
            $form->dateRange('project_start', 'project_end', '方案執行時間');

            $form->submitted(function (Form $form) {
                //discount_price 折扣價 根 折扣 只能出現一者
                $discount_price = $form->discount_price;
                if (!empty($discount_price)) {
                    foreach (config('wash.car_types') as $key => $value) {
                        if ($discount_price[$key] && $discount_price[$key . '_discount']) {
                            return $form->response()->error('折扣價與折扣%只能填寫一者');
                        }
                    }
                }
            });


            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
