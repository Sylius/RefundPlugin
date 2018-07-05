<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Creator;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\RefundPlugin\Factory\RefundFactoryInterface;

final class RefundCreator implements RefundCreatorInterface
{
    /** @var RefundFactoryInterface */
    private $refundFactory;

    /** @var ObjectManager */
    private $refundManager;

    public function __construct(RefundFactoryInterface $refundFactory, ObjectManager $refundManager)
    {
        $this->refundFactory = $refundFactory;
        $this->refundManager = $refundManager;
    }

    public function __invoke(string $orderNumber, int $unitId, int $amount): void
    {
        $refund = $this->refundFactory->createWithData($orderNumber, $unitId, $amount);

        $this->refundManager->persist($refund);
        $this->refundManager->flush();
    }
}
