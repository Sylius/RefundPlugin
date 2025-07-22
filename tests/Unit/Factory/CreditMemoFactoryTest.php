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
use PHPUnit\Framework\MockObject\MockObject;
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
    private FactoryInterface&MockObject $creditMemoFactory;

    private CreditMemoIdentifierGeneratorInterface&MockObject $creditMemoIdentifierGenerator;

    private CreditMemoNumberGeneratorInterface&MockObject $creditMemoNumberGenerator;

    private CurrentDateTimeImmutableProviderInterface&MockObject $currentDateTimeImmutableProvider;

    private CreditMemoFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();
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
        self::assertInstanceOf(CreditMemoFactoryInterface::class, $this->factory);
    }

    public function testItCreatesANewCreditMemo(): void
    {
        $creditMemo = $this->createMock(CreditMemoInterface::class);

        $this->creditMemoFactory->expects(self::once())
            ->method('createNew')
            ->willReturn($creditMemo);

        $result = $this->factory->createNew();

        self::assertSame($creditMemo, $result);
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

        $this->creditMemoIdentifierGenerator->expects(self::once())
            ->method('generate')
            ->willReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');

        $this->creditMemoNumberGenerator->expects(self::once())
            ->method('generate')
            ->with($order, $dateTime)
            ->willReturn('2018/07/00001111');

        $this->currentDateTimeImmutableProvider->expects(self::once())
            ->method('now')
            ->willReturn($dateTime);

        $order->expects(self::once())
            ->method('getChannel')
            ->willReturn($channel);

        $order->expects(self::once())
            ->method('getCurrencyCode')
            ->willReturn('USD');

        $order->expects(self::once())
            ->method('getLocaleCode')
            ->willReturn('en_US');

        $this->creditMemoFactory->expects(self::once())
            ->method('createNew')
            ->willReturn($creditMemo);

        $creditMemo->expects(self::once())
            ->method('setId')
            ->with('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');

        $creditMemo->expects(self::once())
            ->method('setNumber')
            ->with('2018/07/00001111');

        $creditMemo->expects(self::once())
            ->method('setOrder')
            ->with($order);

        $creditMemo->expects(self::once())
            ->method('setChannel')
            ->with($channel);

        $creditMemo->expects(self::once())
            ->method('setCurrencyCode')
            ->with('USD');

        $creditMemo->expects(self::once())
            ->method('setLocaleCode')
            ->with('en_US');

        $creditMemo->expects(self::once())
            ->method('setTotal')
            ->with(1400);

        $creditMemo->expects(self::once())
            ->method('setLineItems')
            ->with($this->callback(function (ArrayCollection $lineItems) use ($firstLineItem, $secondLineItem) {
                return $lineItems->contains($firstLineItem) && $lineItems->contains($secondLineItem);
            }));

        $creditMemo->expects(self::once())
            ->method('setTaxItems')
            ->with($this->callback(function (ArrayCollection $taxItems) use ($taxItem) {
                return $taxItems->contains($taxItem);
            }));

        $creditMemo->expects(self::once())
            ->method('setComment')
            ->with('Comment');

        $creditMemo->expects(self::once())
            ->method('setIssuedAt')
            ->with($dateTime);

        $creditMemo->expects(self::once())
            ->method('setFrom')
            ->with($from);

        $creditMemo->expects(self::once())
            ->method('setTo')
            ->with($to);

        $result = $this->factory->createWithData($order, 1400, [$firstLineItem, $secondLineItem], [$taxItem], 'Comment', $from, $to);

        self::assertSame($creditMemo, $result);
    }
}
