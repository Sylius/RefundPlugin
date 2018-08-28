<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Checker;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Provider\RemainingTotalProviderInterface;

final class UnitRefundingAvailabilityChecker implements UnitRefundingAvailabilityCheckerInterface
{
    /** @var RepositoryInterface */
    private $refundRepository;

    /** @var RemainingTotalProviderInterface */
    private $remainingOrderItemUnitTotalProvider;

    public function __construct(
        RepositoryInterface $refundRepository,
        RemainingTotalProviderInterface $remainingOrderItemUnitTotalProvider
    ) {
        $this->refundRepository = $refundRepository;
        $this->remainingOrderItemUnitTotalProvider = $remainingOrderItemUnitTotalProvider;
    }

    public function __invoke(int $unitId, RefundType $refundType): bool
    {
        // temporary solution until providing the possibility to refund partial shipment as well
        if ($refundType->equals(RefundType::shipment())) {
            return null == $this
                ->refundRepository
                ->findOneBy(['refundedUnitId' => $unitId, 'type' => $refundType->__toString()])
            ;
        }

        return $this->remainingOrderItemUnitTotalProvider->getTotalLeftToRefund($unitId) > 0;
    }
}
