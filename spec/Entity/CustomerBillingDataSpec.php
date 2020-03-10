<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Entity;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Entity\CustomerBillingDataInterface;

final class CustomerBillingDataSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(
            'Rick',
            'Sanchez',
            'Main St. 3322',
            '90802',
            'US',
            'Los Angeles',
            'Curse Purge Plus!',
            'Baldwin Hills',
            '323'
        );
    }

    function it_implements_customer_billing_data_interface(): void
    {
        $this->shouldImplement(CustomerBillingDataInterface::class);
    }

    function it_has_no_id_by_default(): void
    {
        $this->id()->shouldReturn(null);
    }

    function it_has_first_name(): void
    {
        $this->firstName()->shouldReturn('Rick');
    }

    function it_has_last_name(): void
    {
        $this->lastName()->shouldReturn('Sanchez');
    }

    function it_has_full_name(): void
    {
        $this->fullName()->shouldReturn('Rick Sanchez');
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
