<?php

namespace App\Models;

use App\Admin\Extensions\Tools\OrderToken;
use App\Http\Controllers\LineController;
use App\Http\Controllers\WashController;
use App\Services\AutopassService;
use Dcat\Admin\Traits\HasDateTimeFormatter;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class Wash extends Model
{
    use HasDateTimeFormatter;
    use SoftDeletes;
    protected $guarded = ['id'];
    protected $casts = [
        'before_photos' => 'json',
        'after_photos' => 'json',
        'pay_data' => 'json',
        'pay_result' => 'json',
        'pay_auth_result' => 'json',
        'addition_services' => 'json',
        'suggest_time' => 'json',
    ];

    function getNewBooking()
    {

        $data = [
            'name' => $this->name,
            'phone' => $this->phone,
            'license' => $this->license,
            'car_type' => carType($this->car_type),
            'model' => $this->model,
            'booking_time' => zhDate($this->date . ' ' . $this->time),
            'method' => $this->project->name . '/' . number_format($this->project->use_time, 1, '.', '') . 'hr',
            'addition' => implode("\n", $this->getAdditions()),
            'total' => $this->price,
            'total_hour' => number_format($this->total_hour, 1, '.', ''),
            'get_car_time' => zhDate($this->exit_date . ' ' . $this->exit_time),
            'adjust_time' => implode("\n", $this->getAdjustTime())
        ];

        $data['info_str'] =
            implode("\n", [
                '姓名:' . $this->name,
                '電話:' . $this->phone,
                '車款:' . $this->model,
                '車牌:' . $this->license,

                '服務項目:' . $this->project->name . '/' . number_format($this->project->use_time, 1, '.', '') . 'hr',
                '附加服務:' . implode("\n", $this->getAdditions()),
                '總金額:' . $this->price,
                '總時數:' . number_format($this->total_hour, 1, '.', ''),
            ]);

        $data['time_str'] =
            implode("\n", [
                '預約時間:',
                $data['booking_time'],
                '取車時間:,',
                $data['get_car_time']
            ]);

        $data['project_str']    =
            implode("\n", [
                '服務方案:',
                $data['method'],
                $data['addition'],
                '本次費用 ' . $this->price . ' 元 ',
                '預計工時 ' . $data['total_hour'] . ' 小時 ',
            ]);


        // 如果是3.00 顯示 3 ,3.50 顯示 3.5

        return $data;
    }

    function getAdditions()
    {

        $results = [];
        if ($this->addition_services) {
            foreach ($this->additions as $addition) {
                $results[] = $addition->name . '/' . $addition->use_time . 'hr';
            }
        }
        return $results;
    }





    function getSetAmoutnLink()
    {
        return ['link' => liffUrl('wash/' . $this->id . '/set_amount')];
    }


    function getPayLink()
    {
        $data = $this->getNewBooking();
        $data['link'] = url("wash/$this->id/redirect_pay?openExternalBrowser=1");
        return $data;
    }


    function setWorker($data)
    {
        $this->worker = $data[0];
        $this->save();
        return [];
    }

    function getTodayReserve()
    {
        $today = date('Y-m-d');
        $reserves = Wash::where('date', $today)->orderBy('time', 'asc')->get();
        $str = '';
        foreach ($reserves as $reserve) {
            $str .= substr($reserve->time, 0, 5) . ' ' . $reserve->phone . ' ' . $reserve->license . ' ' . $reserve->model . ' ' . $reserve->worker . "\n";
        }

        if (empty($str)) {
            $str = '今日無預約';
        }

        return [

            'reserves' =>  $str
        ];
    }


    function reject()
    {
        $this->status = 'rejected';
        $this->save();
        return [];
    }

    function confirmBooking()
    {

        $this->status = 'confirmed';
        $this->save();
        $data = $this->getNewBooking();
        return $data;
    }

    function getFinishLink()
    {

        $data = $this->getNewBooking();
        $data['link'] = 'https://www.google.com/maps/place/AKdetailing+x+%E8%80%81%E8%95%AD%E5%B0%88%E6%A5%AD%E6%97%A5%E8%A6%8F%E5%A4%96%E5%8C%AF%E8%BB%8A+%E5%8F%B0%E5%8C%97%E6%97%97%E8%89%A6%E5%BA%97/@25.0716714,121.5816533,17z/data=!3m1!4b1!4m6!3m5!1s0x3442ad848ed4b64d:0xd8ca8cf33d1abf43!8m2!3d25.0716714!4d121.5816533!16s%2Fg%2F11t1j92fxp?entry=ttu';;
        return $data;
    }

    function getMasterAttribute()
    {
        return Master::find($this->worker);
    }



    function payFinish()
    {
        $this->status = 'paid';
        $this->save();
        $data = $this->getNewBooking();
        $masters = Master::where('status', 1)->get();
        $master_options = [];
        foreach ($masters as $master) {
            $master_options[] = ['label' => $master->name, 'text' => '@安排師傅 ' . $master->id];
        }

        $data['text_buttons'] = $master_options;
        // $data['link'] = liffUrl('wash/' . $this->id . '/arrange');
        return $data;
    }

    function arrange()
    {
        $this->status = 'arranged';
        $this->save();
        return [
            'arrive_at' => $this->arrive_at
        ];
    }


    function getBeforePhotos()
    {
        return [
            'photos' => $this->before_photos
        ];
    }

    function getBeforeUrl()
    {
        return ['link' => liffUrl('wash/' . $this->id . '/before')];
    }


    function getAfterUrl()
    {
        return ['link' => liffUrl('wash/' . $this->id . '/after')];
    }

    function getModel()
    {
        return ['model' => $this->model, 'car_type' => carType($this->car_type)];
    }
    public function getQestionUrl()
    {
        return ['link' => 'https://pklotcorp.typeform.com/to/Wdnl2Gyw'];
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function getAdditionsAttribute()
    {
        if (empty($this->addition_services)) {
            return [];
        }
        return Addition::whereIn('id', $this->addition_services)->get();
    }

    public function calculateTotalAmount()
    {

        $hr = 0;
        $total =   $this->project->discount_price[$this->car_type] ?? 0;
        $hr += $this->project->use_time;
        if ($this->additions) {
            foreach ($this->additions as $service) {
                $total += $service['discount_price'][$this->car_type] ?? 0;
                $hr += $service->use_time;
            }
        }
        $this->price = $total;
        $this->total_hour = $hr;


        $this->save();
        return $total;
    }


    public function changeCarType($car_type)
    {
        $this->car_type = $car_type;
        $this->calculateTotalAmount();
        $this->save();
    }

    public function setCarTypeHouse()
    {
        $this->changeCarType('house');
        // $this->sendAdjustMessage();

        return $this->getNewBooking();
    }

    public function setCarType5p()
    {
        $this->changeCarType('5p');
        // $this->sendAdjustMessage();
        return $this->getNewBooking();
    }

    public function setCarType7p()
    {
        $this->changeCarType('7p');
        // $this->sendAdjustMessage();
        return $this->getNewBooking();
    }

    public function sendAdjustMessage()
    {
        // $input = [
        //     'keyword' => '調整完畢',
        //     'value' => $this->id,
        // ];
        // $line = new LineController();
        // $line->actionTrigger(json_decode(json_encode($input), false), 'group');

        // return;
    }

    public function getTimeAdjust()
    {

        return ['link' => liffUrl('wash/' . $this->id . '/time_adjust')];
    }

    public function getReBooking()
    {

        return ['link' => liffUrl('wash/' . $this->id . '/re_book')];
    }


    public function getAdjustTimeWithLabel()
    {
        $data = $this->getNewBooking();
        $data['adjust_time'] = $this->getAdjustTime();
        $times = [];
        foreach ($data['adjust_time'] as $time) {
            $times[] = ['label' => $time, 'text' => '@同意修改時間 ' . $time];
        }

        $times[] = ['label' => '預約其他時間', 'text' => '@預約其他時間'];
        $times[] = ['label' => '取消預約', 'text' => '@取消預約'];
        $data['text_buttons'] = $times;
        return  $data;
    }

    public function getAdjustTime()
    {

        $times = [];
        if (!empty($this->suggest_time)) {
            foreach ($this->suggest_time as $time) {
                $times[] = $time['date'] . ' ' . $time['time'];
            }
        }
        return $times;
    }

    public function getAdjustTimeStr()
    {
        $adjust_time = $this->getAdjustTime();
        $data = $this->getNewBooking();
        $data['adjust_time'] = implode("\n", $adjust_time);
        return $data;
    }

    public function setAdjustTime($t)
    {
        $this->date = $t[0];
        $this->time = $t[1];
        $this->status = 'arranged';
        $this->save();
        return $this->confirmBooking();
    }


    public function getNameAttribute()
    {
        $message = Message::where('social_id', $this->social_id)
            ->orderBy('id', 'desc')
            ->first();
        return $message->name ?? '';
    }


    public function getMethodAttribute()
    {
        if (!empty($this->project)) {
            return $this->project->name . '/' . number_format($this->project->use_time, 1, '.', '') . 'hr';
        }
        return '';
    }

    public function setCancel()
    {
        $this->status = 'canceled';
        $this->save();
        return $this->getNewBooking();
    }


    function setReject()
    {
        $this->status = 'rejected';
        $this->save();
        return $this->getNewBooking();
    }


    function setTimeOut()
    {
        $this->status = 'timeout';
        $this->save();
        return  $this->getNewBooking();
    }
}
