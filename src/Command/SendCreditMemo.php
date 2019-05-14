<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Command;

final class SendCreditMemo
{
    /** @var string */
    private $number;

    public function __construct(string $number)
    {
        $this->number = $number;
    }

    public function number(): string
    {
        return $this->number;
    }
}
