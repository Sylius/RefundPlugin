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
use Sylius\RefundPlugin\Entity\ShopBillingDataInterface;

final class ShopBillingDataSpec extends ObjectBehavior
{
    function it_implements_shop_billing_data_interface(): void
    {
        $this->shouldImplement(ShopBillingDataInterface::class);
    }

    function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_a_company(): void
    {
        $this->setCompany('Needful Things');
        $this->getCompany()->shouldReturn('Needful Things');
    }

    function it_has_a_tax_id(): void
    {
        $this->setTaxId('000222');
        $this->getTaxId()->shouldReturn('000222');
    }

    function it_has_a_country_code(): void
    {
        $this->setCountryCode('US');
        $this->getCountryCode()->shouldReturn('US');
    }

    function it_has_a_street(): void
    {
        $this->setStreet('Main St. 123');
        $this->getStreet()->shouldReturn('Main St. 123');
    }

    function it_has_a_city(): void
    {
        $this->setCity('Los Angeles');
        $this->getCity()->shouldReturn('Los Angeles');
    }

    function it_has_a_postcode(): void
    {
        $this->setPostcode('90001');
        $this->getPostcode()->shouldReturn('90001');
    }
}
