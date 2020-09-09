<?php

use App\Enums\PaymentMethod;

return [
    PaymentMethod::CASH_ON_DELIVERY => 'From Cash Load',
    PaymentMethod::PAYPAL           => 'From Paypal Load',
    PaymentMethod::STRIPE           => 'From Stripe Load',
];
