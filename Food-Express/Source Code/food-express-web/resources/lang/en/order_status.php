<?php

use App\Enums\OrderStatus;

return [

    OrderStatus::PENDING    => "Pending",
    OrderStatus::CANCEL     => "Cancel",
    OrderStatus::REJECT     => "Reject",
    OrderStatus::ACCEPT     => "Accept",
    OrderStatus::PROCESS    => "Process",
    OrderStatus::ON_THE_WAY => "On the Way",
    OrderStatus::COMPLETED  => "Completed",

];
