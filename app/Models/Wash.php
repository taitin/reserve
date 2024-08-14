<?php

namespace App\Models;

use App\Admin\Extensions\Tools\OrderToken;
use App\Http\Controllers\LineController;
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
        'addition_services' => 'json'
    ];
    protected $car_type_prices = [
        'house' => 1500,
        '5p' => 1600,
        '7p' => 1700
    ];
    protected $service_prices = [
        '車輛前檔玻璃潑水' => 600,
        '全車玻璃潑水' => 1500,
        '鍍膜維護劑' => 1500
    ];


    function getSetAmoutnLink()
    {
        return ['link' => liffUrl('wash/' . $this->id . '/set_amount')];
    }


    function getPayLink()
    {
        $this->status = 'get_pay_link';
        $data = [
            'invoice_no' => $this->id . '_' . time(),
            'request_amount' => $this->price,
            'callback_url' => url('wash/' . $this->id . '/pay_webhook/' . OrderToken::token($this->id)),
        ];
        $this->pay_data = $data;
        $this->save();
        $autopass = new AutopassService();
        $pay_result = $autopass->makePay($data);
        $this->pay_result =  $pay_result;
        $this->save();
        return [
            'car_type' => carType($this->car_type),
            'price' => $this->price,
            'link' => url("wash/$this->id/redirect_pay")
        ];
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

        $data = [
            'phone' => $this->phone,
            'license' => $this->license,
            'model' => $this->model,
            'date' => $this->date,
            'time' => $this->time,
        ];


        return $data;
    }


    function payFinish()
    {
        $this->status = 'paid';
        $this->save();

        $data = [
            'phone' => $this->phone,
            'license' => $this->license,
            'model' => $this->model,
            'parking' => $this->parking,
            'entry_time' => $this->entry_time,
            'exit_time' => $this->exit_time,
            'price' => $this->price
        ];


        $data['link'] = liffUrl('wash/' . $this->id . '/arrange');
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
        return ['model' => $this->model];
    }
    public function getQestionUrl()
    {
        return ['link' => 'https://pklotcorp.typeform.com/to/Wdnl2Gyw'];
    }

    public function calculateTotalAmount()
    {
        $total = $this->car_type_prices[$this->car_type];
        if ($this->addition_services) {
            foreach ($this->addition_services as $service) {
                $total += $this->service_prices[$service] ?? 0;
            }
        }
        $this->price = $total;
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
        $this->sendAdjustMessage();

        return ['price' => $this->price];
    }

    public function setCarType5p()
    {
        $this->changeCarType('5p');
        $this->sendAdjustMessage();

        return ['price' => $this->price];
    }

    public function setCarType7p()
    {
        $this->changeCarType('7p');
        $this->sendAdjustMessage();
        return ['price' => $this->price];
    }

    public function sendAdjustMessage()
    {
        $input = [
            'keyword' => '調整完畢',
            'value' => $this->id,
        ];
        $line = new LineController();
        $line->actionTrigger(json_decode(json_encode($input), false), 'group');

        return;
    }
}
