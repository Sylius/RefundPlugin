<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Unit\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Entity\CustomerBillingDataInterface;
use Sylius\RefundPlugin\Entity\LineItemInterface;
use Sylius\RefundPlugin\Entity\ShopBillingDataInterface;
use Sylius\RefundPlugin\Entity\TaxItemInterface;
use Sylius\RefundPlugin\Factory\CreditMemoFactory;
use Sylius\RefundPlugin\Factory\CreditMemoFactoryInterface;
use Sylius\RefundPlugin\Generator\CreditMemoIdentifierGeneratorInterface;
use Sylius\RefundPlugin\Generator\CreditMemoNumberGeneratorInterface;
use Sylius\RefundPlugin\Provider\CurrentDateTimeImmutableProviderInterface;

final class CreditMemoFactoryTest extends TestCase
{
    private FactoryInterface $creditMemoFactory;
    private CreditMemoIdentifierGeneratorInterface $creditMemoIdentifierGenerator;
    private CreditMemoNumberGeneratorInterface $creditMemoNumberGenerator;
    private CurrentDateTimeImmutableProviderInterface $currentDateTimeImmutableProvider;
    private CreditMemoFactory $factory;

    protected function setUp(): void
    {
        $this->creditMemoFactory = $this->createMock(FactoryInterface::class);
        $this->creditMemoIdentifierGenerator = $this->createMock(CreditMemoIdentifierGeneratorInterface::class);
        $this->creditMemoNumberGenerator = $this->createMock(CreditMemoNumberGeneratorInterface::class);
        $this->currentDateTimeImmutableProvider = $this->createMock(CurrentDateTimeImmutableProviderInterface::class);

        $this->factory = new CreditMemoFactory(
            $this->creditMemoFactory,
            $this->creditMemoIdentifierGenerator,
            $this->creditMemoNumberGenerator,
            $this->currentDateTimeImmutableProvider,
        );
    }

    public function testItImplementsACreditMemoFactoryInterface(): void
    {
        $this->assertInstanceOf(CreditMemoFactoryInterface::class, $this->factory);
    }

    public function testItCreatesANewCreditMemo(): void
    {
        $creditMemo = $this->createMock(CreditMemoInterface::class);

        $this->creditMemoFactory->expects($this->once())
            ->method('createNew')
            ->willReturn($creditMemo);

        $result = $this->factory->createNew();

        $this->assertSame($creditMemo, $result);
    }

    public function testItCreatesANewCreditMemoWithData(): void
    {
        $creditMemo = $this->createMock(CreditMemoInterface::class);
        $order = $this->createMock(OrderInterface::class);
        $channel = $this->createMock(ChannelInterface::class);
        $firstLineItem = $this->createMock(LineItemInterface::class);
        $secondLineItem = $this->createMock(LineItemInterface::class);
        $taxItem = $this->createMock(TaxItemInterface::class);
        $from = $this->createMock(CustomerBillingDataInterface::class);
        $to = $this->createMock(ShopBillingDataInterface::class);

        $dateTime = new \DateTimeImmutable('01-01-2020 10:10:10');

        $this->creditMemoIdentifierGenerator->expects($this->once())
            ->method('generate')
            ->willReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');

        $this->creditMemoNumberGenerator->expects($this->once())
            ->method('generate')
            ->with($order, $dateTime)
            ->willReturn('2018/07/00001111');

        $this->currentDateTimeImmutableProvider->expects($this->once())
            ->method('now')
            ->willReturn($dateTime);

        $order->expects($this->once())
            ->method('getChannel')
            ->willReturn($channel);

        $order->expects($this->once())
            ->method('getCurrencyCode')
            ->willReturn('USD');

        $order->expects($this->once())
            ->method('getLocaleCode')
            ->willReturn('en_US');

        $this->creditMemoFactory->expects($this->once())
            ->method('createNew')
            ->willReturn($creditMemo);

        $creditMemo->expects($this->once())
            ->method('setId')
            ->with('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');

        $creditMemo->expects($this->once())
            ->method('setNumber')
            ->with('2018/07/00001111');

        $creditMemo->expects($this->once())
            ->method('setOrder')
            ->with($order);

        $creditMemo->expects($this->once())
            ->method('setChannel')
            ->with($channel);

        $creditMemo->expects($this->once())
            ->method('setCurrencyCode')
            ->with('USD');

        $creditMemo->expects($this->once())
            ->method('setLocaleCode')
            ->with('en_US');

        $creditMemo->expects($this->once())
            ->method('setTotal')
            ->with(1400);

        $creditMemo->expects($this->once())
            ->method('setLineItems')
            ->with($this->callback(function (ArrayCollection $lineItems) use ($firstLineItem, $secondLineItem) {
                return $lineItems->contains($firstLineItem) && $lineItems->contains($secondLineItem);
            }));

        $creditMemo->expects($this->once())
            ->method('setTaxItems')
            ->with($this->callback(function (ArrayCollection $taxItems) use ($taxItem) {
                return $taxItems->contains($taxItem);
            }));

        $creditMemo->expects($this->once())
            ->method('setComment')
            ->with('Comment');

        $creditMemo->expects($this->once())
            ->method('setIssuedAt')
            ->with($dateTime);

        $creditMemo->expects($this->once())
            ->method('setFrom')
            ->with($from);

        $creditMemo->expects($this->once())
            ->method('setTo')
            ->with($to);

        $result = $this->factory->createWithData($order, 1400, [$firstLineItem, $secondLineItem], [$taxItem], 'Comment', $from, $to);

        $this->assertSame($creditMemo, $result);
    }
}