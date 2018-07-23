<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Entity;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;

final class CreditMemoSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('2018/07/00003333', '0000222', 1000, 'USD');
    }

    function it_implements_credit_memo_interface(): void
    {
        $this->shouldImplement(CreditMemoInterface::class);
    }

    function it_has_number(): void
    {
        $this->getNumber()->shouldReturn('2018/07/00003333');
    }

    function it_has_order_number(): void
    {
        $this->getOrderNumber()->shouldReturn('0000222');
    }

    function it_has_total(): void
    {
        $this->getTotal()->shouldReturn(1000);
    }

    function it_has_currency_code(): void
    {
        $this->getCurrencyCode()->shouldReturn('USD');
    }
}
