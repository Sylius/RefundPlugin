<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Creator;

use Doctrine\Common\Persistence\ObjectManager;
use Prooph\ServiceBus\EventBus;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Checker\UnitRefundingAvailabilityCheckerInterface;
use Sylius\RefundPlugin\Event\UnitRefunded;
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

    /** @var EventBus */
    private $eventBus;

    public function __construct(
        RefundFactoryInterface $refundFactory,
        UnitRefundingAvailabilityCheckerInterface $unitRefundingAvailabilityChecker,
        ObjectManager $refundManager,
        EventBus $eventBus
    ) {
        $this->refundFactory = $refundFactory;
        $this->unitRefundingAvailabilityChecker = $unitRefundingAvailabilityChecker;
        $this->refundManager = $refundManager;
        $this->eventBus = $eventBus;
    }

    public function __invoke(string $orderNumber, int $unitId, int $amount): void
    {
        if (!$this->unitRefundingAvailabilityChecker->__invoke($unitId)) {
            throw UnitAlreadyRefundedException::withIdAndOrderNumber($unitId, $orderNumber);
        }

        $refund = $this->refundFactory->createWithData($orderNumber, $unitId, $amount);

        $this->refundManager->persist($refund);
        $this->refundManager->flush();

        $this->eventBus->dispatch(new UnitRefunded($orderNumber, $unitId, $amount));
    }
}
