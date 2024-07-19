<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentServiceInterface;
use App\Models\Transaction;
use App\Services\Payment\HyperPayService;

class HyperPayController extends Controller
{
    protected $hyperPayService;

    public function __construct()
    {
        $this->hyperPayService = app()->make(PaymentServiceInterface::class,['paymentType'=>'hyper-pay']);
    }

    public function payOrder()
    {
        $data = [
            'entityId' => config('services.hyperPay.entity_id'),
            'amount' => '92.00',
            'currency' => 'EUR',
            'paymentType' => 'DB',
        ];
        $responseData = $this->hyperPayService->initiatePayment($data);

        if (isset($responseData['id'])) {
            $checkoutId = $responseData['id'];
            Transaction::query()->create([
                'checkout_id' => $checkoutId,
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'status' => 'pending',

            ]);
            return view('pay', compact('checkoutId'));
        }

        return response()->json($responseData);
    }

    public function getPaymentStatus($checkoutId)
    {

        $transaction = Transaction::where('checkout_id', $checkoutId)->first();
        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        $response = $this->hyperPayService->getPaymentStatus("/v1/checkouts/{$checkoutId}/payment");
        if (isset($response['error'])) {
            return response()->json(['error' => $response['error']], 400);
        }

        if ($response['result']['code'] == '000.100.110') {
            $transaction->status = 'success';
            $transaction->save();
        }


        return response()->json($response);
    }

}
