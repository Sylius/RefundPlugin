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

namespace Tests\Sylius\RefundPlugin\Unit\ProcessManager;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Sylius\RefundPlugin\Event\RefundPaymentGenerated;
use Sylius\RefundPlugin\Event\UnitsRefunded;
use Sylius\RefundPlugin\Factory\RefundPaymentFactoryInterface;
use Sylius\RefundPlugin\Model\UnitRefundInterface;
use Sylius\RefundPlugin\ProcessManager\RefundPaymentProcessManager;
use Sylius\RefundPlugin\ProcessManager\UnitsRefundedProcessStepInterface;
use Sylius\RefundPlugin\Provider\RelatedPaymentIdProviderInterface;
use Sylius\RefundPlugin\StateResolver\OrderFullyRefundedStateResolverInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class RefundPaymentProcessManagerTest extends TestCase
{
    private OrderFullyRefundedStateResolverInterface&MockObject $orderFullyRefundedStateResolver;

    private RelatedPaymentIdProviderInterface&MockObject $relatedPaymentIdProvider;

    private RefundPaymentFactoryInterface&MockObject $refundPaymentFactory;

    /** @var OrderRepositoryInterface<OrderInterface>&MockObject */
    private OrderRepositoryInterface&MockObject $orderRepository;

    /** @var PaymentMethodRepositoryInterface<PaymentMethodInterface>&MockObject */
    private PaymentMethodRepositoryInterface&MockObject $paymentMethodRepository;

    private EntityManagerInterface&MockObject $entityManager;

    private MessageBusInterface&MockObject $eventBus;

    private RefundPaymentProcessManager $refundPaymentProcessManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderFullyRefundedStateResolver = $this->createMock(OrderFullyRefundedStateResolverInterface::class);
        $this->relatedPaymentIdProvider = $this->createMock(RelatedPaymentIdProviderInterface::class);
        $this->refundPaymentFactory = $this->createMock(RefundPaymentFactoryInterface::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->paymentMethodRepository = $this->createMock(PaymentMethodRepositoryInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->eventBus = $this->createMock(MessageBusInterface::class);

        $this->refundPaymentProcessManager = new RefundPaymentProcessManager(
            $this->orderFullyRefundedStateResolver,
            $this->relatedPaymentIdProvider,
            $this->refundPaymentFactory,
            $this->orderRepository,
            $this->paymentMethodRepository,
            $this->entityManager,
            $this->eventBus,
        );
    }

    /** @test */
    public function it_implements_units_refunded_process_step_interface(): void
    {
        self::assertInstanceOf(UnitsRefundedProcessStepInterface::class, $this->refundPaymentProcessManager);
    }

    /** @test */
    public function it_reacts_on_units_refunded_event_and_creates_refund_payment(): void
    {
        $refundPayment = $this->createMock(RefundPaymentInterface::class);
        $order = $this->createMock(OrderInterface::class);
        $paymentMethod = $this->createMock(PaymentMethodInterface::class);
        $firstUnitRefund = $this->createMock(UnitRefundInterface::class);
        $secondUnitRefund = $this->createMock(UnitRefundInterface::class);

        $this->orderRepository
            ->expects(self::once())
            ->method('findOneByNumber')
            ->with('000222')
            ->willReturn($order);

        $this->paymentMethodRepository
            ->expects(self::once())
            ->method('find')
            ->with(1)
            ->willReturn($paymentMethod);

        $this->refundPaymentFactory
            ->expects(self::once())
            ->method('createWithData')
            ->with($order, 1000, 'USD', RefundPaymentInterface::STATE_NEW, $paymentMethod)
            ->willReturn($refundPayment);

        $this->entityManager
            ->expects(self::once())
            ->method('persist')
            ->with($refundPayment);

        $this->entityManager
            ->expects(self::once())
            ->method('flush');

        $this->orderFullyRefundedStateResolver
            ->expects(self::once())
            ->method('resolve')
            ->with('000222');

        $refundPayment
            ->expects(self::once())
            ->method('getId')
            ->willReturn(10);

        $refundPayment
            ->expects($this->never())
            ->method('getOrder');

        $refundPayment
            ->expects($this->never())
            ->method('getAmount');

        $this->relatedPaymentIdProvider
            ->expects(self::once())
            ->method('getForRefundPayment')
            ->with($refundPayment)
            ->willReturn(3);

        $event = new RefundPaymentGenerated(10, '000222', 1000, 'USD', 1, 3);
        $this->eventBus
            ->expects(self::once())
            ->method('dispatch')
            ->with($event)
            ->willReturn(new Envelope($event));

        $this->refundPaymentProcessManager->next(new UnitsRefunded(
            '000222',
            [$firstUnitRefund, $secondUnitRefund],
            1,
            1000,
            'USD',
            'Comment',
        ));
    }
}
