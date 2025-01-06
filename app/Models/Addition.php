<?php

namespace App\Models;

use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Addition extends Model implements Sortable
{
    use HasDateTimeFormatter;
    use SoftDeletes;
    use SortableTrait;
    protected $casts = [
        'price' => 'json',
        'discount_price' => 'json',
        'use_times' => 'json',
    ];

    public $sortable = [
        'order_column_name' => 'order',
        'sort_when_creating' => true,
    ];

    public function getDiscountAttribute($value)
    {

        $result = [];
        foreach (config('wash.car_types') as $type => $name) {
            if (empty($this->use_discount)) $result[$type] = null;
            else {
                //$this->discount_user  0 不看is_member  1 只看is_member   2 只看非is_member
                if (
                    $this->by_discount_user == 1 && !$this->is_member ||
                    $this->by_discount_user == 2 && $this->is_member
                ) $result[$type] = null;
                else {
                    if (
                        !empty($this->discount_start) && !empty($this->discount_end) &&
                        (
                            strtotime(date('Y-m-d')) > strtotime($this->discount_end) ||
                            strtotime(date('Y-m-d')) < strtotime($this->discount_start)
                        )
                    ) $result[$type] = null;
                    else {
                        if (!empty($this->discount_price[$type])) {
                            $result[$type] = $this->discount_price[$type];
                        } elseif (!empty($this->discount_price[$type . '_discount'])) {
                            $result[$type] = round($this->price[$type] * $this->discount_price[$type . '_discount'] / 100);
                        } else   $result[$type] = null;
                    }
                }
            }
        }
        return $result;
    }


    public function projects()
    {
        return $this->belongsToMany(Project::class, 'addition_projects');
    }

    public function setMember($is_member)
    {
        $this->is_member = $is_member;
    }
}
