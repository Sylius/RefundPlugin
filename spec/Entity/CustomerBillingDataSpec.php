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

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Entity\CustomerBillingDataInterface;

final class CustomerBillingDataSpec extends ObjectBehavior
{
    function it_implements_customer_billing_data_interface(): void
    {
        $this->shouldImplement(CustomerBillingDataInterface::class);
    }

    function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_an_id(): void
    {
        $this->setId(1234);
        $this->getId()->shouldReturn(1234);
    }

    function it_has_a_first_name(): void
    {
        $this->setFirstName('Rick');
        $this->getFirstName()->shouldReturn('Rick');
    }

    function it_has_a_last_name(): void
    {
        $this->setLastName('Sanchez');
        $this->getLastName()->shouldReturn('Sanchez');
    }

    function it_has_a_full_name(): void
    {
        $this->setFirstName('Rick');
        $this->setLastName('Sanchez');
        $this->getFullName()->shouldReturn('Rick Sanchez');
    }

    function it_has_a_company(): void
    {
        $this->setCompany('Curse Purge Plus!');
        $this->getCompany()->shouldReturn('Curse Purge Plus!');
    }

    function it_has_a_street(): void
    {
        $this->setStreet('Main St. 3322');
        $this->getStreet()->shouldReturn('Main St. 3322');
    }

    function it_has_a_postcode(): void
    {
        $this->setPostcode('90802');
        $this->getPostcode()->shouldReturn('90802');
    }

    function it_has_a_country_code(): void
    {
        $this->setCountryCode('US');
        $this->getCountryCode()->shouldReturn('US');
    }

    function it_has_a_city(): void
    {
        $this->setCity('Los Angeles');
        $this->getCity()->shouldReturn('Los Angeles');
    }

    function it_has_a_province_name(): void
    {
        $this->setProvinceName('Baldwin Hills');
        $this->getProvinceName()->shouldReturn('Baldwin Hills');
    }

    function it_has_a_province_code(): void
    {
        $this->setProvinceCode('323');
        $this->getProvinceCode()->shouldReturn('323');
    }
}
