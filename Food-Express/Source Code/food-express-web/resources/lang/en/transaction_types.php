<?php

use App\Enums\TransactionType;

return [
    TransactionType::ADDFUND  => 'Add Fund',
    TransactionType::PAYMENT  => 'Payment',
    TransactionType::REFUND   => 'Refund',
    TransactionType::TRANSFER => 'Transfer',
    TransactionType::WITHDRAW => 'Withdraw',
];
