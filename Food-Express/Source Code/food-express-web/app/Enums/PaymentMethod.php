<?php
/**
 * Created by PhpStorm.
 * User: dipok
 * Date: 18/4/20
 * Time: 12:42 PM
 */

namespace App\Enums;

interface PaymentMethod
{
    const CASH_ON_DELIVERY = 5;
    const PAYPAL           = 10;
    const STRIPE           = 15;
}
