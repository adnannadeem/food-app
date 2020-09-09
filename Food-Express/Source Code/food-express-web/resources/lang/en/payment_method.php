<?php

use App\Enums\PaymentMethod;

return [
    PaymentMethod::CASH_ON_DELIVERY => 'Cash',
    PaymentMethod::PAYPAL           => 'Paypal',
    PaymentMethod::STRIPE           => 'Stripe',
];
