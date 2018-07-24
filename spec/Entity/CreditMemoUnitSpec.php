<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Entity;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Entity\CreditMemoUnitInterface;

final class CreditMemoUnitSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('Microverse Battery', 10000, 990, 100);
    }

    function it_implements_credit_memo_unit_interface(): void
    {
        $this->shouldImplement(CreditMemoUnitInterface::class);
    }

    function it_has_product_name(): void
    {
        $this->getProductName()->shouldReturn('Microverse Battery');
    }

    function it_has_total(): void
    {
        $this->getTotal()->shouldReturn(10000);
    }

    function it_has_taxes_total(): void
    {
        $this->getTaxesTotal()->shouldReturn(990);
    }

    function it_has_discount(): void
    {
        $this->getDiscount()->shouldReturn(100);
    }

    function it_can_be_serialized(): void
    {
        $this->serialize()->shouldReturn(
            '{"product_name":"Microverse Battery","total":10000,"taxes_total":990,"discount":100}'
        );
    }

    function it_can_be_unserialized(): void
    {
        $this->unserialize('{"product_name":"Microverse Battery","total":10000,"taxes_total":990,"discount":100}');

        $this->getProductName()->shouldReturn('Microverse Battery');
        $this->getTotal()->shouldReturn(10000);
        $this->getTaxesTotal()->shouldReturn(990);
        $this->getDiscount()->shouldReturn(100);
    }
}
