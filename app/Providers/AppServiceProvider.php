<?php

namespace App\Providers;

use App\Contracts\PaymentServiceInterface;
use App\Services\Payment\FatoorahService;
use App\Services\Payment\HyperPayService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        $this->app->bind(PaymentServiceInterface::class,function ($app,$params) {
            switch ($params['paymentType']) {
                case 'hyper-pay':
                    return new HyperPayService();
                case 'fatoorah':
                    return new FatoorahService();
                default:
                    throw new \InvalidArgumentException("Payment service [{$params['paymentType']}] is not supported.");
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
