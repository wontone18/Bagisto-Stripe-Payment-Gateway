<?php

namespace Wontonee\Stripe\Payment;

use Webkul\Payment\Payment\Payment;

class Stripe extends Payment
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $code  = 'stripe';

    public function getRedirectUrl()
    {
        return route('stripe.process');
        
    }
}