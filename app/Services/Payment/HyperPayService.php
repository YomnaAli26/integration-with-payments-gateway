<?php
namespace App\Services\Payment;

use GuzzleHttp\Client;


class HyperPayService extends BaseService
{

    public function __construct()
    {
        $client = new Client();
        parent::__construct($client,config('services.hyperPay.base_url'), [
            'Authorization' => 'Bearer ' . config('services.hyperPay.access_token'),
            'Content-Type' => 'application/x-www-form-urlencoded'
        ]);
    }



    public function initiatePayment($data)
    {
        return $this->buildRequest('POST', '/v1/checkouts', $data);
    }

    public function getPaymentStatus($resourcePath)
    {
        $entityId = http_build_query(['entityId' => config('services.hyperPay.entity_id')]);
        return $this->buildRequest('GET', "$resourcePath?$entityId");
    }
}
