<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\CommandHandler;

use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Creator\RefundCreatorInterface;
use Sylius\RefundPlugin\Provider\RefundedUnitTotalProviderInterface;

final class RefundUnitsHandler
{
    /** @var RefundCreatorInterface */
    private $refundCreator;

    /** @var RefundedUnitTotalProviderInterface */
    private $refundedUnitTotalProvider;

    public function __construct(
        RefundCreatorInterface $refundCreator,
        RefundedUnitTotalProviderInterface $refundedUnitTotalProvider
    ) {
        $this->refundCreator = $refundCreator;
        $this->refundedUnitTotalProvider = $refundedUnitTotalProvider;
    }

    public function __invoke(RefundUnits $command): void
    {
        foreach ($command->refundedUnitIds() as $refundedUnitId) {
            $this->refundCreator->__invoke(
                $command->orderNumber(),
                $refundedUnitId,
                $this->refundedUnitTotalProvider->getTotalOfUnitWithId($refundedUnitId)
            );
        }
    }
}
