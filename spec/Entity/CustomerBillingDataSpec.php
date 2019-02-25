<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Entity;

use PhpSpec\ObjectBehavior;

final class CustomerBillingDataSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(
            'Rick Sanchez',
            'Main St. 3322',
            '90802',
            'US',
            'Los Angeles',
            'Curse Purge Plus!',
            'Baldwin Hills',
            '323'
        );
    }

    function it_has_customer_name(): void
    {
        $this->customerName()->shouldReturn('Rick Sanchez');
    }

    function it_has_company(): void
    {
        $this->company()->shouldReturn('Curse Purge Plus!');
    }

    function it_has_street(): void
    {
        $this->street()->shouldReturn('Main St. 3322');
    }

    function it_has_postcode(): void
    {
        $this->postcode()->shouldReturn('90802');
    }

    function it_has_country_code(): void
    {
        $this->countryCode()->shouldReturn('US');
    }

    function it_has_city(): void
    {
        $this->city()->shouldReturn('Los Angeles');
    }

    function it_has_province_name(): void
    {
        $this->provinceName()->shouldReturn('Baldwin Hills');
    }

    function it_has_province_code(): void
    {
        $this->provinceCode()->shouldReturn('323');
    }
}
