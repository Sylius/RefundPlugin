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

namespace spec\Sylius\RefundPlugin\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Entity\CustomerBillingDataInterface;
use Sylius\RefundPlugin\Entity\LineItemInterface;
use Sylius\RefundPlugin\Entity\ShopBillingDataInterface;
use Sylius\RefundPlugin\Entity\TaxItemInterface;
use Sylius\RefundPlugin\Factory\CreditMemoFactoryInterface;
use Sylius\RefundPlugin\Generator\CreditMemoIdentifierGeneratorInterface;
use Sylius\RefundPlugin\Generator\CreditMemoNumberGeneratorInterface;
use Sylius\RefundPlugin\Provider\CurrentDateTimeImmutableProviderInterface;

final class CreditMemoFactorySpec extends ObjectBehavior
{
    function let(
        FactoryInterface $creditMemoFactory,
        CreditMemoIdentifierGeneratorInterface $creditMemoIdentifierGenerator,
        CreditMemoNumberGeneratorInterface $creditMemoNumberGenerator,
        CurrentDateTimeImmutableProviderInterface $currentDateTimeImmutableProvider,
    ): void {
        $this->beConstructedWith(
            $creditMemoFactory,
            $creditMemoIdentifierGenerator,
            $creditMemoNumberGenerator,
            $currentDateTimeImmutableProvider,
        );
    }

    function it_implements_a_credit_memo_factory_interface(): void
    {
        $this->shouldImplement(CreditMemoFactoryInterface::class);
    }

    function it_creates_a_new_credit_memo(
        FactoryInterface $creditMemoFactory,
        CreditMemoInterface $creditMemo,
    ): void {
        $creditMemoFactory->createNew()->willReturn($creditMemo);

        $this->createNew()->shouldReturn($creditMemo);
    }

    function it_creates_a_new_credit_memo_with_data(
        FactoryInterface $creditMemoFactory,
        CreditMemoIdentifierGeneratorInterface $creditMemoIdentifierGenerator,
        CreditMemoNumberGeneratorInterface $creditMemoNumberGenerator,
        CurrentDateTimeImmutableProviderInterface $currentDateTimeImmutableProvider,
        CreditMemoInterface $creditMemo,
        OrderInterface $order,
        ChannelInterface $channel,
        LineItemInterface $firstLineItem,
        LineItemInterface $secondLineItem,
        TaxItemInterface $taxItem,
        CustomerBillingDataInterface $from,
        ShopBillingDataInterface $to,
    ): void {
        $creditMemoIdentifierGenerator->generate()->willReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');
        $creditMemoNumberGenerator->generate($order, new \DateTimeImmutable('01-01-2020 10:10:10'))->willReturn('2018/07/00001111');
        $currentDateTimeImmutableProvider->now()->willReturn(new \DateTimeImmutable('01-01-2020 10:10:10'));

        $order->getChannel()->willReturn($channel);
        $order->getCurrencyCode()->willReturn('USD');
        $order->getLocaleCode()->willReturn('en_US');

        $creditMemoFactory->createNew()->willReturn($creditMemo);
        $creditMemo->setId('7903c83a-4c5e-4bcf-81d8-9dc304c6a353')->shouldBeCalled();
        $creditMemo->setNumber('2018/07/00001111')->shouldBeCalled();
        $creditMemo->setOrder($order)->shouldBeCalled();
        $creditMemo->setChannel($channel)->shouldBeCalled();
        $creditMemo->setCurrencyCode('USD')->shouldBeCalled();
        $creditMemo->setLocaleCode('en_US')->shouldBeCalled();
        $creditMemo->setTotal(1400)->shouldBeCalled();
        $creditMemo->setLineItems(new ArrayCollection([$firstLineItem->getWrappedObject(), $secondLineItem->getWrappedObject()]))->shouldBeCalled();
        $creditMemo->setTaxItems(new ArrayCollection([$taxItem->getWrappedObject()]))->shouldBeCalled();
        $creditMemo->setComment('Comment')->shouldBeCalled();
        $creditMemo->setIssuedAt(new \DateTimeImmutable('01-01-2020 10:10:10'))->shouldBeCalled();
        $creditMemo->setFrom($from)->shouldBeCalled();
        $creditMemo->setTo($to)->shouldBeCalled();

        $this
            ->createWithData($order, 1400, [$firstLineItem, $secondLineItem], [$taxItem], 'Comment', $from, $to)
            ->shouldBeLike($creditMemo)
        ;
    }
}
