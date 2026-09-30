<?php

declare(strict_types=1);

use Behat\Config\Config;

return (new Config())
    ->import([
        'suites/application/refunds.php',
        'suites/ui/customer_credit_memos.php',
        'suites/ui/refunds.php',
    ])
;
