<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentServiceInterface;
use App\Services\Payment\FatoorahService;
use Illuminate\Http\Request;

class FatoorahController extends Controller
{
    private  $fatoorahService;
    public function __construct()
    {
        $this->fatoorahService = app()->make(PaymentServiceInterface::class,['paymentType'=>'fatoorah']);
    }
    public function payOrder()
    {
        $data =[
            'InvoiceValue'       => 55,
            'CustomerName'       => 'fname lname',
            'NotificationOption' => 'LNK',
            'CustomerEmail'      => 'email@example.com',
            'CallBackUrl'        => config('fatoorah.callback_url'),
            'ErrorUrl'           => config('fatoorah.error_url'),
            'Language'           => 'en',
        ];
        return $this->fatoorahService->initiatePayment($data);
        //transaction table
        //invoiceId - userId
    }

    public function callback(Request $request)
    {
        $data=[];
        $data['Key'] = $request->paymentId;
        $data['KeyType'] ='paymentId';
        return $this->fatoorahService->getPaymentStatus($data);
        //check invoiceId in transaction table
    }

    public function error(Request $request)
    {
        $data=[];
        $data['Key'] = $request->paymentId;
        $data['KeyType'] ='paymentId';
        return $this->fatoorahService->getPaymentStatus($data);
    }

}
