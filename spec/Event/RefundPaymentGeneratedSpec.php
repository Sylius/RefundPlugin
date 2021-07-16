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

final class RefundPaymentGeneratedSpec extends ObjectBehavior
{
    function it_represents_an_immutable_fact_that_refund_payment_has_been_generated(): void
    {
        $this->beConstructedWith(1, '000222', 10000, 'GBP', 2, 3);

        $this->id()->shouldReturn(1);
        $this->orderNumber()->shouldReturn('000222');
        $this->amount()->shouldReturn(10000);
        $this->currencyCode()->shouldReturn('GBP');
        $this->paymentMethodId()->shouldReturn(2);
        $this->paymentId()->shouldReturn(3);
    }
}
