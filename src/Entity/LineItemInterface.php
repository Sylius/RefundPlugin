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

use Sylius\Component\Resource\Model\ResourceInterface;

interface LineItemInterface extends ResourceInterface
{
    public function id(): ?int;

    public function name(): string;

    public function quantity(): int;

    public function unitNetPrice(): int;

    public function unitGrossPrice(): int;

    public function netValue(): int;

    public function grossValue(): int;

    public function taxAmount(): int;

    public function taxRate(): ?string;

    public function merge(self $newLineItem): void;

    public function compare(self $lineItem): bool;
}
