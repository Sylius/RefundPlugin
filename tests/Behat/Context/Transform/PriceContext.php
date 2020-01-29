<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Context\Transform;

use Behat\Behat\Context\Context;

final class PriceContext implements Context
{
    /**
     * @Transform /^"(\-)?(\d+(?:\.\d{1,2})?)"$/
     */
    public function getPriceFromString(string $sign, string $price): int
    {
        $price = (int) round((float) $price * 100, 2);

        if ('-' === $sign) {
            $price *= -1;
        }

        return $price;
    }
}
