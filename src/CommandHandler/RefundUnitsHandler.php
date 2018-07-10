<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\CommandHandler;

use Prooph\ServiceBus\EventBus;
use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Creator\RefundCreatorInterface;
use Sylius\RefundPlugin\Event\UnitsRefunded;
use Sylius\RefundPlugin\Provider\RefundedUnitTotalProviderInterface;

final class RefundUnitsHandler
{
    /** @var RefundCreatorInterface */
    private $refundCreator;

    /** @var RefundedUnitTotalProviderInterface */
    private $refundedUnitTotalProvider;

    /** @var EventBus */
    private $eventBus;

    public function __construct(
        RefundCreatorInterface $refundCreator,
        RefundedUnitTotalProviderInterface $refundedUnitTotalProvider,
        EventBus $eventBus
    ) {
        $this->refundCreator = $refundCreator;
        $this->refundedUnitTotalProvider = $refundedUnitTotalProvider;
        $this->eventBus = $eventBus;
    }

    public function __invoke(RefundUnits $command): void
    {
        $refundedTotal = 0;
        foreach ($command->refundedUnitIds() as $refundedUnitId) {
            $refundAmount = $this->refundedUnitTotalProvider->getTotalOfUnitWithId($refundedUnitId);

            $this->refundCreator->__invoke($command->orderNumber(), $refundedUnitId, $refundAmount);

            $refundedTotal += $refundAmount;
        }

        $this->eventBus->dispatch(new UnitsRefunded(
            $command->orderNumber(),
            $command->refundedUnitIds(),
            $refundedTotal
        ));
    }
}
