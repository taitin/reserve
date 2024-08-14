<?php

namespace App\Admin\Extensions\Tools;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderToken
{
    public static function token($id, $token = '')
    {
        $key = md5('wash' . $id);
        if ($token == '') return $key;
        else if ($token == $key) return true;
        else return false;
    }

    public static function tToken($id, $token, $expired = 5 * 60)
    {
        if ($id + $expired < time()) return false;
        $key = md5('wash' . $id);
        if ($token == '') return $key;
        else if ($token == $key) return true;
        else return false;
    }

    public static function  shareCode($id, $token = '')
    {
        $key = $id . '-' . substr(md5('ugo' . $id), 0, 5);
        if ($token == '') return $key;
        else if ($token == $key) return true;
        else return false;
    }

    public static function  registerCode()
    {
        $code = '';
        $num = 6;
        for ($i = 1; $i <= $num; $i++) {
            $code = $code . rand(0, 9);
        }
        session(['register_code' => $code]);
        return $code;
    }
}
