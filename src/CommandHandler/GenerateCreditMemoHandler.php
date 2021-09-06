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

namespace Sylius\RefundPlugin\CommandHandler;

use Doctrine\Persistence\ObjectManager;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Command\GenerateCreditMemo;
use Sylius\RefundPlugin\Event\CreditMemoGenerated;
use Sylius\RefundPlugin\Generator\CreditMemoGeneratorInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class GenerateCreditMemoHandler
{
    private CreditMemoGeneratorInterface $creditMemoGenerator;

    private ObjectManager $creditMemoManager;

    private MessageBusInterface $eventBus;

    private OrderRepositoryInterface $orderRepository;

    public function __construct(
        CreditMemoGeneratorInterface $creditMemoGenerator,
        ObjectManager $creditMemoManager,
        MessageBusInterface $eventBus,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->creditMemoGenerator = $creditMemoGenerator;
        $this->creditMemoManager = $creditMemoManager;
        $this->eventBus = $eventBus;
        $this->orderRepository = $orderRepository;
    }

    public function __invoke(GenerateCreditMemo $command): void
    {
        $orderNumber = $command->orderNumber();
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);

        $creditMemo = $this->creditMemoGenerator->generate(
            $order,
            $command->total(),
            $command->units(),
            $command->shipments(),
            $command->comment()
        );

        $this->creditMemoManager->persist($creditMemo);
        $this->creditMemoManager->flush();

        $this->eventBus->dispatch(new CreditMemoGenerated($creditMemo->getNumber(), $orderNumber));
    }
}
