<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\CommandHandler;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prooph\ServiceBus\EventBus;
use Prophecy\Argument;
use Sylius\RefundPlugin\Command\GenerateCreditMemo;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Event\CreditMemoGenerated;
use Sylius\RefundPlugin\Generator\CreditMemoGeneratorInterface;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;

final class GenerateCreditMemoHandlerSpec extends ObjectBehavior
{
    function let(
        CreditMemoGeneratorInterface $creditMemoGenerator,
        ObjectManager $creditMemoManager,
        EventBus $eventBus
    ) {
        $this->beConstructedWith($creditMemoGenerator, $creditMemoManager, $eventBus);
    }

    function it_generates_credit_memo(
        CreditMemoGeneratorInterface $creditMemoGenerator,
        ObjectManager $creditMemoManager,
        EventBus $eventBus,
        CreditMemoInterface $creditMemo
    ): void {
        $unitRefunds = [new OrderItemUnitRefund(1, 1000), new OrderItemUnitRefund(3, 2000), new OrderItemUnitRefund(5, 3000)];

        $creditMemoGenerator->generate('000666', 1000, $unitRefunds, [3, 4], 'Comment')->willReturn($creditMemo);

        $creditMemo->getNumber()->willReturn('2018/01/000001');

        $creditMemoManager->persist($creditMemo)->shouldBeCalled();
        $creditMemoManager->flush()->shouldBeCalled();

        $eventBus->dispatch(Argument::that(function (CreditMemoGenerated $event): bool {
            return
                $event->number() === '2018/01/000001' &&
                $event->orderNumber() === '000666'
            ;
        }))->shouldBeCalled();

        $this(new GenerateCreditMemo('000666', 1000, $unitRefunds, [3, 4], 'Comment'));
    }
}
