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

/** @final */
class CreditMemoSequence implements CreditMemoSequenceInterface
{
    protected ?int $id = null;

    protected int $index = 0;

    protected int $version = 1;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function incrementIndex(): void
    {
        ++$this->index;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function setVersion(int $version): void
    {
        $this->version = $version;
    }
}
