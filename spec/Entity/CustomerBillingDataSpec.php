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
    public function let(): void
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

    public function it_implements_customer_billing_data_interface(): void
    {
        $this->shouldImplement(CustomerBillingDataInterface::class);
    }

    public function it_has_no_id_by_default(): void
    {
        $this->id()->shouldReturn(null);
    }

    public function it_has_first_name(): void
    {
        $this->firstName()->shouldReturn('Rick');
    }

    public function it_has_last_name(): void
    {
        $this->lastName()->shouldReturn('Sanchez');
    }

    public function it_has_full_name(): void
    {
        $this->fullName()->shouldReturn('Rick Sanchez');
    }

    public function it_has_company(): void
    {
        $this->company()->shouldReturn('Curse Purge Plus!');
    }

    public function it_has_street(): void
    {
        $this->street()->shouldReturn('Main St. 3322');
    }

    public function it_has_postcode(): void
    {
        $this->postcode()->shouldReturn('90802');
    }

    public function it_has_country_code(): void
    {
        $this->countryCode()->shouldReturn('US');
    }

    public function it_has_city(): void
    {
        $this->city()->shouldReturn('Los Angeles');
    }

    public function it_has_province_name(): void
    {
        $this->provinceName()->shouldReturn('Baldwin Hills');
    }

    public function it_has_province_code(): void
    {
        $this->provinceCode()->shouldReturn('323');
    }
}
