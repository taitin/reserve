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
use Illuminate\Support\Facades\Log;

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
            'org_car_type' => carType($this->org_car_type),
            'model' => $this->model,
            'booking_time' => zhDate($this->date . ' ' . $this->time),
            'method' => $this->project->name . ' / ' . number_format($this->project->use_times[$this->car_type], 1, '.', '') . ' hr',
            'addition' => implode("\n", $this->getAdditions()),
            'total' => $this->price,
            'org_total' => $this->getTotal($this->org_car_type)['total'],
            'org_total_hour' => number_format($this->getTotal($this->org_car_type)['hr'], 1, '.', ''),
            'total_hour' => number_format($this->total_hour, 1, '.', ''),
            'get_car_time' => zhDate($this->exit_date . ' ' . $this->exit_time),
            'adjust_time' => implode("\n", $this->getAdjustTime())
        ];
        $data['info_str'] =
            implode("\n", [
                // '訂單編號：' . $this->id,
                '姓名：' . $this->name,
                '電話：' . $this->phone,
                '車款：' . $this->model,
                '車型：' . carType($this->car_type),
                '車牌：' . $this->license,
            ]);

        $data['time_str'] =
            implode("\n", [
                '預約時間：',
                $data['booking_time'],
                '取車時間：',
                $data['get_car_time']
            ]);

        $data['project_str']    =
            implode("\n", [
                '服務方案：',
                $data['method'],
                $data['addition'],
                '',
                '本次費用 ' . $this->price . ' 元 ',
                '預計工時 ' . $data['total_hour'] . ' 小時 ',
            ]);

        $data['adjust_type']  = '';
        $adjust_types = [];

        if ($data['org_car_type'] != $data['car_type']) {
            $adjust_types[] = "⚠️{$data['org_car_type']} > {$data['car_type']}";
        }
        if ($data['org_total'] != $data['total']) {
            $adjust_types[] = "⚠️{$data['org_total']} 元 > {$data['total']} 元";
        }
        if ($data['org_total_hour'] != $data['total_hour']) {
            $adjust_types[] = "⚠️{$data['org_total_hour']} hr > {$data['total_hour']} hr";
        }
        $data['adjust_type'] = implode("\n", $adjust_types);
        // if ($data['org_total'] != $data['total']) {
        //     $data['adjust_type'] .= '⚠️{org_total} 元 > {total} 元';
        // }
        // if ($data['org_total_hour'] != $data['total_hour']) {
        //     $data['adjust_type'] .= '⚠️{org_total_hour} hr > {total_hour} hr';
        // }

        return $data;
    }

    function getAdditions()
    {

        $results = [];
        if ($this->addition_services) {
            foreach ($this->additions as $addition) {
                $results[] = $addition->name . ' / ' . round($addition->use_time, 1) . ' hr';
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
        $data['link'] = 'https://www.google.com/maps/place/AK+STUDIO+-+A%E5%92%96%E5%B0%88%E6%A5%AD%E8%BB%8A%E9%AB%94%E7%BE%8E%E5%AE%B9%EF%BC%88%E6%B4%97%E8%BB%8A%E6%89%93%E8%A0%9F%2F%E9%8D%8D%E8%86%9C%2F%E6%B1%BD%E8%BB%8A%E7%BE%8E%E5%AE%B9%EF%BC%89/@25.0716714,121.579073,17z/data=!3m1!4b1!4m6!3m5!1s0x3442addffedbf703:0x3f3a4a8525361143!8m2!3d25.0716714!4d121.5816533!16s%2Fg%2F11vwwb7y66?entry=ttu&g_ep=EgoyMDI0MDkyMi4wIKXMDSoASAFQAw%3D%3D';;
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

    public function getTotal($car_type)
    {
        $hr = 0;
        $total =   $this->project->discount_price[$car_type] ?? 0;
        $hr += $this->project->use_times[$car_type] ?? 0;
        if ($this->additions) {
            foreach ($this->additions as $service) {
                $total += $service['discount_price'][$car_type] ?? 0;
                $hr += $service->use_time;
            }
        }
        return ['total' => $total, 'hr' => $hr];
    }


    public function calculateTotalAmount()
    {

        $r =  $this->getTotal($this->car_type);
        $this->price = $r['total'];
        $this->total_hour = $r['hr'];


        $this->save();
        return $this->price;
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
        $adjust_time = $this->getAdjustTime();
        $times = [];
        foreach ($adjust_time as $time) {
            $times[] = ['label' => $time, 'text' => '@同意修改時間 ' . $time];
        }
        $data = $this->getAdjustTimeStr();
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
                if (str_contains($time['time'], '無預約')) {
                    continue;
                }

                $times[] = $time['date'] . ' ' . $time['time'];
                Log::debug($times);
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

        $this->total_hour;

        $this->exit_date = date('Y-m-d', strtotime($this->date));
        $this->exit_time = date('H:i', strtotime($this->date . ' ' . $this->time) + $this->total_hour * 3600);


        $this->save();
        return $this->confirmBooking();
    }


    public function getSocialNameAttribute()
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
