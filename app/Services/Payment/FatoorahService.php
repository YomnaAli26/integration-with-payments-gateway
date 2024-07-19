<?php

namespace App\Services\Payment;

use GuzzleHttp\Client;

class FatoorahService extends BaseService
{
    public function __construct()
    {
        $client = new Client();
        parent::__construct($client,config('fatoorah.base_url'), [
            'Content-Type' => 'application/json',
            'authorization' => 'Bearer '.config('fatoorah.token')
        ]);
    }
    public function initiatePayment($data)
    {
        return $this->buildRequest("POST","v2/SendPayment",$data);

    }

    public function getPaymentStatus($resourcePath)
    {
        return $this->buildRequest("POST","v2/getPaymentStatus",$resourcePath);

    }
}
