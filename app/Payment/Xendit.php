<?php

namespace App\Payment;

use Xendit\Xendit as XenditPayment;

use Xendit\Balance;

use Xendit\Invoice;

use Carbon\Carbon;

XenditPayment::setApiKey('xnd_development_OgNrtxjd5BdOt0k0sN5svGKMQq7Gdnu8b13g0zThXAKaMKVfmvYEAunmpg');

class Xendit
{
    public static function makePayment(int $amount, string $uuid, string $description, string $email)
    {
        $params = [
            'external_id' => $uuid,
            'payer_email' => $email,
            'description' => $description,
            'amount' => $amount
        ];

        $createInvoice = Invoice::create($params);

        return $createInvoice;
    }
}
