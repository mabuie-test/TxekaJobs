<?php

namespace App\Providers;

use App\Services\Payments\MockPaymentGateway;
use App\Services\Payments\MpesaPaymentGateway;
use App\Services\Payments\PaymentGatewayInterface;
use Illuminate\Support\ServiceProvider;
use Karson\MpesaPhpSdk\Mpesa; // library from https://github.com/karson/mpesa-php-sdk

class PaymentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaymentGatewayInterface::class, function () {
            $driver = config('payments.driver', 'mock');

            if ($driver === 'mpesa') {
                $config = config('payments.mpesa');

                $mpesaClient = new Mpesa([
                    'environment' => $config['environment'],
                    'api_key' => $config['api_key'],
                    'public_key' => $config['public_key'],
                    'service_provider_code' => $config['service_provider_code'],
                    'initiator_identifier' => $config['initiator_identifier'],
                    'security_credential' => $config['security_credential'],
                    'short_code' => $config['default_short_code'],
                    'callback_url' => $config['callback_url'],
                ]);

                return new MpesaPaymentGateway($mpesaClient);
            }

            return new MockPaymentGateway();
        });
    }
}
