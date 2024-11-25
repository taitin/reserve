<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AutopassService
{
    private $url = '';
    private $access_token = '';

    public function __construct()
    {
        if (env('AUTOPASS_ENV', 'production') == 'sandbox') {
            $this->url = config('autopass.sandbox_url') . '/v2';
            $this->access_token = config('autopass.sandbox_token');
        } else {
            $this->url = config('autopass.url') . '/v2';
            $this->access_token = config('autopass.access_token');
        }
    }

    public function makePay($data)
    {
        //建立一個 http post with   auth bear token
        $url = $this->url . '/invoices';
        $data = [
            'transaction_type' => 'wash_car',
            'capture_method' => $data['capture_method'] ?? 'manual',
            // 'capture_method' => 'automatic',

            'invoice_no' => $data['invoice_no'],
            'request_amount' => $data['request_amount'],
            'callback_url' => $data['callback_url'],
            'use_special_plate_number' => $data['use_special_plate_number'] ?? false,
            'plate_number' => $data['plate_number'] ?? '',

            // 'request_description'=>'交易細節˙
        ];
        $ret =  Http::withToken($this->access_token)->post($url, $data);
        return $ret->json();
    }


    public function cancelPay($invoice_no)
    {
        $url = $this->url . '/invoices/' . $invoice_no . '/cancel';
        $ret =  Http::withToken($this->access_token)->post($url);
        return $ret->json();
    }

    public function getPayResult($invoice_no)
    {
        $url = $this->url . '/invoices/' . $invoice_no;
        $ret =  Http::withToken($this->access_token)->get($url);
        return $ret->json();
    }
}
