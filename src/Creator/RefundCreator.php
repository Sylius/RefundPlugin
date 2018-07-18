<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Creator;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\RefundPlugin\Checker\UnitRefundingAvailabilityCheckerInterface;
use Sylius\RefundPlugin\Exception\UnitAlreadyRefundedException;
use Sylius\RefundPlugin\Factory\RefundFactoryInterface;

final class RefundCreator implements RefundCreatorInterface
{
    /** @var RefundFactoryInterface */
    private $refundFactory;

    /** @var UnitRefundingAvailabilityCheckerInterface */
    private $unitRefundingAvailabilityChecker;

    /** @var ObjectManager */
    private $refundManager;

    public function __construct(
        RefundFactoryInterface $refundFactory,
        UnitRefundingAvailabilityCheckerInterface $unitRefundingAvailabilityChecker,
        ObjectManager $refundManager
    ) {
        $this->refundFactory = $refundFactory;
        $this->unitRefundingAvailabilityChecker = $unitRefundingAvailabilityChecker;
        $this->refundManager = $refundManager;
    }

    public function __invoke(string $orderNumber, int $unitId, int $amount): void
    {
        if (!$this->unitRefundingAvailabilityChecker->__invoke($unitId)) {
            throw UnitAlreadyRefundedException::withIdAndOrderNumber($unitId, $orderNumber);
        }

        $refund = $this->refundFactory->createWithData($orderNumber, $unitId, $amount);

        $this->refundManager->persist($refund);
        $this->refundManager->flush();
    }
}
