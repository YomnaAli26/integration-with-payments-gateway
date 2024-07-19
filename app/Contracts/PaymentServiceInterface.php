<?php
namespace App\Contracts;
interface PaymentServiceInterface
{
    public function initiatePayment($data);
    public function getPaymentStatus($resourcePath);


}
