<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Provider;

use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\RefundInterface;

final class RepositoryOrderRefundedTotalProvider implements OrderRefundedTotalProviderInterface
{
    /** @var RepositoryInterface */
    private $refundRepository;

    /** @var RepositoryInterface */
    private $orderItemUnitRepository;

    public function __construct(RepositoryInterface $refundRepository, RepositoryInterface $orderItemUnitRepository)
    {
        $this->refundRepository = $refundRepository;
        $this->orderItemUnitRepository = $orderItemUnitRepository;
    }

    public function __invoke(string $orderNumber): int
    {
        $refunds = $this->refundRepository->findOneBy(['orderNumber' => $orderNumber]);

        $orderRefundedTotal = 0;
        /** @var RefundInterface $refund */
        foreach ($refunds as $refund) {
            /** @var OrderItemUnitInterface $orderItemUnit */
            $orderItemUnit = $this->orderItemUnitRepository->find($refund->getRefundedUnitId());

            $orderRefundedTotal += $orderItemUnit->getTotal();
        }

        return $orderRefundedTotal;
    }
}
