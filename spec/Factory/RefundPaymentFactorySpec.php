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

namespace spec\Sylius\RefundPlugin\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\RefundPlugin\Entity\RefundPayment;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Sylius\RefundPlugin\Factory\RefundPaymentFactory;
use Sylius\RefundPlugin\Factory\RefundPaymentFactoryInterface;

final class RefundPaymentFactorySpec extends ObjectBehavior
{
    public function let(PaymentMethodRepositoryInterface $paymentMethodRepository): void
    {
        $this->beConstructedWith($paymentMethodRepository);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(RefundPaymentFactory::class);
    }

    public function it_implements_refund_payment_factory_interface(): void
    {
        $this->shouldImplement(RefundPaymentFactoryInterface::class);
    }

    public function it_creates_new_refund_payment(
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentMethodInterface $paymentMethod,
        OrderInterface $order
    ): void {
        $paymentMethodRepository->find(1)->willReturn($paymentMethod);

        $this
            ->createWithData(
                $order,
                1000,
                'USD',
                RefundPaymentInterface::STATE_NEW,
                1
            )
            ->shouldBeLike(new RefundPayment(
                    $order->getWrappedObject(),
                1000,
                'USD',
                RefundPaymentInterface::STATE_NEW,
                $paymentMethod->getWrappedObject()
            )
        );
    }
}
