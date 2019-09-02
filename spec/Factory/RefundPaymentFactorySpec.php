<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\RefundPlugin\Entity\RefundPayment;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Sylius\RefundPlugin\Factory\RefundPaymentFactory;
use Sylius\RefundPlugin\Factory\RefundPaymentFactoryInterface;

final class RefundPaymentFactorySpec extends ObjectBehavior
{
    function let(PaymentMethodRepositoryInterface $paymentMethodRepository): void
    {
        $this->beConstructedWith(RefundPayment::class, $paymentMethodRepository);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(RefundPaymentFactory::class);
    }

    function it_implements_refund_payment_factory_interface(): void
    {
        $this->shouldImplement(RefundPaymentFactoryInterface::class);
    }

    function it_creates_new_refund_payment(
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentMethodInterface $paymentMethod
    ): void {
        $paymentMethodRepository->find(1)->willReturn($paymentMethod);

        $this->createWithData(
            '0002',
            1000,
            'USD',
            RefundPaymentInterface::STATE_NEW,
            1
        )->shouldBeLike(new RefundPayment(
            '0002',
            1000,
            'USD',
            RefundPaymentInterface::STATE_NEW,
            $paymentMethod->getWrappedObject())
        );
    }
}
