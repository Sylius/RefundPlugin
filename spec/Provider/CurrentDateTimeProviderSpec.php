<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Provider\CurrentDateTimeProviderInterface;

final class CurrentDateTimeProviderSpec extends ObjectBehavior
{
    function it_implements_current_date_time_provider_interface(): void
    {
        $this->shouldImplement(CurrentDateTimeProviderInterface::class);
    }

    function it_provides_current_date_and_time(): void
    {
        $this->now()->shouldReturnDate(new \DateTime());
    }

    public function getMatchers(): array
    {
        return [
            'returnDate' => function (\DateTimeInterface $dateTime): bool {
                $now = new \DateTime();

                return $dateTime->format('d/m/Y H:i:s') === $now->format('d/m/Y H:i:s');
            },
        ];
    }
}
