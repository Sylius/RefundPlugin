<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\CommandHandler;

use Prooph\ServiceBus\EventBus;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Checker\OrderRefundingAvailabilityCheckerInterface;
use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Event\UnitsRefunded;
use Sylius\RefundPlugin\Exception\OrderNotAvailableForRefundingException;
use Sylius\RefundPlugin\Refunder\RefunderInterface;

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

    public function __construct(
        RefunderInterface $orderUnitsRefunder,
        RefunderInterface $orderShipmentsRefunder,
        OrderRefundingAvailabilityCheckerInterface $orderRefundingAvailabilityChecker,
        EventBus $eventBus,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->orderRefundingAvailabilityChecker = $orderRefundingAvailabilityChecker;
        $this->orderUnitsRefunder = $orderUnitsRefunder;
        $this->orderShipmentsRefunder = $orderShipmentsRefunder;
        $this->eventBus = $eventBus;
        $this->orderRepository = $orderRepository;
    }

    public function __invoke(RefundUnits $command): void
    {
        if (!$this->orderRefundingAvailabilityChecker->__invoke($command->orderNumber())) {
            throw OrderNotAvailableForRefundingException::withOrderNumber($command->orderNumber());
        }

        $orderNumber = $command->orderNumber();

        $currencyCode = $this->orderRepository->findOneByNumber($orderNumber)->getCurrencyCode();

        $refundedTotal = 0;
        $refundedTotal += $this->orderUnitsRefunder->refundFromOrder($command->unitIds(), $orderNumber);
        $refundedTotal += $this->orderShipmentsRefunder->refundFromOrder($command->shipmentIds(), $orderNumber);

        $this->eventBus->dispatch(new UnitsRefunded(
            $orderNumber,
            $command->unitIds(),
            $command->shipmentIds(),
            $command->paymentMethodId(),
            $refundedTotal,
            $currencyCode
        ));
    }
}
