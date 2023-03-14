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

namespace Sylius\RefundPlugin\Creator;

use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Exception\UnitAlreadyRefunded;
use Sylius\RefundPlugin\Factory\RefundFactoryInterface;
use Sylius\RefundPlugin\Model\RefundTypeInterface;
use Sylius\RefundPlugin\Provider\RemainingTotalProviderInterface;
use Webmozart\Assert\Assert;

final class RefundCreator implements RefundCreatorInterface
{
    private RefundFactoryInterface $refundFactory;

    private RemainingTotalProviderInterface $remainingTotalProvider;

    private OrderRepositoryInterface $orderRepository;

    private ObjectManager $refundManager;

    public function __construct(
        RefundFactoryInterface $refundFactory,
        RemainingTotalProviderInterface $remainingTotalProvider,
        OrderRepositoryInterface $orderRepository,
        ObjectManager $refundManager,
    ) {
        $this->refundFactory = $refundFactory;
        $this->remainingTotalProvider = $remainingTotalProvider;
        $this->orderRepository = $orderRepository;
        $this->refundManager = $refundManager;
    }

    public function __invoke(string $orderNumber, int $unitId, int $amount, RefundTypeInterface $refundType): void
    {
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);
        Assert::notNull($order);

        $remainingTotal = $this->remainingTotalProvider->getTotalLeftToRefund($unitId, $refundType);

        if ($remainingTotal === 0) {
            throw UnitAlreadyRefunded::withIdAndOrderNumber($unitId, $orderNumber);
        }

        $refund = $this->refundFactory->createWithData($order, $unitId, $amount, $refundType);

        $this->refundManager->persist($refund);
        $this->refundManager->flush();
    }
}
