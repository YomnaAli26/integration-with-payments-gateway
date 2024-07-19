<?php

namespace App\Services;


use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class PaymentsService
{
    public string $url = 'https://eu-prod.oppwa.com/v1/checkouts';
    public string $payment_url = 'https://eu-prod.oppwa.com/v1/registrations';

    public function create_request($data,$order)
    {
        $payment_data = [
            'entityId' => $this->getEntityId($data['methods']),
            'amount' =>number_format((float) $data['amount'],2,'.',''),
            'currency' => 'SAR',
            'customParameters[3DS2_enrolled]' => true,

            'customer.email' => auth()->user()->email,
            'billing.street1' => $data['address'],
            'billing.city' => toExists('city',$data) ? $data['city'] : '',
            'billing.state' => toExists('state',$data) ? $data['state'] : '',
            'billing.country' => 'SA',
            'billing.postcode' => $data['zip'],
            'customer.givenName' => $data['fname'],
            'customer.surname' => $data['lname'],
            'merchantTransactionId' => $data['merchantTransactionId'],
            'paymentType' => 'DB',

            'shopperResultUrl' => route('checouts.checkPaymentStatus')
        ];



        $res = Http::withToken(env('payment_token'))->asForm()->post($this->url, $payment_data);

        $id = '';
        if (isset($res->json()['id'])) {
            $id = $res->json()['id'];

        }
        return $id;
    }

    public function payment($data)
    {
        if(strlen($data['expiryMonth'])==1){
            $data['expiryMonth']='0'. $data['expiryMonth'];
        }
        if(strlen($data['expiryYear'])==2){
            $data['expiryYear']='20'. $data['expiryYear'];
        }
        $payment_data = [
            'entityId' => $this->getEntityId($data['methods']),
            'amount' => number_format(round($data['amount'], 2), 2, '.', ''),
            'currency' => 'SAR',
            'customParameters[3DS2_enrolled]' => true,
            'customer.email' => auth()->user()->email,
            'billing.street1' => $data['address'],
            'billing.city' => toExists('city',$data) ? $data['city'] : '',
            'billing.state' => toExists('state',$data) ? $data['state'] : '',
            'billing.country' => 'SA',
            'billing.postcode' => $data['zip'],
            'customer.givenName' => $data['fname'],
            'customer.surname' => $data['lname'],
            'merchantTransactionId' => $data['merchantTransactionId'],

            'paymentBrand' => $data['methods'],
            'card.number' => $data['card_number'],
            'card.holder' => $data['holder_name'],
            'card.expiryMonth' =>   $data['expiryMonth'],
            'card.expiryYear' => $data['expiryYear'],
            'card.cvv' => $data['cvv'],

        ];
        if (!$data['methods'] == 'MADA') {

            $payment_data['testMode'] = 'EXTERNAL';

        }
        $res = Http::withToken(env('payment_token'))->asForm()->post($this->payment_url, $payment_data);

        return $res;

    }

    public function getEntityId($method)
    {
        $entityId = env('visaMasterEntityId');
        if ($method == 'MADA') {

            $entityId = env('madaEntityId');


        }
        return $entityId;
    }

    public function checkPaymentStatus()
    {
        $id = $_GET['id'];

        $payment_data = Cache::get('order' . auth()->user()->id);

        $entityId = $this->getEntityId($payment_data['data']['methods']);

        $res = Http::withToken(env('payment_token'))->asForm()->get('https://eu-prod.oppwa.com/v1/checkouts/' . $id . '/payment', [

            'entityId' =>$entityId,

        ]);
        $res = $res->json();
        return $res;


    }

    public function checkPaymentStatusSecound()
    {
        $id = $_GET['id'];

        $payment_data = Cache::get('order' . auth()->user()->id);

        $entityId = $this->getEntityId($payment_data['data']['methods']);

        $res = Http::withToken(env('payment_token'))->asForm()->get(' https://eu-prod.oppwa.com/v1/payments/' . $id, [

            'entityId' => $entityId,

        ]);
        $res = $res->json();
        return $res;


    }

}

//        $url = '';
//        if (isset($res->json()['redirect'])) {
//            $redirect = $res->json()['redirect'];
//            $base_url=$redirect['url'];
//            $params=$redirect['parameters'];
//            foreach ($params as $key=>$value){
//                if($key==0){
//                    $url=$base_url.'?'.$value['name'].'='.$value['value'];
//                }else{
//                    $url.='&'.$value['name'].'='.$value['value'];
//                }
//            }
//
//        }
//        return $url;
