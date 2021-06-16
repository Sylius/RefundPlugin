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
    public function it_implements_shop_billing_data_interface(): void
    {
        $this->shouldImplement(ShopBillingDataInterface::class);
    }

    public function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_has_company(): void
    {
        $this->setCompany('Needful Things');
        $this->getCompany()->shouldReturn('Needful Things');
    }

    public function it_has_tax_id(): void
    {
        $this->setTaxId('000222');
        $this->getTaxId()->shouldReturn('000222');
    }

    public function it_has_country_code(): void
    {
        $this->setCountryCode('US');
        $this->getCountryCode()->shouldReturn('US');
    }

    public function it_has_street(): void
    {
        $this->setStreet('Main St. 123');
        $this->getStreet()->shouldReturn('Main St. 123');
    }

    public function it_has_city(): void
    {
        $this->setCity('Los Angeles');
        $this->getCity()->shouldReturn('Los Angeles');
    }

    public function it_has_postcode(): void
    {
        $this->setPostcode('90001');
        $this->getPostcode()->shouldReturn('90001');
    }
}
