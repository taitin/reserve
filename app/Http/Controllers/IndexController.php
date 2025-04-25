<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IndexController extends Controller
{
    //

    public function index(Request $request)
    {
        dd(env('APP_ENV'));
        if (env('APP_ENV') == 'production') {
            return view('wash.close', ['message' => '本系統已經停止服務。']);
        }
        if ($request->to) return redirect($request->to);




        if (isset($_SERVER['QUERY_STRING'])) {
            $r = explode('liff.state=', $_SERVER['QUERY_STRING']);
            if (isset($r[1])) {
                $url = explode('&', $r[1]);
                if (isset($url[0])) {
                    if (isset($url[0]) && !strpos($url[0], '?liff.hback')) {
                        return redirect($request->to ?? urldecode($url[0]));
                    }
                }
            }
        }

        return redirect()->to('wash/portal?' . $_SERVER['QUERY_STRING']);
    }
}
