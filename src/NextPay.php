<?php

namespace Armincms\NextPay;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\{Text, Boolean};
use Armincms\Arminpay\Contracts\{Gateway, Billing}; 
use Shetabit\Payment\Facade\Payment;
use Shetabit\Multipay\Invoice;

class NextPay implements Gateway
{ 
    /**
     * The gateway configuration values.
     * 
     * @var array
     */
    public $config = [];    

    /**
     * Construcy the instance.
     * 
     * @param array $config 
     */
    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Make payment for the given Billing.
     * 
     * @param  \Illuminate\Http\Request  $request  
     * @param  \Armincms\Arminpay\Contracts\Billing $billing  
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @throws \InvalidArgumentException
     */
    public function pay(Request $request, Billing $billing)
    {    
        return (string) Payment::via('nextpay')
                        ->config(array_merge($this->getConfigurations(), [
                        	'callbackUrl' => $billing->callback(),
                        ]))
                        ->purchase($this->newInvoice($billing))
                        ->callbackUrl($billing->callback())
                        ->pay();
    } 

    public function newInvoice(Billing $billing)
    {
        return tap(new Invoice, function($invoice) use ($billing) {
            $invoice->amount(intval($billing->amount()))
                    ->uuid($billing->getIdentifier());
        });
    }

    /**
     * Verify the payment for the given Billing.
     * 
     * @param  \Illuminate\Http\Request  $request  
     * @param  \Armincms\Arminpay\Contracts\Billing $billing  
     * @return \Symfony\Component\HttpFoundation\Response
     * 
     * @throws \InvalidArgumentException
     */
    public function verify(Request $request, Billing $billing)
    {
        return Payment::amount(intval($billing->amount()))
                    ->via('nextpay')
                    ->config($this->getConfigurations())
                    ->verify()
                    ->getReferenceId();
    } 
 
    /**
     * Returns configuration fields.
     * 
     * @return array 
     */
    public function fields(Request $request): array
    {
        return [ 
            Text::make('Api Key', 'merchantId')
                ->help(__('Please enter the given the NextPay API key.'))
                ->required()
                ->rules('required'),  
        ];
    }  

    public function getConfigurations()
    {   
        return $this->config; 
    }
}
