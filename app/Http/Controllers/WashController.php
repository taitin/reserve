<?php

namespace App\Http\Controllers;

use App\Admin\Extensions\Tools\OrderToken;
use App\Admin\Repositories\Parking;
use App\Models\Except;
use App\Models\Group;
use App\Models\Parking as ModelsParking;
use App\Models\Wash;
use App\Services\AutopassService;
use App\Services\LineService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WashController extends Controller
{
    //

    public function payFake()
    {

        //最近5比 wash
        $washes = Wash::orderBy('id', 'desc')->take(5)->get();


        foreach ($washes as $wash) {
            echo  '<h1><a href="' . url("wash/$wash->id/pay_trigger") . '">pay for ' . $wash->id . '<a></h1>';
        }
    }
    public function index(Request $request)
    {
        //洗車 form

        $parkings = ModelsParking::all();
        $data['parkings'] = $parkings->groupBy('city');
        $data['wash'] = Wash::find($request->id);

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
                'project_id' => 'required',
                'date' => 'required',
                'time' => 'required',
                'car_type' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            //dd($e->errors());
            return redirect()->back()->withErrors($e->errors());
        }
        $wash = new \App\Models\Wash($request->all());

        $wash->calculateTotalAmount();
        $wash->save();

        //reply user

        //push to group
        $group =  Group::where('type', '時間到府')->first();

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
        if (!in_array($auth_result['data']['payment_state'], ['authorized', 'paid'])) {
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

    public function setReturn(Request $request)
    {
        $input = [
            'keyword' => '返回預約申請回覆',
            'value' => $request->wash_id,
        ];
        $line = new LineController();
        $line->actionTrigger(json_decode(json_encode($input), false), 'group');
        return ['result' => true];
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
        if (in_array($wash->status, ['cancelled', 'timeout', 'pay_fail'])) {
            return view('wash.close', ['message' => '付款連結失效']);
        }

        $wash->status = 'get_pay_link';
        $data = [
            'invoice_no' => $wash->id . '_' . time(),
            'request_amount' => $wash->price,
            'callback_url' => url('wash/' . $wash->id . '/pay_webhook/' . OrderToken::token($wash->id)),
            'plate_number' => strtoupper($wash->license)
        ];

        $wash->pay_data = $data;
        $wash->save();
        $autopass = new AutopassService();
        $pay_result = $autopass->makePay($data);
        $wash->pay_result =  $pay_result;
        $wash->save();


        try {
            return redirect()->to($wash->pay_result['data']['payment_url']);
        } catch (\Exception $e) {
            return view('wash.close', ['message' => '付款連線失敗，請與Autopass聯繫' . $wash->pay_result['error']]);
        }
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
        //扣掉 除外的時間
        $washes = Except::where('date', $date)->get();
        foreach ($washes as $wash) {
            foreach ($wash->time as $time) {
                $key = array_search($time, $available_times);
                unset($available_times[$key]);
            }
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

        $result = [];
        $change_time = false;
        for ($i = 1; $i <= 3; $i++) {


            if (!empty($request->input('time' . $i))) {

                $data = [
                    'date' =>  $request->input('date' . $i),
                    'time' => $request->input('time' . $i),
                ];
                $result[] = $data;
                $change_time = true;
            }
        }
        $wash = \App\Models\Wash::find($request->id);
        $wash->suggest_time = $result;
        $wash->save();

        $Line = new LineController();
        if ($request->car_type != $wash->car_type) {
            $wash->car_type = $request->car_type;
            $wash->save();

            if ($change_time) {
                $inputText = '@送出調整時間及車型 ' . $request->id;
            } else {
                $inputText = '@送出調整車型 ' . $request->id;
            }
        } else {
            if ($change_time) {
                $inputText = '@送出調整時間 ' . $request->id;
            } else {
                $inputText = '@返回預約申請回覆 ' . $request->id;
            }
        }
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

        return view('wash.close', ['message' => '調整已送出']);
    }

    public function getProjects()
    {
        $projects = \App\Models\Project::where('status', 1)->get();
        //將 id 設為 key
        $projects = $projects->keyBy('id');


        return ['result' => true, 'projects' => $projects];
    }

    public function getAdditions()
    {
        $additions = \App\Models\Addition::where('status', 1)->get();
        //將 id 設為 key
        $additions = $additions->keyBy('id');

        return ['result' => true, 'additions' => $additions];
    }

    public function payFailed(Wash $wash)
    {
        //洗車 form
        $wash->status = 'pay_fail';
        $wash->save();
        $input = [
            'keyword' => '付款失敗',
            'value' => $wash->id,
        ];
        $line = new LineController();
        $line->actionTrigger(json_decode(json_encode($input), false), 'customer');
        return view('wash.close', ['message' => '付款失敗']);
    }
}
