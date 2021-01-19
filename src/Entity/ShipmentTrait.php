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

namespace Sylius\RefundPlugin\Entity;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Order\Model\AdjustmentInterface as BaseAdjustmentInterface;
use Webmozart\Assert\Assert;

/**
 * @internal
 *
 * This trait is not covered by the backward compatibility promise and it will be removed after update Sylius to 1.9.
 * It is a duplication of a logic from Sylius to provide proper adjustments handling.
 */
trait ShipmentTrait
{
    /**
     * @var Collection|AdjustmentInterface[]
     *
     * @ORM\OneToMany(
     *     targetEntity="Sylius\RefundPlugin\Entity\AdjustmentInterface",
     *     mappedBy="shipment",
     *     orphanRemoval=true,
     *     cascade={"all"}
     * )
     *
     * @psalm-var Collection<array-key, AdjustmentInterface>
     */
    protected $adjustments;

    /**
     * @var int
     * @ORM\Column(type="integer", name="adjustments_total")
     */
    protected $adjustmentsTotal = 0;

    public function getAdjustments(?string $type = null): Collection
    {
        if (null === $type) {
            return $this->adjustments;
        }

        return $this->adjustments->filter(function (AdjustmentInterface $adjustment) use ($type): bool {
            return $type === $adjustment->getType();
        });
    }

    public function addAdjustment(BaseAdjustmentInterface $adjustment): void
    {
        /** @var AdjustmentInterface $adjustment */
        Assert::isInstanceOf($adjustment, AdjustmentInterface::class);

        if ($this->hasAdjustment($adjustment)) {
            return;
        }

        $this->adjustments->add($adjustment);
        $adjustment->setShipment($this);
        $this->recalculateAdjustmentsTotal();
        $this->order->recalculateAdjustmentsTotal();
    }

    public function removeAdjustment(BaseAdjustmentInterface $adjustment): void
    {
        /** @var AdjustmentInterface $adjustment */
        if ($adjustment->isLocked() || !$this->hasAdjustment($adjustment)) {
            return;
        }

        $this->adjustments->removeElement($adjustment);
        $adjustment->setShipment(null);
        $this->recalculateAdjustmentsTotal();
        $this->order->recalculateAdjustmentsTotal();
    }

    public function hasAdjustment(BaseAdjustmentInterface $adjustment): bool
    {
        return $this->adjustments->contains($adjustment);
    }

    public function getAdjustmentsTotal(?string $type = null): int
    {
        $total = 0;
        foreach ($this->getAdjustments($type) as $adjustment) {
            if (!$adjustment->isNeutral()) {
                $total += $adjustment->getAmount();
            }
        }

        return $total;
    }

    public function removeAdjustments(?string $type = null): void
    {
        foreach ($this->getAdjustments($type) as $adjustment) {
            $this->removeAdjustment($adjustment);
        }
    }

    public function recalculateAdjustmentsTotal(): void
    {
        $this->adjustmentsTotal = $this->getAdjustmentsTotal();
    }
}
