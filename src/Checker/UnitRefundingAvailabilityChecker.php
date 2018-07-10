<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Checker;

use Sylius\Component\Resource\Repository\RepositoryInterface;

final class UnitRefundingAvailabilityChecker implements UnitRefundingAvailabilityCheckerInterface
{
    /** @var RepositoryInterface */
    private $refundRepository;

    public function __construct(RepositoryInterface $refundRepository)
    {
        $this->refundRepository = $refundRepository;
    }

    public function __invoke(int $unitId): bool
    {
        return null == $this->refundRepository->findOneBy(['refundedUnitId' => $unitId]);
    }
}
