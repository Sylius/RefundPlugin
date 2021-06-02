<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Model;

use PhpSpec\ObjectBehavior;

final class CreditMemoPdfSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith('2018_01_0000002.pdf', 'pdf content');
    }

    public function it_has_filename(): void
    {
        $this->filename()->shouldReturn('2018_01_0000002.pdf');
    }

    public function it_has_content(): void
    {
        $this->content()->shouldReturn('pdf content');
    }
}
