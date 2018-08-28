<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Creator;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\RefundPlugin\Checker\UnitRefundingAvailabilityCheckerInterface;
use Sylius\RefundPlugin\Exception\UnitAlreadyRefundedException;
use Sylius\RefundPlugin\Exception\UnitRefundExceededException;
use Sylius\RefundPlugin\Factory\RefundFactoryInterface;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Provider\RemainingTotalProviderInterface;

final class RefundCreator implements RefundCreatorInterface
{
    /** @var RefundFactoryInterface */
    private $refundFactory;

    /** @var UnitRefundingAvailabilityCheckerInterface */
    private $unitRefundingAvailabilityChecker;

    /** @var RemainingTotalProviderInterface */
    private $remainingTotalProvider;

    /** @var ObjectManager */
    private $refundManager;

    public function __construct(
        RefundFactoryInterface $refundFactory,
        UnitRefundingAvailabilityCheckerInterface $unitRefundingAvailabilityChecker,
        RemainingTotalProviderInterface $remainingTotalProvider,
        ObjectManager $refundManager
    ) {
        $this->refundFactory = $refundFactory;
        $this->unitRefundingAvailabilityChecker = $unitRefundingAvailabilityChecker;
        $this->remainingTotalProvider = $remainingTotalProvider;
        $this->refundManager = $refundManager;
    }

    public function __invoke(string $orderNumber, int $unitId, int $amount, RefundType $refundType): void
    {
        if (!$this->unitRefundingAvailabilityChecker->__invoke($unitId, $refundType)) {
            throw UnitAlreadyRefundedException::withIdAndOrderNumber($unitId, $orderNumber);
        }

        if (
            $refundType->__toString() === RefundType::orderItemUnit()->__toString() &&
            $this->remainingTotalProvider->getTotalLeftToRefund($unitId) < $amount
        ) {
            throw new UnitRefundExceededException();
        }

        $refund = $this->refundFactory->createWithData($orderNumber, $unitId, $amount, $refundType);

        $this->refundManager->persist($refund);
        $this->refundManager->flush();
    }
}
