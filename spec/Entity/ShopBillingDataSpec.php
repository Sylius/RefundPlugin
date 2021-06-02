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
    public function let(): void
    {
        $this->beConstructedWith('Needful Things', '000222', 'US', 'Main St. 123', 'Los Angeles', '90001');
    }

    public function it_implements_shop_billing_data_interface(): void
    {
        $this->shouldImplement(ShopBillingDataInterface::class);
    }

    public function it_has_no_id_by_default(): void
    {
        $this->id()->shouldReturn(null);
    }

    public function it_has_company(): void
    {
        $this->company()->shouldReturn('Needful Things');
    }

    public function it_has_tax_id(): void
    {
        $this->taxId()->shouldReturn('000222');
    }

    public function it_has_country_code(): void
    {
        $this->countryCode()->shouldReturn('US');
    }

    public function it_has_street(): void
    {
        $this->street()->shouldReturn('Main St. 123');
    }

    public function it_has_city(): void
    {
        $this->city()->shouldReturn('Los Angeles');
    }

    public function it_has_postcode(): void
    {
        $this->postcode()->shouldReturn('90001');
    }
}
