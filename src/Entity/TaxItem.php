<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

class TaxItem implements TaxItemInterface
{
    protected ?int $id = null;

    protected string $label;

    protected int $amount;

    public function __construct(string $label, int $amount)
    {
        $this->label = $label;
        $this->amount = $amount;
    }

    public function getId(): ?int
    {
        return $this->id();
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function amount(): int
    {
        return $this->amount;
    }
}
