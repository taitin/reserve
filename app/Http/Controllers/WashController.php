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
                'parking' => 'required',
                'entry_time' => 'required',
                'exit_time' => 'required',
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
        $message = "洗車申請\n";
        $message .= "電話: " . $request->phone . "\n";
        $message .= "車牌: " . $request->license . "\n";


        $message .= "停車位: " . $request->parking . "\n";
        $message .= "進場時間: " . $request->entry_time . "\n";
        $message .= "出場時間: " . $request->exit_time . "\n";

        $message .= "車款: " . $request->model . "\n";
        $message .= "客戶自填車型: " . carType($request->car_type) . "\n";
        if (!empty($request->addition_services))
            $message .= "額外加值服務: " . implode(',', $request->addition_services) . "\n";

        //金額試算
        $message .= "總金額: " . $wash->price . "\n";



        $group->pushMessage([
            'message' => $message,
            'text_buttons' => [
                ['label' => '同意安排洗車', 'text' => '@同意洗車 ' . $wash->id],
                ['label' => '同意洗車，但須調整價格', 'text' => '@調整價格 ' . $wash->id],
                ['label' => '拒絕洗車', 'text' => '@拒絕洗車 ' . $wash->id],
            ]
        ]);


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



        return view('wash.close', ['message' => '洗車申請已送出']);
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
}
