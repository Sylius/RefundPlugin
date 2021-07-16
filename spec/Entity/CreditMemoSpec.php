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

namespace spec\Sylius\RefundPlugin\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Entity\CustomerBillingDataInterface;
use Sylius\RefundPlugin\Entity\LineItemInterface;
use Sylius\RefundPlugin\Entity\ShopBillingDataInterface;
use Sylius\RefundPlugin\Entity\TaxItemInterface;

final class CreditMemoSpec extends ObjectBehavior
{
    function it_implements_a_credit_memo_interface(): void
    {
        $this->shouldImplement(CreditMemoInterface::class);
    }

    function it_has_an_id(): void
    {
        $this->setId('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');
        $this->getId()->shouldReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');
    }

    function it_has_a_number(): void
    {
        $this->setNumber('2018/07/00003333');
        $this->getNumber()->shouldReturn('2018/07/00003333');
    }

    function it_has_an_order(OrderInterface $order): void
    {
        $this->setOrder($order);
        $this->getOrder()->shouldReturn($order);
    }

    function it_has_a_total(): void
    {
        $this->setTotal(1000);
        $this->getTotal()->shouldReturn(1000);
    }

    function it_has_a_currency_code(): void
    {
        $this->setCurrencyCode('USD');
        $this->getCurrencyCode()->shouldReturn('USD');
    }

    function it_has_a_locale_code(): void
    {
        $this->setLocaleCode('en_US');
        $this->getLocaleCode()->shouldReturn('en_US');
    }

    function it_has_a_channel(ChannelInterface $channel): void
    {
        $this->setChannel($channel);
        $this->getChannel()->shouldReturn($channel);
    }

    function it_has_line_items(LineItemInterface $lineItem): void
    {
        $this->setLineItems(new ArrayCollection([$lineItem->getWrappedObject()]));
        $this->getLineItems()->shouldBeLike(new ArrayCollection([$lineItem->getWrappedObject()]));
    }

    function it_has_tax_items(TaxItemInterface $taxItem): void
    {
        $this->setTaxItems(new ArrayCollection([$taxItem->getWrappedObject()]));
        $this->getTaxItems()->shouldBeLike(new ArrayCollection([$taxItem->getWrappedObject()]));
    }

    function it_has_a_date_of_creation(): void
    {
        $this->setIssuedAt(new \DateTimeImmutable('01-01-2020 10:10:10'));
        $this->getIssuedAt()->shouldBeLike(new \DateTimeImmutable('01-01-2020 10:10:10'));
    }

    function it_has_a_comment(): void
    {
        $this->setComment('Comment');
        $this->getComment()->shouldReturn('Comment');
    }

    function it_has_a_from_address(CustomerBillingDataInterface $from): void
    {
        $this->setFrom($from);
        $this->getFrom()->shouldReturn($from);
    }

    function it_has_a_to_address(ShopBillingDataInterface $to): void
    {
        $this->setTo($to);
        $this->getTo()->shouldReturn($to);
    }
}
