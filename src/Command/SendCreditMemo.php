<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Command;

final class SendCreditMemo
{
    /** @var string */
    private $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function id(): string
    {
        return $this->id;
    }
}
