<?php

namespace App\Http\Controllers;

use App\Admin\Repositories\Parking;
use App\Models\Group;
use App\Models\Parking as ModelsParking;
use App\Models\Wash;
use App\Services\LineService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WashController extends Controller
{
    //

    public function index()
    {
        //洗車 form

        $parkings = ModelsParking::all();
        $data['parkings'] = $parkings->groupBy('city');

        return view('wash.index', $data);
    }

    public function getProfile($social_id)
    {
        //洗車 form
        $wash =  Wash::where('social_id', $social_id)->orderBy('id', 'desc')->first();

        return [
            'result' => true,
            'data' => $wash
        ];
    }
    public function store(Request $request)
    {

        //洗車 form
        try {
            $request->validate([
                'phone' => 'required',
                'license' => 'required',
                'model' => 'required',
                // 'parking' => 'required',
                'date' => 'required',
                'time' => 'required',
                'car_type' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            dd($e->errors());
        }
        $wash = new \App\Models\Wash($request->all());

        $wash->calculateTotalAmount();
        $wash->save();

        //reply user

        //push to group
        $group =  Group::where('type', '時間到府')->first();

        //組織上面資料變成文字傳送 要換行
        // $message = "洗車申請\n";
        // $message .= "電話: " . $request->phone . "\n";
        // $message .= "車牌: " . $request->license . "\n";

        // $message .= "預約日期: " . $request->date . "\n";
        // $message .= "預約時間: " . $request->time . "\n";

        // $message .= "客戶自填車型: " . carType($request->car_type) . "\n";
        // if (!empty($request->addition_services))
        //     $message .= "額外加值服務: " . implode(',', $request->addition_services) . "\n";

        // //金額試算
        // $message .= "總金額: " . $wash->price . "\n";



        // $group->pushMessage([
        //     'message' => $message,
        //     'text_buttons' => [
        //         ['label' => '同意申請', 'text' => '@同意洗車 ' . $wash->id],
        //         ['label' => '需調整預約申請', 'text' => '@調整價格 ' . $wash->id],
        //         ['label' => '拒絕申請', 'text' => '@拒絕洗車 ' . $wash->id],
        //     ]
        // ]);


        $Line = new LineController();
        $inputText = '@送出洗車表單 ' . $wash->id;
        $type = 'customer';
        $keywords =   $Line->fetchKeyword($inputText,  $type);
        $input = [
            'name' => 'system',
            'group_name' =>  'system',
            'group_id' => 0,
            'message_type' => 'text',
            'social_id' => 0,
            'date' => date('Y-m-d H:i:s'),
            'text' =>  $inputText,
            'keyword' => implode(',', array_keys($keywords)),
            'value' => implode(',', array_column($keywords, 'value')),
            'message_id' =>  '',
            'reply_token' =>  '',
            'quotedMessageId' => ''

        ];
        $Line->saveRecord($input, $type);



        return view('wash.close', ['message' => '洗車預約已送出']);
    }

    public function setAmount(Request $request)
    {
        //洗車 form
        $wash = \App\Models\Wash::find($request->id);

        return view('wash.pay', compact('wash'));
    }


    public function doSetAmount(Request $request)
    {
        //洗車 form
        try {
            $request->validate([
                'amount' => 'required',
            ]);

            $wash = \App\Models\Wash::find($request->id);
            $wash->price = $request->amount;
            $wash->status = 'set_amount';
            $wash->save();

            $Line = new LineController();
            $inputText = '@同意洗車 ' . $wash->id;
            $type = 'group';
            $keywords =   $Line->fetchKeyword($inputText,  $type);
            $input = [
                'name' => 'system',
                'group_name' =>  'system',
                'group_id' => 0,
                'message_type' => 'text',
                'social_id' => 0,
                'date' => date('Y-m-d H:i:s'),
                'text' =>  $inputText,
                'keyword' => implode(',', array_keys($keywords)),
                'value' => implode(',', array_column($keywords, 'value')),
                'message_id' =>  '',
                'reply_token' =>  '',
                'quotedMessageId' => ''

            ];
            $Line->saveRecord($input, $type);

            //Return 自動關閉的頁面
            return view('wash.close', ['message' => '洗車金額已設定']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            dd($e->errors());
        }

        return view('wash.close', ['message' => '洗車金額設定失敗 請洽款管理員']);
    }


    public function payWebhookFake(Request $request)
    {

        //洗車 form
        $wash = \App\Models\Wash::find($request->id);
        $wash->status = 'paid';
        $wash->save();
        $input = [
            'keyword' => '付款完成',
            'value' => $wash->id,
        ];
        $line = new LineController();
        $line->actionTrigger(json_decode(json_encode($input), false), 'customer');

        return view('wash.close', ['message' => '付款成功！！請返回Line繼續操作']);
    }



    public function payWebhook(Request $request)
    {
        //洗車 form
        $wash = \App\Models\Wash::find($request->id);
        $autopass = new \App\Services\AutopassService();
        if (empty($wash->pay_data['invoice_no'])) {
            return view('wash.close', ['message' => '付款失敗']);
        }
        $auth_result = $autopass->getPayResult($wash->pay_data['invoice_no']);

        $wash->pay_auth_result = $auth_result;
        if ($auth_result['data']['payment_state'] != 'authorized') {
            $wash->status = 'pay_fail';
            $wash->save();
            return view('wash.close', ['message' => '付款失敗']);
        }

        $wash->status = 'paid';
        $wash->save();
        $input = [
            'keyword' => '付款完成',
            'value' => $wash->id,
        ];
        $line = new LineController();
        $line->actionTrigger(json_decode(json_encode($input), false), 'customer');

        return view('wash.close', ['message' => '付款成功！！請返回Line繼續操作']);
    }


    public function pay(Request $request)
    {
        //洗車 form
        $wash = \App\Models\Wash::find($request->id);

        return view('wash.pay', compact('wash'));
    }

    public function paid(Request $request)
    {
        //洗車 form
        try {
            $request->validate([
                'wash_id' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            dd($e->errors());
        }


        $input = [
            'keyword' => '付款完成',
            'value' => $request->wash_id,
        ];
        $line = new LineController();
        $line->actionTrigger(json_decode(json_encode($input), false), 'customer');

        return view('wash.close', ['message' => '付款完成']);
    }

    public function arrange($id)
    {
        $wash = \App\Models\Wash::find($id);
        $default_arrange = Carbon::now()->addMinutes(30)->toDateTimeString(); //

        return view('wash.arrange', compact('wash', 'default_arrange'));
    }

    public function arranged(Request $request)
    {
        //洗車 form
        try {
            $request->validate([
                'wash_id' => 'required',
                'arrive_at' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            dd($e->errors());
        }


        $wash = \App\Models\Wash::find($request->wash_id);
        $wash->status = 'arranged';
        $wash->arrive_at = $request->arrive_at;
        $wash->save();
        $input = [
            'keyword' => '安排完成',
            'value' => $request->wash_id,
        ];
        $line = new LineController();
        $line->actionTrigger(json_decode(json_encode($input), false), 'group');

        return view('wash.close', ['message' => '安排完成']);
    }
    public function before($id)
    {
        $wash = \App\Models\Wash::find($id);

        return view('wash.photo', ['photos' => $wash->before_photos, 'title' => '洗車前照片']);
    }

    public function after($id)
    {
        $wash = \App\Models\Wash::find($id);

        return view('wash.photo', ['photos' => $wash->after_photos, 'title' => '洗車後照片']);
    }

    public function redirectPay($id)
    {
        $wash = \App\Models\Wash::find($id);
        //付款狀態確認
        if ($wash->status == 'paid') {
            return view('wash.close', ['message' => '付款連結失效']);
        }


        return redirect()->to($wash->pay_result['data']['payment_url']);
    }

    public function getAvailableTime(Request $request)
    {
        $date = $request->date;
        $available_times =   $this->getAvailable($date);

        return ['result' => true, 'available_times' => array_values($available_times)];
    }


    public function getAvailable($date)
    {
        $day = Carbon::parse($date)->dayOfWeek;
        //判斷若日期為今天以前 則 $day = 0 無法預約
        strtotime($date) < strtotime(date('Y-m-d')) ? $day = 0 : $day = $day;

        if ($day == 0) {
            $available_times = ['*本日已無預約時端，請選擇其他日期'];
        } else {
            $available_times = ['09:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00'];
        }

        //扣掉已預約的時間
        $washes = Wash::where('date', $date)->get();
        foreach ($washes as $wash) {
            $key = array_search(substr($wash->time, 0, 5), $available_times);
            if ($key !== false) {
                unset($available_times[$key]);
            }
        }

        return $available_times;
    }

    public function adjustTime(Request $request)
    {

        $wash = \App\Models\Wash::find($request->id);

        return  view('wash.adjust_time', ['wash' => $wash]);
    }

    public function saveAdjustTime(Request $request)
    {

        return $request;
    }
}
