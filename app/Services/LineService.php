<?php

namespace App\Services;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\GoogleController;
use App\Models\Member;
use App\Models\PushMessage;
use App\Services\BubbleContainerBuilder as ServicesBubbleContainerBuilder;
use Google\Service\CloudHealthcare\Message;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\BoxComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ButtonComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ImageComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\TextComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\BubbleContainerBuilder;
use LINE\LINEBot\MessageBuilder\FlexMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\MessageBuilder\RawMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use LINE\LINEBot\MessageBuilder\Flex\BlockStyleBuilder;
use LINE\LINEBot\MessageBuilder\Flex\BubbleStylesBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;

class LineService
{
    public $line = [];

    public function __construct()
    {
        $this->line =     getLineSetting();
    }


    public function getLoginBaseUrl()
    {


        // 組成 Line Login Url
        $url = config('line.authorize_base_url') . '?';
        $url .= 'response_type=code';
        $url .= '&client_id=' .  $this->line['login_channel_id'];
        $url .= '&redirect_uri=' . urlencode(env('APP_URL') . '/line/callback');
        $url .= '&state=test'; // 暫時固定方便測試
        $url .= '&scope=openid%20profile';
        return $url;
    }

    public function getLineToken($code)
    {

        $client = new Client();
        $response = $client->request('POST', config('line.get_token_url'), [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => config('app.url') . '/line/callback',
                'client_id' =>   $this->line['login_channel_id'],
                'client_secret' =>   $this->line['login_secret']
            ]
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    public function getUserProfile($token)
    {
        $client = new Client();
        $headers = [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ];
        $response = $client->request('GET', config('line.get_user_profile_url'), [
            'headers' => $headers
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }
    public function getContent($message, $folder = '', $key = true, $groupId = '')
    {
        $text  = $message[$message['type']] ?? '';
        $use_folder = false;
        if ($key != false) {
            if ($message['type'] == 'image') {
                return $this->getImage($message['id'], $folder, $use_folder);
            } elseif ($message['type'] == 'sticker') {
                return 'https://stickershop.line-scdn.net/stickershop/v1/sticker/' . $message['stickerId'] . '/android/sticker.png';
            } else if ($message['type'] == 'video') return $this->getImage($message['id'], $folder, $use_folder);
            else if ($message['type'] == 'audio') return $this->getImage($message['id'], $folder, $use_folder);
        }

        return $message[$message['type']] ?? '';
    }
    public function getImage($message_id, $folder, $use_folder = false)
    {

        $folder = 'wash';
        $response =  $this->getMessage($message_id);
        $path = $folder . '/' . $message_id . '.jpg';
        Storage::disk('public')->put($folder . '/' . $message_id . '.jpg', $response);


        return $path;
    }



    public function getMessage($message_id)
    {


        $header = array(
            // 'Content-Type: application/json',
            'Authorization: Bearer ' . $this->line['bot_access_token'],
            // 'User-Agent: LINE-BotSDK-PHP/7.6.1'
        );
        $context = stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'method' => 'GET',
                'header' => implode("\r\n", $header),
            ],
        ]);

        $response = file_get_contents(config('line.bot_data_api_url') . '/message/' . urlencode($message_id) . '/content', false, $context);

        if (strpos($http_response_header[0], '200') === false) {
            error_log('Request failed: ' . $response);
        }

        //存成圖檔


        return ($response);
    }





    public function getUser($user_id)
    {
        $httpClient = new CurlHTTPClient($this->line['bot_access_token']);
        $LINE = new LINEBot($httpClient, ['channelSecret' => $this->line['bot_secret']]);
        $response =  $LINE->getProfile($user_id);
        return json_decode($response->getRawBody(), false);
    }


    public function getGroup($group_id)
    {
        $client = new Client();
        $headers = [
            'Authorization' => 'Bearer ' . $this->line['bot_access_token'],
            'Accept'        => 'application/json',
        ];
        $response = $client->request('GET', config('line.bot_api_url') . '/group/' . urlencode($group_id) . '/summary', [
            'headers' => $headers
        ]);
        return json_decode($response->getBody()->getContents());
    }

    /**
     * pushMessage
     *
     * @param  mixed $social_id
     * @param  mixed $data
     * [
     * 'message'=>'send message',
     * 'url' =>'link url',
     * 'image' =>'image',
     * 'text_buttons'=>[
     * ['label'=>'button label','text'=>'button text'],
     * ['label'=>'button label','text'=>'button text'],
     * ]
     * ]
     *  @return void
     */
    public function pushMessage($social_id, $data)
    {

        if (!isset($social_id)) return 'argument fault' . $social_id . json_encode($data);
        $messageBuilder  =  $this->setMessageUI($data);
        $httpClient = new CurlHTTPClient($this->line['bot_access_token']);
        $LINE = new LINEBot($httpClient, ['channelSecret' => $this->line['bot_secret']]);
        $response =  $LINE->pushMessage($social_id,  $messageBuilder);
        if (isset($response)) {
            if ($response->isSucceeded()) {
                (new PushMessage([
                    'social_id' => $social_id,
                    'content' =>  $messageBuilder->buildMessage(),
                    'result' => 'Succeeded'
                ]))->save();

                return 'Succeeded!';
            }
            (new PushMessage([
                'social_id' => $social_id,
                'content' =>   $messageBuilder->buildMessage(),
                'result' =>  $response->getHTTPStatus() . ' ' . $response->getRawBody()
            ]))->save();

            // Failed
            return  $response->getHTTPStatus() . ' ' . $response->getRawBody();
        }
    }

    /**
     * replyMessage
     *
     * @param  mixed $data[
     * 'reply_token=>'reply_token
     * 'message'=>'send message',
     * 'url' =>'link url',
     * 'image' =>'image',
     * ]
     * @return void
     */
    public function replyMessage($reply_token, $inputs)
    {

        if (!isset($reply_token)) return 'reply_token argument fault';

        $multiMessageBuilder = new MultiMessageBuilder();
        foreach ($inputs as $data) {
            $multiMessageBuilder->add($this->setMessageUI($data));
        }

        $httpClient = new CurlHTTPClient($this->line['bot_access_token']);
        $LINE = new LINEBot($httpClient, ['channelSecret' => $this->line['bot_secret']]);
        $response =  $LINE->replyMessage($reply_token, $multiMessageBuilder);
        if (isset($response)) {
            if ($response->isSucceeded()) {
                (new PushMessage([
                    'reply_token' => $reply_token,
                    'content' => $multiMessageBuilder->buildMessage(),
                    'result' => 'Succeeded'
                ]))->save();



                return 'Succeeded!';
            }
            (new PushMessage([
                'reply_token' => $reply_token,
                'content' => $multiMessageBuilder->buildMessage(),
                'result' =>  $response->getHTTPStatus() . ' ' . $response->getRawBody()
            ]))->save();


            // Failed
            return  $response->getHTTPStatus() . ' ' . $response->getRawBody();
        }
    }


    /**
     * setMessageUI
     *
     * @param  mixed $data
     * @return
     */
    private function setMessageUI($data)
    {

        $content = [];
        if (isset($data['url']) || isset($data['text_buttons'])) {
            $content[] =  new TextComponentBuilder($data['message'], null, 'sm', null, 'start', null, true, null, null);

            $body = new BoxComponentBuilder('vertical', $content, null, 'md', 'md');
            $content = [];
            if (!empty($data['url'])) {
                $actionBuilder = new UriTemplateActionBuilder($data['url_text'] ?? '請點我', ($data['url']));
                $content[] =  new ButtonComponentBuilder($actionBuilder, null, 'none', 'sm', null, '#336666');
            }
            if (!empty($data['text_buttons'])) {

                foreach ($data['text_buttons'] as $button) {
                    if (empty($button['label']) || empty($button['text'])) continue;
                    $actionBuilder = new MessageTemplateActionBuilder($button['label'], $button['text']);
                    $content[] =  new ButtonComponentBuilder($actionBuilder, null, 'lg', 'sm',  $button['style'] ?? 'secondary', $button['color'] ?? null);
                }
            }
            $footer = new BoxComponentBuilder('vertical', $content, null, 'md', 'md');


            $body_stye = new BlockStyleBuilder($data['bg_color'] ?? null);
            $style = new BubbleStylesBuilder(null, null, $body_stye, null);
            $containerBuilder = new ServicesBubbleContainerBuilder('ltr', null, null, $body, $footer,  $style);

            $messageBuilder = new FlexMessageBuilder($data['message'], $containerBuilder);
            // $buttonTemplateBuilder   =  new ButtonTemplateBuilder(
            //     'hi',
            //     $data['message'],
            //     null,
            //     $content
            // );
            // $messageBuilder = new FlexMessageBuilder($data['message'], $buttonTemplateBuilder);


            // $messageBuilder->setWidth('40%');
        } else if (isset($data['image'])) {
            $messageBuilder  =  new ImageMessageBuilder($data['image'], $data['image']);
        } else if (isset($data['images'])) {
            $messageBuilder = new \LINE\LINEBot\MessageBuilder\MultiMessageBuilder();
            if (isset($data['message'])) $messageBuilder->add(new TextMessageBuilder($data['message']));

            foreach ($data['images'] as $image) {
                $imageMessageBuilder = new \LINE\LINEBot\MessageBuilder\ImageMessageBuilder($image, $image);
                $messageBuilder->add($imageMessageBuilder);
            }
        } else {
            $messageBuilder  =  new TextMessageBuilder($data['message']);
        }

        return $messageBuilder;
    }
}
