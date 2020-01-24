<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Context\Transform;

use Behat\Behat\Context\Context;

final class PriceContext implements Context
{
    /**
     * @Transform /^"(\-)?((?:\d+\.)?\d+)"$/
     */
    public function getPriceFromString(string $sign, string $price): int
    {
        $this->validatePriceString($price);

        $price = (int) round((float) $price * 100, 2);

        if ('-' === $sign) {
            $price *= -1;
        }

        return $price;
    }

    /** @throws \InvalidArgumentException */
    private function validatePriceString(string $price): void
    {
        if (!(bool) preg_match('/^\d+(?:\.\d{1,2})?$/', $price)) {
            throw new \InvalidArgumentException('Price string should not have more than 2 decimal digits.');
        }
    }
}
