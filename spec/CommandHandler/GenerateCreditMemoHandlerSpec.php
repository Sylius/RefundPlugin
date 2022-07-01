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

namespace spec\Sylius\RefundPlugin\CommandHandler;

use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Command\GenerateCreditMemo;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Event\CreditMemoGenerated;
use Sylius\RefundPlugin\Generator\CreditMemoGeneratorInterface;
use Sylius\RefundPlugin\Generator\CreditMemoPdfFileGeneratorInterface;
use Sylius\RefundPlugin\Manager\CreditMemoFileManagerInterface;
use Sylius\RefundPlugin\Model\CreditMemoPdf;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class GenerateCreditMemoHandlerSpec extends ObjectBehavior
{
    function let(
        CreditMemoGeneratorInterface $creditMemoGenerator,
        ObjectManager $creditMemoManager,
        MessageBusInterface $eventBus,
        OrderRepositoryInterface $orderRepository,
        CreditMemoPdfFileGeneratorInterface $creditMemoPdfFileGenerator,
        CreditMemoFileManagerInterface $creditMemoFileManager,
    ) {
        $this->beConstructedWith(
            $creditMemoGenerator,
            $creditMemoManager,
            $eventBus,
            $orderRepository,
            true,
            $creditMemoPdfFileGenerator,
            $creditMemoFileManager,
        );
    }

    function it_generates_credit_memo_with_a_pdf_file(
        CreditMemoGeneratorInterface $creditMemoGenerator,
        ObjectManager $creditMemoManager,
        MessageBusInterface $eventBus,
        OrderRepositoryInterface $orderRepository,
        CreditMemoPdfFileGeneratorInterface $creditMemoPdfFileGenerator,
        CreditMemoFileManagerInterface $creditMemoFileManager,
        CreditMemoInterface $creditMemo,
        OrderInterface $order
    ): void {
        $orderItemUnitRefunds = [new OrderItemUnitRefund(1, 1000), new OrderItemUnitRefund(3, 2000), new OrderItemUnitRefund(5, 3000)];
        $shipmentRefunds = [new ShipmentRefund(3, 1000)];

        $orderRepository->findOneByNumber('000666')->willReturn($order);

        $creditMemoGenerator->generate($order, 7000, $orderItemUnitRefunds, $shipmentRefunds, 'Comment')->willReturn($creditMemo);

        $creditMemo->getNumber()->willReturn('2018/01/000001');
        $creditMemo->getId()->willReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');

        $creditMemoManager->persist($creditMemo)->shouldBeCalled();
        $creditMemoManager->flush()->shouldBeCalled();

        $creditMemoPdf = new CreditMemoPdf('credit_memo.pdf', 'CONTENT');
        $creditMemoPdfFileGenerator->generate('7903c83a-4c5e-4bcf-81d8-9dc304c6a353')->willReturn($creditMemoPdf);
        $creditMemoFileManager->save($creditMemoPdf)->shouldBeCalled();

        $event = new CreditMemoGenerated('2018/01/000001', '000666');
        $eventBus->dispatch($event)->willReturn(new Envelope($event))->shouldBeCalled();

        $this(new GenerateCreditMemo('000666', 7000, $orderItemUnitRefunds, $shipmentRefunds, 'Comment'));
    }

    function it_generates_only_credit_memo_without_a_pdf_file(
        CreditMemoGeneratorInterface $creditMemoGenerator,
        ObjectManager $creditMemoManager,
        MessageBusInterface $eventBus,
        OrderRepositoryInterface $orderRepository,
        CreditMemoPdfFileGeneratorInterface $creditMemoPdfFileGenerator,
        CreditMemoInterface $creditMemo,
        OrderInterface $order
    ): void {
        $this->beConstructedWith(
            $creditMemoGenerator,
            $creditMemoManager,
            $eventBus,
            $orderRepository,
        );

        $orderItemUnitRefunds = [new OrderItemUnitRefund(1, 1000), new OrderItemUnitRefund(3, 2000), new OrderItemUnitRefund(5, 3000)];
        $shipmentRefunds = [new ShipmentRefund(3, 1000)];

        $orderRepository->findOneByNumber('000666')->willReturn($order);

        $creditMemoGenerator->generate($order, 7000, $orderItemUnitRefunds, $shipmentRefunds, 'Comment')->willReturn($creditMemo);

        $creditMemo->getNumber()->willReturn('2018/01/000001');
        $creditMemo->getId()->willReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');

        $creditMemoManager->persist($creditMemo)->shouldBeCalled();
        $creditMemoManager->flush()->shouldBeCalled();

        $creditMemoPdfFileGenerator->generate('7903c83a-4c5e-4bcf-81d8-9dc304c6a353')->shouldNotBeCalled();

        $event = new CreditMemoGenerated('2018/01/000001', '000666');
        $eventBus->dispatch($event)->willReturn(new Envelope($event))->shouldBeCalled();

        $this(new GenerateCreditMemo('000666', 7000, $orderItemUnitRefunds, $shipmentRefunds, 'Comment'));
    }

    function it_generates_only_credit_memo_without_a_pdf_file_if_pdf_generation_is_disabled(
        CreditMemoGeneratorInterface $creditMemoGenerator,
        ObjectManager $creditMemoManager,
        MessageBusInterface $eventBus,
        OrderRepositoryInterface $orderRepository,
        CreditMemoPdfFileGeneratorInterface $creditMemoPdfFileGenerator,
        CreditMemoFileManagerInterface $creditMemoFileManager,
        CreditMemoInterface $creditMemo,
        OrderInterface $order
    ): void {
        $this->beConstructedWith(
            $creditMemoGenerator,
            $creditMemoManager,
            $eventBus,
            $orderRepository,
            false,
            $creditMemoPdfFileGenerator,
            $creditMemoFileManager,
        );

        $orderItemUnitRefunds = [new OrderItemUnitRefund(1, 1000), new OrderItemUnitRefund(3, 2000), new OrderItemUnitRefund(5, 3000)];
        $shipmentRefunds = [new ShipmentRefund(3, 1000)];

        $orderRepository->findOneByNumber('000666')->willReturn($order);

        $creditMemoGenerator->generate($order, 7000, $orderItemUnitRefunds, $shipmentRefunds, 'Comment')->willReturn($creditMemo);

        $creditMemo->getNumber()->willReturn('2018/01/000001');
        $creditMemo->getId()->willReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');

        $creditMemoManager->persist($creditMemo)->shouldBeCalled();
        $creditMemoManager->flush()->shouldBeCalled();

        $creditMemoPdfFileGenerator->generate('7903c83a-4c5e-4bcf-81d8-9dc304c6a353')->shouldNotBeCalled();

        $event = new CreditMemoGenerated('2018/01/000001', '000666');
        $eventBus->dispatch($event)->willReturn(new Envelope($event))->shouldBeCalled();

        $this(new GenerateCreditMemo('000666', 7000, $orderItemUnitRefunds, $shipmentRefunds, 'Comment'));
    }

    function it_deprecates_not_passing_credit_memo_pdf_file_generator_and_manager(
        CreditMemoGeneratorInterface $creditMemoGenerator,
        ObjectManager $creditMemoManager,
        MessageBusInterface $eventBus,
        OrderRepositoryInterface $orderRepository,
    ): void {
        $this->beConstructedWith(
            $creditMemoGenerator,
            $creditMemoManager,
            $eventBus,
            $orderRepository,
        );

        $this->shouldTrigger(
            \E_USER_DEPRECATED,
            'Not passing a $creditMemoPdfFileGenerator to Sylius\RefundPlugin\CommandHandler\GenerateCreditMemoHandler constructor is deprecated since sylius/refund-plugin 1.3 and will be prohibited in 2.0.'
        )->duringInstantiation();

        $this->shouldTrigger(
            \E_USER_DEPRECATED,
            'Not passing a $creditMemoFileManager to Sylius\RefundPlugin\CommandHandler\GenerateCreditMemoHandler constructor is deprecated since sylius/refund-plugin 1.3 and will be prohibited in 2.0.'
        )->duringInstantiation();
    }
}
