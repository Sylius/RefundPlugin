<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Provider;

use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class RepositoryRefundedUnitTotalProvider implements RefundedUnitTotalProviderInterface
{
    /** @var RepositoryInterface */
    private $orderItemUnitRepository;

    public function __construct(RepositoryInterface $orderItemUnitRepository)
    {
        $this->orderItemUnitRepository = $orderItemUnitRepository;
    }

    public function getTotalOfUnitWithId(int $unitId): int
    {
        /** @var OrderItemUnitInterface $unit */
        $unit = $this->orderItemUnitRepository->find($unitId);
        Assert::notNull($unit);

        return $unit->getTotal();
    }
}
