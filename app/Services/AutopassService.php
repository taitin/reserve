<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AutopassService
{
    private $url = '';
    private $access_token = '';

    public function __construct()
    {
        $this->url = config('autopass.url') . '/v2/';
        $this->access_token = config('autopass.access_token');
    }

    public function makePay($data)
    {
        //建立一個 http post with   auth bear token
        $url = $this->url . '/invoices';
        $data = [
            'transaction_type' => 'wash_car',
            'capture_method' => 'manual',
            'invoice_no' => $data['invoice_no'],
            'request_amount' => $data['request_amount'],
            'callback_url' => $data['callback_url'],
            'use_special_plate_number' => false
        ];
        $ret =  Http::withToken($this->access_token)->post($url, $data);
        return $ret->json();
    }

    public function getPayResult($invoice_no)
    {
        $url = $this->url . '/invoices/' . $invoice_no;
        $ret =  Http::withToken($this->access_token)->get($url);
        return $ret->json();
    }
}
