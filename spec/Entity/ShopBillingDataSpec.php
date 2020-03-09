<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Entity;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Entity\ShopBillingDataInterface;

final class ShopBillingDataSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('Needful Things', '000222', 'US', 'Main St. 123', 'Los Angeles', '90001');
    }

    function it_implements_shop_billing_data_interface(): void
    {
        $this->shouldImplement(ShopBillingDataInterface::class);
    }

    function it_has_no_id_by_default(): void
    {
        $this->id()->shouldReturn(null);
    }

    function it_has_company(): void
    {
        $this->company()->shouldReturn('Needful Things');
    }

    function it_has_tax_id(): void
    {
        $this->taxId()->shouldReturn('000222');
    }

    function it_has_country_code(): void
    {
        $this->countryCode()->shouldReturn('US');
    }

    function it_has_street(): void
    {
        $this->street()->shouldReturn('Main St. 123');
    }

    function it_has_city(): void
    {
        $this->city()->shouldReturn('Los Angeles');
    }

    function it_has_postcode(): void
    {
        $this->postcode()->shouldReturn('90001');
    }
}
