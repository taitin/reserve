<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\Group;
use App\Models\Message;
use App\Models\Product;
use App\Models\ProductModel;
use App\Models\Wash;
use App\Services\LineService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LineController extends Controller
{
    //
    public function __construct() {}





    public function sandBox(Request $request)
    {
        $inputText = $request->message;
        $type = $request->type;
        $keywords =  $this->fetchKeyword($inputText,    $type);
        $input = [
            'name' => $user->displayName ?? '匿名使用者',
            'group_name' => 'test',
            'group_id' => $groupId ?? '',
            'message_type' =>  $type,
            'social_id' => $socialId ?? '',
            'date' => date('Y-m-d H:i:s'),
            'text' =>  $inputText,
            'keyword' => implode(',', array_keys($keywords)),
            'value' => implode(',', array_column($keywords, 'value')),
            'message_id' => $events[0]['message']['id'] ?? '',
            'reply_token' =>  $replyToken ?? '',
            'quotedMessageId' => $quotedMessageId ?? ''

        ];
        return   $this->saveRecord($input, $type);
    }


    public function LineCallback(Request $request)
    {
        $events = $request->events;

        // $events = json_decode('[{"type":"message","message":{"type":"text","id":"488126177878212660","quotedMessageId":"488077650032590904","quoteToken":"IMykhWUG8-5nvyFek-koj_wqg4NQqccA5EZ0WIB9G_I839j2JexAU5Dj-jW-V38-0HysueLxUTOB44krwO8zpeFx69McAtygA6wSwHTg7hxSzc8Ok1vL0Wnr4hLTyYxAdClmCowuPagO4yBUD3S4IA","text":"@\u7a0d\u5f8c\u56de\u8986"},"webhookEventId":"01HJRGH5D0Q8K96GYB3Z1A9X5X","deliveryContext":{"isRedelivery":false},"timestamp":1703777178536,"source":{"type":"group","groupId":"C506c9fa45c2017359b3c169da8c99468","userId":"U43ce048d8409b3d0ebe641feec57ed62"},"replyToken":"799d16e3bd2b48b6acb8ed8ab9ae35a5","mode":"active"}]', true);
        $replyToken = $events[0]['replyToken'] ?? '';
        $line = new LineService();
        $text = [];
        $socialId = $events[0]['source']['userId'] ?? '';
        $groupId = $events[0]['source']['groupId'] ?? '';
        $type = $events[0]['source']['type'] ?? '';
        if ($type == 'group') {
            $group_ret = $this->lineGroupBindCheck($groupId);
        }


        foreach ($events as $event) {
            if (isset($event['message'])) {
                $text[] =  $line->getContent($event['message'], '', true, $groupId);
                if (in_array($event['message']['type'], ['image', 'video', 'audio', 'sticker'])) {
                    $is_media = true;
                } else $message = '';
            } else $message = '';
        }

        $inputText  = implode("\n", $text);
        $quotedMessageId = $events[0]['message']['quotedMessageId'] ?? '';



        if ($type == 'group') {
            $ret = $group_ret ?? $this->lineGroupBindCheck($groupId);
            $account = $ret->first()->{myConfig('line.bind.group.account')} ?? '';
            if ($ret->count() == 0) {
                $line = new LineService();
                $group = $line->getGroup($groupId);
                $name = $group->groupName;
                $this->setGroupLineBind($groupId, $name);
                $message = '這是你的首次使用，請通知管理員綁定群組類型，方可進行其他操作';
            } else {
                if (empty($ret->first()->type)) {
                    $message = '群組尚未綁定，請通知管理員綁定群組類型';
                } else {
                    try {
                        if (isset($is_media) && $is_media) {
                            //確認最後一筆 message 是否關鍵字是 洗前照片 或 洗後照片
                            $message =  Message::where('group_type', 'group')
                                ->where('message_type', '!=', 'image')
                                ->orderBy('created_at', 'desc')->first();
                            if (in_array($message->keyword, ['洗前照片', '洗後照片'])) {
                                $wash = Wash::find($message->value);
                                if ($message->keyword == '洗前照片') {
                                    $a = $wash->before_photos ?? [];
                                    $b = explode("\n", $inputText);
                                    $photos = array_merge($a, $b);
                                    $wash->before_photos = $photos;
                                } else {
                                    $a = $wash->after_photos ?? [];
                                    $b = explode("\n", $inputText);
                                    $photos = array_merge($a, $b);
                                    $wash->after_photos = $photos;
                                }
                                $wash->save();
                                $msg = count($photos) . '張照片上傳完畢，都上傳完成後請點選送出照片';
                                $replys = [[
                                    "message" => $msg,
                                    'text_buttons' => [
                                        ['label' => '送出' . $message->keyword, 'text' => '@送出' . $message->keyword . ' ' . $wash->id],
                                    ]

                                ]];
                                $this->replyMessage($groupId, $replys, 'group');
                            } else {

                                $message = '請先點選上傳照片的按鈕,再上傳照片';
                                $replys = [["message" => $message]];
                            }
                        }


                        $group_name = $ret->first()->name;
                        $keywords =  $this->fetchKeyword($inputText,  $type);
                        $line = new LineService();
                        $user = $line->getUser($socialId);
                        $input = [
                            'name' => $user->displayName ?? '匿名使用者',
                            'group_name' => $group_name,
                            'group_id' => $groupId,
                            'message_type' => $events[0]['message']['type'],
                            'social_id' => $socialId,
                            'date' => date('Y-m-d H:i:s', $events[0]['timestamp'] / 1000),
                            'text' =>  $inputText,
                            'keyword' => implode(',', array_keys($keywords)),
                            'value' => implode(',', array_column($keywords, 'value')),
                            'message_id' => $events[0]['message']['id'] ?? '',
                            'reply_token' =>  $replyToken ?? '',
                            'quotedMessageId' => $quotedMessageId ?? ''

                        ];

                        $this->saveRecord($input, $type);
                    } catch (Exception $e) {

                        Log::debug($e);
                    }
                }
            }
            if (!empty($message)) {
                if (is_array($message)) $replys = $message;
                else $replys = [["message" => $message]];
                $r =  $this->replyMessage($groupId, $replys, 'group');
            }
        } else {

            if (!empty($events)) {
                $keywords =  $this->fetchKeyword($inputText);
                $line = new LineService();
                $user = $line->getUser($socialId);
                $input = [
                    'name' => $user->displayName ?? '',
                    // 'project_name' => $project_name,
                    // 'group_id' => $group_name,
                    'social_id' => $socialId,
                    'date' => date('Y-m-d H:i:s', $events[0]['timestamp'] / 1000),
                    'text' =>  $inputText,
                    'message_type' => $events[0]['message']['type'],
                    'keyword' => implode(',', array_keys($keywords)),
                    'value' => implode(',', array_column($keywords, 'value')),
                    'message_id' => $events[0]['message']['id'] ?? '',
                    'reply_token' =>  $replyToken ?? '',
                    'quotedMessageId' => $quotedMessageId ?? ''

                ];
                $this->saveRecord($input);
            }

            // $message = $this->doActions($keywords, $input['message_id']);
        }
    }


    public  function lineBindCheck($social_id)
    {
        $ragic = (new ApiController());
        //check group
        $r =  $ragic->getData(
            myConfig('line.bind.work.sheet'),
            myConfig('line.bind.work.table'),
            '',
            [myConfig('line.bind.work.social_id') . ',eq,' . $social_id]
        );
        return $r;
    }





    public  function lineGroupBindCheck($group_id)
    {

        $r  = Group::where('group_id', $group_id)->get();
        return $r;
    }


    public  function fetchKeyword($text, $type = 'customer')
    {




        $pattern = '/@(\S+)(?:\s+([\d\p{L}]+))?/u';
        preg_match_all($pattern, $text, $matches, PREG_SET_ORDER);

        $result = [];




        foreach ($matches as $match) {
            $key = $match[1];
            $value = trim(str_replace('@' . $key, '', $text)) ?? '';
            // = $match[2] ?? ''; // 如果沒有匹配到第二個組，則默認為空字符串
            if (is_numeric($value)) {
                $value = intval($value); // 如果值是數字，則轉換為整數
            }
            $result[$key] = $value;
        }



        $output = [];

        foreach ($result as $key => $v) {

            $action =  $this->getKeywordAction($key, $type)->first();

            if (!empty($action)) {
                $output[$key] = [
                    'value' => $v,
                    'action' => $action
                ];
            }
        }

        return $output;
    }
    public function getReplyKeywordAction($keyword, $type)
    {
        $ragic = (new ApiController());
        //check group
        $r =  $ragic->getData(
            myConfig('line.keyword.sheet'),
            myConfig('line.keyword.table'),
            '',
            [
                myConfig('line.keyword.reply_keyword') . ',eq,' . $keyword,
                myConfig('line.keyword.type') . ',eq,' . $type,
            ]
        );



        return $r;
    }

    public function getKeywordAction($keyword, $type)
    {
        return  Action::where('keyword', $keyword)->where('from', $type)->get();
    }



    /**
     * saveGroupRecord
     *
     * @param  mixed $input
     * @return void
     */
    public  function saveRecord($input, $type = 'customer')
    {
        $data = [
            'name' => $input['name'],
            'group_id' => $input['group_id'] ?? '',
            'group_name' => $input['group_name'] ?? '',
            'social_id' => $input['social_id'],
            'date' => $input['date'],
            'text' => $input['text'],
            'message_type' => $input['message_type'],
            'keyword' => $input['keyword'],
            'value' => $input['value'],
            'message_id' => $input['message_id'],
            'reply_token' => $input['reply_token'],
            'group_type' => $type,

        ];

        $r =  Message::create($data);

        return  $this->actionTrigger($r, $type);
        $r;
    }
    /**
     * saveLastValue
     *
     * @param  mixed $input social_id text
     * @return void
     */
    public function saveLastValue($input, $where)
    {
        $ragic = (new ApiController());

        $lasts =  $ragic->getData(
            myConfig('line.dialog.sheet'),
            myConfig('line.dialog.table'),
            '',
            $where,
            '&limit=3'
        );


        $save = [];
        if (isset($input['text']))
            $save[myConfig('line.dialog.value')] =  $input['text'];
        if (isset($input['gps']))
            $save[myConfig('line.dialog.gps')] =  $input['gps'];

        if (isset($input['reply_token']))
            $save[myConfig('line.dialog.reply_token')] =  $input['reply_token'];

        $text = [];
        foreach ($lasts as $last) {
            if (!empty($last->{myConfig('line.dialog.keyword')})) {

                if ($last->{myConfig('line.dialog.keyword')} != '簽到' && $last->{myConfig('line.dialog.keyword')} != '簽退') return;

                if (isset($save[myConfig('line.dialog.value')])) {
                    if (!empty($last->{myConfig('line.dialog.value')}))
                        $text[] = $last->{myConfig('line.dialog.value')};
                    $text[] = $save[myConfig('line.dialog.value')];
                    $save[myConfig('line.dialog.value')] =  implode(',', $text);
                }
                $r =  $ragic->saveToRagic(
                    myConfig('line.dialog.sheet'),
                    myConfig('line.dialog.table'),
                    $save,
                    $last->_ragicId,
                );
                $this->actionTrigger($r, $last->{myConfig('line.dialog.reply_token')}, $last->{myConfig('line.dialog.group_type')}, $input);
                return $r;
            }
        }
    }

    public function actionTrigger($r, $type = 'customer')
    {
        $finish = false;
        if (!empty($r->keyword)) {

            $actions =  $this->getKeywordAction($r->keyword, $type);
            foreach ($actions as $action) {
                if (!empty($action)) {
                    $finish = true;
                    $values = explode(' ', $r->value);
                    $params = [];
                    $wash = Wash::find(end($values));
                    if (empty($wash)) $wash = new Wash();
                    if (!empty($action->do_method)) {

                        if (count($values) > 1) $params = $wash->{$action->do_method}($values);
                        else $params = $wash->{$action->do_method}();
                    }

                    $text_buttons = [];
                    if (!empty($action->text_buttons)) {

                        foreach ($action->text_buttons as $button) {
                            $text_buttons[] = [
                                'label' => $button['label'],
                                'text' => $button['text'] . ' ' . $r->value
                            ];
                        }
                    }
                    if (!empty($params['text_buttons'])) {

                        foreach ($params['text_buttons'] as $button) {
                            $text_buttons[] = [
                                'label' => $button['label'],
                                'text' => $button['text'] . ' ' . $r->value
                            ];
                        }
                        unset($params['text_buttons']);
                    }




                    //將content中的 {$name} 取代為 params[$name]
                    preg_match_all('/\{(.+?)\}/', $action->content, $matches);
                    $result_params = [];
                    foreach ($matches[1] as $key => $value) {
                        $result_params[] = $params[$value] ?? '';
                    }


                    // foreach ($matches[1] as $match) {
                    //     $params[$match] = $r->{$match} ?? '';
                    // }

                    $content = $action->content;
                    if (!empty($matches[0]) && !empty($result_params))
                        $content = str_replace($matches[0], $result_params, $action->content);

                    $message =   $content;
                    $replys = [];
                    $reply = ['message' => $message];
                    if (isset($text_buttons)) {
                        $reply['text_buttons'] = $text_buttons;
                    }
                    if (isset($params['link'])) {
                        $reply['url'] = $params['link'];
                    }
                    if (isset($params['photos'])) {
                        //將array cunk 成 5張一組 > arrayUrl($photos)
                        $photos = array_chunk($params['photos'], 5);
                        foreach ($photos as $photo) {
                            $reply['images'] =  arrayUrl($photo);
                            $replys[] = $reply;
                            $reply = [];
                        }
                    } else  $replys[] = $reply;

                    if ($action->target == 'group') {
                        $group = Group::where('type', '時間到府')->first();

                        $this->replyMessage($group->group_id,   $replys, $action->target);
                    }
                    if ($action->target == 'master') {
                        $master = $wash->master;
                        if (!empty($master))
                            $this->replyMessage($master->social_id,   $replys, $action->target);
                    } else {
                        $this->replyMessage($wash->social_id, $replys, $action->target);
                    }
                }
            }
        }
    }

    public function cronPushMessage(Request $request)
    {

        $ragic = new ApiController();
        $where = [
            myConfig('line.push.type') . ',eq,' . '排程'
        ];
        $reports =  $ragic->getData(
            myConfig('line.push.sheet'),
            myConfig('line.push.table'),
            '',
            $where
        );
        foreach ($reports as $push) {
            $n  =  $push->{myConfig('line.push.trigger_before')} ?? 0;
            $tagret = explode('/',  $push->{myConfig('line.push.form')});
            $where = [
                $push->{myConfig('line.push.trigger_date_column')} . ',eq,' . date('Y/m/d', strtotime("-" . $n . " day")),
            ];


            $messages =  $ragic->getData(
                $tagret[0] ?? '',
                $tagret[1] ?? '',
                '',
                $where
            );
            foreach ($messages as $message) {
                $this->pushMessage($message, $push);
            }
        }
    }

    public function clickPushMessage(Request $request)
    {
        $ragic = new ApiController();
        $report =  $ragic->getData(
            myConfig('line.push.sheet'),
            myConfig('line.push.table'),
            $request->ragic_id

        );
        $push = $report->first();
        $tagret = explode('/',  $push->{myConfig('line.push.form')});
        $message =  $ragic->getData(
            $tagret[0] ?? '',
            $tagret[1] ?? '',
            $request->id
        )->first();
        $this->pushMessage($message, $push);
    }

    private function pushMessage($message, $push)
    {
        $content = $push->{myConfig('line.push.content')};
        //將 $content 中的 {$name} 列成 array
        preg_match_all('/\{(.+?)\}/', $content, $matches);
        $ragic = new ApiController();

        $parm = [];
        foreach ($matches[1] as $match) {
            $parm[] = $message->{$match} ?? '';
        }
        $msg = str_replace($matches[0], $parm, $content);

        $project_name = $message->{$push->{myConfig('line.push.project_name_column')}} ?? '';

        //$line = new LineService();
        // return $line->pushMessage('C41ce05543c0734289ccd7796b85a6343', ['message' => 'hello']);

        $where = [
            myConfig('line.bind.group.project_name') . ',eq,' .   $project_name,
        ];
        $groups =  $ragic->getData(
            myConfig('line.bind.group.sheet'),
            myConfig('line.bind.group.table'),
            '',
            $where
        );
        $push_type = [];

        if ($push->{myConfig('line.push.customer_group')} == 'Yes') {
            $push_type[] = '客戶群組';
        }
        if ($push->{myConfig('line.push.work_group')} == 'Yes') {
            $push_type[] = '工班群組';
        }
        foreach ($groups as $group) {

            if (in_array($group->{myConfig('line.bind.group.type')}, $push_type)) {
                $line = new LineService();
                $line->pushMessage($group->{myConfig('line.bind.group.group_id')}, ['message' => $msg]);
            }
        }
    }



    public  function replyMessage($social_id, $inputs, $type)
    {
        if ($type == 'group') {
            //19 分鐘內
            $message = Message::where('group_id', $social_id)
                ->whereNull('replied_at')
                ->where('created_at', '>', date('Y-m-d H:i:s', strtotime('-19 minute')))
                ->orderBy('created_at', 'asc')->first();
            if ($message)
                $message_result = $message->reply($inputs);

            if (!empty($message_result)) Log::debug($message_result);
        } else {

            $message = Message::where('social_id', $social_id)
                ->whereNull('group_id')
                ->whereNull('replied_at')
                ->where('created_at', '>', date('Y-m-d H:i:s', strtotime('-19 minute')))
                ->orderBy('created_at', 'asc')->first();

            if ($message)
                $message_result = $message->reply($inputs);
        }

        if (empty($message_result) || $message_result != 'Succeeded!') {
            foreach ($inputs as $input) {
                $message_result = (new LineService())->pushMessage($social_id, $input);
            }
        }


        return $message_result;
    }



    public  function setLineBind($social_id, $name)
    {
        $ragic = (new ApiController());
        $data = [
            myConfig('line.bind.work.social_id') => $social_id,
            myConfig('line.bind.work.name') => $name
        ];

        //check 工班
        $r =  $ragic->saveToRagic(
            myConfig('line.bind.work.sheet'),
            myConfig('line.bind.work.table'),
            $data,
            '',
        );
        return $r;
    }
    public  function setGroupLineBind($group_id, $name)
    {

        $r =  Group::create([
            'group_id' => $group_id,
            'name' => $name
        ]);

        return $r;
    }


    public function doActions($keywords, $message_id)
    {
        $result = [];
        foreach ($keywords as $each) {

            if ($each['action']->first()->{myConfig('line.keyword.gps')} == 'Yes') {
            }
        }
        return $result;
    }
}
