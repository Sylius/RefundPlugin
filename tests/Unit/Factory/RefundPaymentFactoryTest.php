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

namespace Tests\Sylius\RefundPlugin\Unit\Factory;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\RefundPlugin\Entity\RefundPayment;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Sylius\RefundPlugin\Factory\RefundPaymentFactory;
use Sylius\RefundPlugin\Factory\RefundPaymentFactoryInterface;

final class RefundPaymentFactoryTest extends TestCase
{
    private RefundPaymentFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = new RefundPaymentFactory(RefundPayment::class);
    }

    public function testItIsInitializable(): void
    {
        self::assertInstanceOf(RefundPaymentFactory::class, $this->factory);
    }

    public function testItImplementsRefundPaymentFactoryInterface(): void
    {
        self::assertInstanceOf(RefundPaymentFactoryInterface::class, $this->factory);
    }

    public function testItCreatesANewRefundPayment(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $paymentMethod = $this->createMock(PaymentMethodInterface::class);

        $result = $this->factory->createWithData(
            $order,
            1000,
            'USD',
            RefundPaymentInterface::STATE_NEW,
            $paymentMethod,
        );

        self::assertEquals(new RefundPayment(
            $order,
            1000,
            'USD',
            RefundPaymentInterface::STATE_NEW,
            $paymentMethod,
        ), $result);
    }

    public function testItThrowsExceptionIfItTriesToCreateDefaultRefundPaymentWithoutData(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->factory->createNew();
    }
}
