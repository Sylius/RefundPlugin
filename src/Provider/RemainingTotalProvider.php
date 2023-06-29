<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\RefundPlugin\Provider;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Order\Model\AdjustableInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\RefundInterface;
use Sylius\RefundPlugin\Model\RefundTypeInterface;
use Symfony\Contracts\Service\ServiceProviderInterface;
use Webmozart\Assert\Assert;

final class RemainingTotalProvider implements RemainingTotalProviderInterface
{
    private ?RepositoryInterface $orderItemUnitRepository = null;

    private ?RepositoryInterface $adjustmentRepository = null;

    public function __construct(
        private ServiceProviderInterface|RepositoryInterface $refundUnitTotalProvider,
        private RepositoryInterface $refundRepository,
    ) {
        $args = func_get_args();

        if ($refundUnitTotalProvider instanceof RepositoryInterface) {
            if (!isset($args[2])) {
                throw new \InvalidArgumentException('The 3th argument must be present.');
            }

            $this->orderItemUnitRepository = $refundUnitTotalProvider;
            $this->adjustmentRepository = $this->refundRepository;
            $this->refundRepository = $args[2];

            trigger_deprecation('sylius/refund-plugin', '1.4', sprintf('Passing a "%s" as a 1st argument of "%s" constructor is deprecated and will be removed in 2.0.', RepositoryInterface::class, self::class));
        }
    }

    public function getTotalLeftToRefund(int $id, RefundTypeInterface $type): int
    {
        if (null !== $this->orderItemUnitRepository) {
            $unitTotal = $this->getRefundUnitTotal($id, $type);
        } else {
            Assert::isInstanceOf($this->refundUnitTotalProvider, ServiceProviderInterface::class);
            $unitTotal = $this->refundUnitTotalProvider->get($type->getValue())->getRefundUnitTotal($id);
        }

        $refunds = $this->refundRepository->findBy(['refundedUnitId' => $id, 'type' => $type]);

        if (count($refunds) === 0) {
            return $unitTotal;
        }

        $refundedTotal = 0;
        /** @var RefundInterface $refund */
        foreach ($refunds as $refund) {
            $refundedTotal += $refund->getAmount();
        }

        return $unitTotal - $refundedTotal;
    }

    private function getRefundUnitTotal(int $id, RefundTypeInterface $refundType): int
    {
        Assert::isInstanceOf($this->orderItemUnitRepository, RepositoryInterface::class);
        Assert::isInstanceOf($this->adjustmentRepository, RepositoryInterface::class);

        if ($refundType->getValue() === RefundTypeInterface::ORDER_ITEM_UNIT) {
            /** @var OrderItemUnitInterface $orderItemUnit */
            $orderItemUnit = $this->orderItemUnitRepository->find($id);
            Assert::notNull($orderItemUnit);

            return $orderItemUnit->getTotal();
        }

        /** @var AdjustmentInterface $shippingAdjustment */
        $shippingAdjustment = $this->adjustmentRepository->findOneBy([
            'id' => $id,
            'type' => AdjustmentInterface::SHIPPING_ADJUSTMENT,
        ]);
        Assert::notNull($shippingAdjustment);

        $shipment = $shippingAdjustment->getShipment();
        Assert::notNull($shipment);
        Assert::isInstanceOf($shipment, AdjustableInterface::class);

        return $shipment->getAdjustmentsTotal();
    }
}
