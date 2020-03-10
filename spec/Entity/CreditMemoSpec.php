<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Entity\CustomerBillingData;
use Sylius\RefundPlugin\Entity\LineItemInterface;
use Sylius\RefundPlugin\Entity\ShopBillingData;
use Sylius\RefundPlugin\Entity\TaxItemInterface;

final class CreditMemoSpec extends ObjectBehavior
{
    function let(
        OrderInterface $order,
        ChannelInterface $channel,
        LineItemInterface $lineItem,
        TaxItemInterface $taxItem
    ): void {
        $this->beConstructedWith(
            '7903c83a-4c5e-4bcf-81d8-9dc304c6a353',
            '2018/07/00003333',
            $order,
            1000,
            'USD',
            'en_US',
            $channel,
            [$lineItem->getWrappedObject()],
            [$taxItem->getWrappedObject()],
            'Comment',
            new \DateTime('01-01-2020 10:10:10'),
            new CustomerBillingData('Rick', 'Sanchez', 'Main St. 3322', '90802', 'US', 'Curse Purge Plus!', 'Los Angeles', 'Baldwin Hills', '323'),
            new ShopBillingData('Needful Things', '000222', 'US', 'Main St. 123', 'Los Angeles', '90001')
        );
    }

    function it_implements_a_credit_memo_interface(): void
    {
        $this->shouldImplement(CreditMemoInterface::class);
    }

    function it_has_id(): void
    {
        $this->getId()->shouldReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');
    }

    function it_has_number(): void
    {
        $this->getNumber()->shouldReturn('2018/07/00003333');
    }

    function it_has_an_order(OrderInterface $order): void
    {
        $this->getOrder()->shouldReturn($order);
    }

    function it_has_total(): void
    {
        $this->getTotal()->shouldReturn(1000);
    }

    function it_has_a_currency_code(): void
    {
        $this->getCurrencyCode()->shouldReturn('USD');
    }

    function it_has_a_locale_code(): void
    {
        $this->getLocaleCode()->shouldReturn('en_US');
    }

    function it_has_a_channel(ChannelInterface $channel): void
    {
        $this->getChannel()->shouldReturn($channel);
    }

    function it_has_line_items(LineItemInterface $lineItem): void
    {
        $this->getLineItems()->shouldBeLike(new ArrayCollection([$lineItem->getWrappedObject()]));
    }

    function it_has_tax_items(TaxItemInterface $taxItem): void
    {
        $this->getTaxItems()->shouldBeLike(new ArrayCollection([$taxItem->getWrappedObject()]));
    }

    function it_has_a_date_of_creation(): void
    {
        $this->getIssuedAt()->shouldBeLike(new \DateTime('01-01-2020 10:10:10'));
    }

    function it_has_a_comment(): void
    {
        $this->getComment()->shouldReturn('Comment');
    }

    function it_has_a_from_address(): void
    {
        $this
            ->getFrom()
            ->shouldBeLike(
                new CustomerBillingData('Rick', 'Sanchez', 'Main St. 3322', '90802', 'US', 'Curse Purge Plus!', 'Los Angeles', 'Baldwin Hills', '323')
            )
        ;
    }

    function it_has_a_to_address(): void
    {
        $this
            ->getTo()
            ->shouldBeLike(new ShopBillingData('Needful Things', '000222', 'US', 'Main St. 123', 'Los Angeles', '90001'))
        ;
    }
}
