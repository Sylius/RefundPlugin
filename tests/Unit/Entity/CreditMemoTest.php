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

namespace Tests\Sylius\RefundPlugin\Unit\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\RefundPlugin\Entity\CreditMemo;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Entity\CustomerBillingDataInterface;
use Sylius\RefundPlugin\Entity\LineItemInterface;
use Sylius\RefundPlugin\Entity\ShopBillingDataInterface;
use Sylius\RefundPlugin\Entity\TaxItemInterface;

final class CreditMemoTest extends TestCase
{
    private CreditMemo $creditMemo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->creditMemo = new CreditMemo();
    }

    #[Test]
    public function it_implements_a_credit_memo_interface(): void
    {
        self::assertInstanceOf(CreditMemoInterface::class, $this->creditMemo);
    }

    #[Test]
    public function it_has_an_id(): void
    {
        $this->creditMemo->setId('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');
        self::assertEquals('7903c83a-4c5e-4bcf-81d8-9dc304c6a353', $this->creditMemo->getId());
    }

    #[Test]
    public function it_has_a_number(): void
    {
        $this->creditMemo->setNumber('2018/07/00003333');
        self::assertEquals('2018/07/00003333', $this->creditMemo->getNumber());
    }

    #[Test]
    public function it_has_an_order(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $this->creditMemo->setOrder($order);
        self::assertSame($order, $this->creditMemo->getOrder());
    }

    #[Test]
    public function it_has_a_total(): void
    {
        $this->creditMemo->setTotal(1000);
        self::assertEquals(1000, $this->creditMemo->getTotal());
    }

    #[Test]
    public function it_has_a_currency_code(): void
    {
        $this->creditMemo->setCurrencyCode('USD');
        self::assertEquals('USD', $this->creditMemo->getCurrencyCode());
    }

    #[Test]
    public function it_has_a_locale_code(): void
    {
        $this->creditMemo->setLocaleCode('en_US');
        self::assertEquals('en_US', $this->creditMemo->getLocaleCode());
    }

    #[Test]
    public function it_has_a_channel(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $this->creditMemo->setChannel($channel);
        self::assertSame($channel, $this->creditMemo->getChannel());
    }

    #[Test]
    public function it_has_line_items(): void
    {
        $lineItem = $this->createMock(LineItemInterface::class);
        $lineItems = new ArrayCollection([$lineItem]);
        $this->creditMemo->setLineItems($lineItems);
        self::assertEquals($lineItems, $this->creditMemo->getLineItems());
    }

    #[Test]
    public function it_has_tax_items(): void
    {
        $taxItem = $this->createMock(TaxItemInterface::class);
        $taxItems = new ArrayCollection([$taxItem]);
        $this->creditMemo->setTaxItems($taxItems);
        self::assertEquals($taxItems, $this->creditMemo->getTaxItems());
    }

    #[Test]
    public function it_has_a_date_of_creation(): void
    {
        $issuedAt = new \DateTimeImmutable('01-01-2020 10:10:10');
        $this->creditMemo->setIssuedAt($issuedAt);
        self::assertEquals($issuedAt, $this->creditMemo->getIssuedAt());
    }

    #[Test]
    public function it_has_a_comment(): void
    {
        $this->creditMemo->setComment('Comment');
        self::assertEquals('Comment', $this->creditMemo->getComment());
    }

    #[Test]
    public function it_has_a_from_address(): void
    {
        $from = $this->createMock(CustomerBillingDataInterface::class);
        $this->creditMemo->setFrom($from);
        self::assertSame($from, $this->creditMemo->getFrom());
    }

    #[Test]
    public function it_has_a_to_address(): void
    {
        $to = $this->createMock(ShopBillingDataInterface::class);
        $this->creditMemo->setTo($to);
        self::assertSame($to, $this->creditMemo->getTo());
    }
}
