<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\CommandHandler;

use Prooph\ServiceBus\EventBus;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Checker\OrderRefundingAvailabilityCheckerInterface;
use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Event\UnitsRefunded;
use Sylius\RefundPlugin\Exception\InvalidRefundAmountException;
use Sylius\RefundPlugin\Exception\OrderNotAvailableForRefundingException;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Refunder\RefunderInterface;
use Sylius\RefundPlugin\Validator\RefundAmountValidatorInterface;
use Sylius\RefundPlugin\Validator\RefundUnitsCommandValidatorInterface;

final class RefundUnitsHandler
{
    /** @var RefunderInterface */
    private $orderUnitsRefunder;

    /** @var RefunderInterface */
    private $orderShipmentsRefunder;

    /** @var EventBus */
    private $eventBus;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var RefundUnitsCommandValidatorInterface */
    private $refundUnitsCommandValidator;

    public function __construct(
        RefunderInterface $orderUnitsRefunder,
        RefunderInterface $orderShipmentsRefunder,
        EventBus $eventBus,
        OrderRepositoryInterface $orderRepository,
        RefundUnitsCommandValidatorInterface $refundUnitsCommandValidator
    ) {
        $this->orderUnitsRefunder = $orderUnitsRefunder;
        $this->orderShipmentsRefunder = $orderShipmentsRefunder;
        $this->eventBus = $eventBus;
        $this->orderRepository = $orderRepository;
        $this->refundUnitsCommandValidator = $refundUnitsCommandValidator;
    }

    public function __invoke(RefundUnits $command): void
    {
        $this->refundUnitsCommandValidator->validate($command);

        $orderNumber = $command->orderNumber();

        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);

        $refundedTotal = 0;
        $refundedTotal += $this->orderUnitsRefunder->refundFromOrder($command->units(), $orderNumber);
        $refundedTotal += $this->orderShipmentsRefunder->refundFromOrder($command->shipments(), $orderNumber);

        $this->eventBus->dispatch(new UnitsRefunded(
            $orderNumber,
            $command->units(),
            $command->shipments(),
            $command->paymentMethodId(),
            $refundedTotal,
            $order->getCurrencyCode(),
            $command->comment()
        ));
    }
}
