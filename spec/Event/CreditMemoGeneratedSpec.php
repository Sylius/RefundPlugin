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

namespace spec\Sylius\RefundPlugin\Event;

use PhpSpec\ObjectBehavior;

final class CreditMemoGeneratedSpec extends ObjectBehavior
{
    function it_represents_an_immutable_fact_that_credit_memo_has_been_generated(): void
    {
        $this->beConstructedWith('2018/01/000001', '000222');

        $this->number()->shouldReturn('2018/01/000001');
        $this->orderNumber()->shouldReturn('000222');
    }
}
