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

namespace spec\Sylius\RefundPlugin\Model;

use PhpSpec\ObjectBehavior;

final class CreditMemoPdfSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('2018_01_0000002.pdf', 'pdf content');
    }

    function it_has_filename(): void
    {
        $this->filename()->shouldReturn('2018_01_0000002.pdf');
    }

    function it_has_content(): void
    {
        $this->content()->shouldReturn('pdf content');
    }
}
