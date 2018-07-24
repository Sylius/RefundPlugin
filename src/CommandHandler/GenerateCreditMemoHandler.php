<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\CommandHandler;

use Doctrine\Common\Persistence\ObjectManager;
use Prooph\ServiceBus\EventBus;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Command\GenerateCreditMemo;
use Sylius\RefundPlugin\Entity\CreditMemo;
use Sylius\RefundPlugin\Event\CreditMemoGenerated;
use Sylius\RefundPlugin\Exception\OrderNotFound;
use Sylius\RefundPlugin\Generator\NumberGenerator;

final class GenerateCreditMemoHandler
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var ObjectManager */
    private $creditMemoManager;

    /** @var NumberGenerator */
    private $creditMemoNumberGenerator;

    /** @var EventBus */
    private $eventBus;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ObjectManager $creditMemoManager,
        NumberGenerator $creditMemoNumberGenerator,
        EventBus $eventBus
    ) {
        $this->orderRepository = $orderRepository;
        $this->creditMemoManager = $creditMemoManager;
        $this->creditMemoNumberGenerator = $creditMemoNumberGenerator;
        $this->eventBus = $eventBus;
    }

    public function __invoke(GenerateCreditMemo $command): void
    {
        $orderNumber = $command->orderNumber();
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);

        if ($order === null) {
            throw OrderNotFound::withOrderNumber($orderNumber);
        }

        $this->creditMemoManager->persist(new CreditMemo(
            $this->creditMemoNumberGenerator->generate(),
            $orderNumber,
            $command->total(),
            $order->getCurrencyCode(),
            []
        ));
        $this->creditMemoManager->flush();

        $this->eventBus->dispatch(new CreditMemoGenerated($orderNumber));
    }
}
