<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

interface RefundPaymentInterface extends ResourceInterface
{
    public const STATE_NEW = 'new';

    public const STATE_COMPLETED = 'completed';

    public function getOrder(): OrderInterface;

    public function getAmount(): int;

    public function getCurrencyCode(): string;

    public function getState(): string;

    public function getPaymentMethod(): PaymentMethodInterface;
}
