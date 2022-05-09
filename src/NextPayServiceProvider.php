<?php

namespace Armincms\NextPay;

use Illuminate\Contracts\Support\DeferrableProvider; 
use Illuminate\Support\ServiceProvider; 

class NextPayServiceProvider extends ServiceProvider implements DeferrableProvider
{   
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        app('arminpay')->extend('nextpay', function($app, $config) {
            return new NextPay($config);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    /**
     * Get the events that trigger this service provider to register.
     *
     * @return array
     */
    public function when()
    {
        return [
            \Armincms\Arminpay\Events\ResolvingArminpay::class
        ];
    }
}
