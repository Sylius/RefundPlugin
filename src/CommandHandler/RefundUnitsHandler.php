<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\CommandHandler;

use Prooph\ServiceBus\EventBus;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Checker\OrderFullyRefundedTotalCheckerInterface;
use Sylius\RefundPlugin\Checker\OrderRefundingAvailabilityCheckerInterface;
use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Event\UnitsRefunded;
use Sylius\RefundPlugin\Exception\OrderNotAvailableForRefundingException;
use Sylius\RefundPlugin\Refunder\RefunderInterface;
use Sylius\RefundPlugin\StateResolver\OrderFullyRefundedStateResolverInterface;

final class RefundUnitsHandler
{
    /** @var RefunderInterface */
    private $orderUnitsRefunder;

    /** @var RefunderInterface */
    private $orderShipmentsRefunder;

    /** @var OrderRefundingAvailabilityCheckerInterface */
    private $orderRefundingAvailabilityChecker;

    /** @var EventBus */
    private $eventBus;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var OrderFullyRefundedTotalCheckerInterface */
    private $orderFullyRefundedTotalChecker;

    /** @var OrderFullyRefundedStateResolverInterface */
    private $orderFullyRefundedStateResolver;

    public function __construct(
        RefunderInterface $orderUnitsRefunder,
        RefunderInterface $orderShipmentsRefunder,
        OrderRefundingAvailabilityCheckerInterface $orderRefundingAvailabilityChecker,
        EventBus $eventBus,
        OrderRepositoryInterface $orderRepository,
        OrderFullyRefundedTotalCheckerInterface $orderFullyRefundedTotalChecker,
        OrderFullyRefundedStateResolverInterface $orderFullyRefundedStateResolver
    ) {
        $this->orderRefundingAvailabilityChecker = $orderRefundingAvailabilityChecker;
        $this->orderUnitsRefunder = $orderUnitsRefunder;
        $this->orderShipmentsRefunder = $orderShipmentsRefunder;
        $this->eventBus = $eventBus;
        $this->orderRepository = $orderRepository;
        $this->orderFullyRefundedTotalChecker = $orderFullyRefundedTotalChecker;
        $this->orderFullyRefundedStateResolver = $orderFullyRefundedStateResolver;
    }

    public function __invoke(RefundUnits $command): void
    {
        if (!$this->orderRefundingAvailabilityChecker->__invoke($command->orderNumber())) {
            throw OrderNotAvailableForRefundingException::withOrderNumber($command->orderNumber());
        }

        $orderNumber = $command->orderNumber();
        $order = $this->orderRepository->findOneByNumber($orderNumber);

        $refundedTotal = 0;
        $refundedTotal += $this->orderUnitsRefunder->refundFromOrder($command->unitIds(), $orderNumber);

        $this->resolveOrderState($order);

        $refundedTotal += $this->orderShipmentsRefunder->refundFromOrder($command->shipmentIds(), $orderNumber);

        $this->resolveOrderState($order);

        $this->eventBus->dispatch(new UnitsRefunded(
            $orderNumber,
            $command->unitIds(),
            $command->shipmentIds(),
            $refundedTotal
        ));
    }

    private function resolveOrderState(OrderInterface $order): void
    {
        if ($this->orderFullyRefundedTotalChecker->check($order)) {
            $this->orderFullyRefundedStateResolver->resolve($order);
        }
    }
}
