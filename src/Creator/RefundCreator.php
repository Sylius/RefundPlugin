<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Creator;

use Doctrine\Persistence\ObjectManager;
use Sylius\RefundPlugin\Exception\UnitAlreadyRefunded;
use Sylius\RefundPlugin\Factory\RefundFactoryInterface;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Provider\RemainingTotalProviderInterface;

final class RefundCreator implements RefundCreatorInterface
{
    /** @var RefundFactoryInterface */
    private $refundFactory;

    /** @var RemainingTotalProviderInterface */
    private $remainingTotalProvider;

    /** @var ObjectManager */
    private $refundManager;

    public function __construct(
        RefundFactoryInterface $refundFactory,
        RemainingTotalProviderInterface $remainingTotalProvider,
        ObjectManager $refundManager
    ) {
        $this->refundFactory = $refundFactory;
        $this->remainingTotalProvider = $remainingTotalProvider;
        $this->refundManager = $refundManager;
    }

    public function __invoke(string $orderNumber, int $unitId, int $amount, RefundType $refundType): void
    {
        $remainingTotal = $this->remainingTotalProvider->getTotalLeftToRefund($unitId, $refundType);

        if ($remainingTotal === 0) {
            throw UnitAlreadyRefunded::withIdAndOrderNumber($unitId, $orderNumber);
        }

        $refund = $this->refundFactory->createWithData($orderNumber, $unitId, $amount, $refundType);

        $this->refundManager->persist($refund);
        $this->refundManager->flush();
    }
}
