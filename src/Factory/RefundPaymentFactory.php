<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Factory;

use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;

final class RefundPaymentFactory implements RefundPaymentFactoryInterface
{
    /** @var string */
    private $className;

    /** @var PaymentMethodRepositoryInterface */
    private $paymentMethodRepository;

    public function __construct(string $className, PaymentMethodRepositoryInterface $paymentMethodRepository)
    {
        $this->className = $className;
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    public function createWithData(
        string $orderNumber,
        int $amount,
        string $currencyCode,
        string $state,
        int $paymentMethodId
    ): RefundPaymentInterface {
        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $this->paymentMethodRepository->find($paymentMethodId);

        return new $this->className($orderNumber, $amount, $currencyCode, $state, $paymentMethod);
    }

    public function createNew()
    {
        throw new \RuntimeException();
    }
}
