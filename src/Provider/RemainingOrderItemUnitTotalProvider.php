<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Provider;

use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\RefundInterface;
use Sylius\RefundPlugin\Model\RefundType;
use Webmozart\Assert\Assert;

final class RemainingOrderItemUnitTotalProvider implements RemainingTotalProviderInterface
{
    /** @var RepositoryInterface */
    private $orderItemUnitRepository;

    /** @var RepositoryInterface */
    private $refundRepository;

    public function __construct(RepositoryInterface $orderItemUnitRepository, RepositoryInterface $refundRepository)
    {
        $this->orderItemUnitRepository = $orderItemUnitRepository;
        $this->refundRepository = $refundRepository;
    }

    public function getTotalLeftToRefund(int $id): int
    {
        /** @var OrderItemUnitInterface $orderItemUnit */
        $orderItemUnit = $this->orderItemUnitRepository->find($id);
        Assert::notNull($orderItemUnit);

        $refunds = $this->refundRepository->findBy(['refundedUnitId' => $id, 'type' => RefundType::orderItemUnit()->__toString()]);

        if (count($refunds) === 0) {
            return $orderItemUnit->getTotal();
        }

        $refundedTotal = 0;
        /** @var RefundInterface $refund */
        foreach ($refunds as $refund) {
            $refundedTotal += $refund->getAmount();
        }

        return $orderItemUnit->getTotal() - $refundedTotal;
    }
}
