<?php

use App\Http\Controllers\ApiController;
use App\Models\Client;
use App\Models\Event;
use App\Models\EventsLang;
use App\Models\Language;
use App\Models\LanguageMap;
use App\Models\Setting;
use Dcat\Admin\Admin;
use GuzzleHttp\Client as GuzzleHttpClient;

if (!function_exists('myConfig')) {


    function myConfig($key)
    {

        $attrs = explode('.', $key);
        $setting = Setting::where('key', $attrs[0])->first();
        $v =   $setting->value;
        for ($i = 1; $i < count($attrs); $i++) {
            $v = $v[$attrs[$i]] ?? '';
        }

        return $v;
    }
}


if (!function_exists('storage')) {
    function storage($key)
    {
        return asset('storage/' . $key);
    }
}



if (!function_exists('version')) {
    /**
     *
     * @return  string  $iframe
     */
    function version()
    {
        return '20240521';
    }
}

/*
https://youtu.be/EMzMu9qcwzs
轉
https://www.youtube.com/embed/EMzMu9qcwzs?si=c4Wr-XY05PrLernK
*/

if (!function_exists('youtubeEmbed')) {
    /**
     *
     * @return  string  $iframe
     */
    function youtubeEmbed($url)
    {

        return str_replace('https://youtu.be/', 'https://www.youtube.com/embed/', $url);;
    }
}


if (!function_exists('getLineSetting')) {


    function getLineSetting()
    {
        return myConfig('line_message');
    }
}

// liffUrl
if (!function_exists('liffUrl')) {
    function liffUrl($url)
    {
        $append = str_replace('//', '/',  '/' . $url);
        return 'https://liff.line.me/' .  myConfig('line_message.LINE_LIFF_ID') . $append;;
    }
}


//將陣列類的字串完整網址
if (!function_exists('arrayUrl')) {
    function arrayUrl($photos)
    {
        $result = [];
        foreach ($photos as $key => $photo) {
            $result[] = asset('storage/' . $photo);
        }
        return $result;
    }
}


//車型轉換
/*
   <option value="house">轎車</option>
                <option value="5p">5人座休旅車</option>
                <option value="7p">7人座休旅車</option>
                */
if (!function_exists('carType')) {
    function carType($type)
    {
        return config('wash.car_types.' . $type) ?? '';
    }
}


if (!function_exists('zhDate')) {

    //2024-08-31 (六) 10:00
    function zhDate($date)
    {
        $week = ['日', '一', '二', '三', '四', '五', '六'];
        $day = Carbon\Carbon::parse($date)->dayOfWeek;
        return Carbon\Carbon::parse($date)->format('Y-m-d') . ' (' . $week[$day] . ') ' . Carbon\Carbon::parse($date)->format('H:i');
    }
}


if (!function_exists('countMatchingItems')) {

    function countMatchingItems(array $array1, array $array2): int
    {
        $count = 0;
        foreach ($array1 as $item) {
            if (in_array($item, $array2)) {
                $count++;
            }
        }
        return $count;
    }
}



if (!function_exists('getBusinessTimes')) {

    function getBusinessTimes($date = null)
    {
        if (empty($date)) {
            $date = date('Y-m-d');
        }
        $week_day = Carbon\Carbon::parse($date)->dayOfWeek;
        $business_times =
            config('wash.business_times_by_day.' . $week_day) ?? config('wash.business_times');


        return $business_times;
    }
}
