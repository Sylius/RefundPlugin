<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\CommandHandler;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Command\GenerateCreditMemo;
use Sylius\RefundPlugin\Entity\CreditMemo;
use Sylius\RefundPlugin\Exception\OrderNotFound;

final class GenerateCreditMemoHandler
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var ObjectManager */
    private $creditMemoManager;

    public function __construct(OrderRepositoryInterface $orderRepository, ObjectManager $creditMemoManager)
    {
        $this->orderRepository = $orderRepository;
        $this->creditMemoManager = $creditMemoManager;
    }

    public function __invoke(GenerateCreditMemo $command): void
    {
        $orderNumber = $command->orderNumber();
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);

        if ($order === null) {
            throw OrderNotFound::withOrderNumber($orderNumber);
        }

        $this->creditMemoManager->persist(new CreditMemo($orderNumber, $command->total(), $order->getCurrencyCode()));
        $this->creditMemoManager->flush();
    }
}
