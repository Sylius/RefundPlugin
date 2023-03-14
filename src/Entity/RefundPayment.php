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

class RefundPayment implements RefundPaymentInterface
{
    protected ?int $id = null;

    protected OrderInterface $order;

    protected int $amount;

    protected string $currencyCode;

    protected string $state;

    protected PaymentMethodInterface $paymentMethod;

    public function __construct(
        OrderInterface $order,
        int $amount,
        string $currencyCode,
        string $state,
        PaymentMethodInterface $paymentMethod,
    ) {
        $this->order = $order;
        $this->amount = $amount;
        $this->currencyCode = $currencyCode;
        $this->state = $state;
        $this->paymentMethod = $paymentMethod;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrder(): OrderInterface
    {
        return $this->order;
    }

    /** @deprecated this function is deprecated and will be removed in v2.0.0. Use RefundPayment::getOrder() instead */
    public function getOrderNumber(): string
    {
        return (string) $this->getOrder()->getNumber();
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function getPaymentMethod(): PaymentMethodInterface
    {
        return $this->paymentMethod;
    }
}
