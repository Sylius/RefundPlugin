<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Creator;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Exception\UnitAlreadyRefundedException;
use Sylius\RefundPlugin\Factory\RefundFactoryInterface;

final class RefundCreator implements RefundCreatorInterface
{
    /** @var RefundFactoryInterface */
    private $refundFactory;

    /** @var RepositoryInterface */
    private $refundRepository;

    /** @var ObjectManager */
    private $refundManager;

    public function __construct(
        RefundFactoryInterface $refundFactory,
        RepositoryInterface $refundRepository,
        ObjectManager $refundManager
    ) {
        $this->refundFactory = $refundFactory;
        $this->refundRepository = $refundRepository;
        $this->refundManager = $refundManager;
    }

    public function __invoke(string $orderNumber, int $unitId, int $amount): void
    {
        if ($this->refundRepository->findOneBy(['orderNumber' => $orderNumber, 'refundedUnitId' => $unitId]) !== null) {
            throw UnitAlreadyRefundedException::withIdAndOrderNumber($unitId, $orderNumber);
        }

        $refund = $this->refundFactory->createWithData($orderNumber, $unitId, $amount);

        $this->refundManager->persist($refund);
        $this->refundManager->flush();
    }
}
