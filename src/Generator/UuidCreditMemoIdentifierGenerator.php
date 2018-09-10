<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Generator;

use Ramsey\Uuid\Uuid;

final class UuidCreditMemoIdentifierGenerator implements CreditMemoIdentifierGeneratorInterface
{
    public function generate(): string
    {
        return Uuid::getFactory()->uuid4()->toString();
    }
}
