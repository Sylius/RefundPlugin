<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Provider\CurrentDateTimeImmutableProviderInterface;

final class CurrentDateTimeImmutableProviderSpec extends ObjectBehavior
{
    function it_implements_current_date_time_immutable_provider_interface(): void
    {
        $this->shouldImplement(CurrentDateTimeImmutableProviderInterface::class);
    }

    function it_provides_current_immutable_date_and_time(): void
    {
        $this->now()->shouldReturnDate(new \DateTimeImmutable());
    }

    public function getMatchers(): array
    {
        return [
            'returnDate' => function (\DateTimeInterface $dateTime): bool {
                $now = new \DateTimeImmutable();

                return $dateTime->format('d/m/Y H:i:s') === $now->format('d/m/Y H:i:s');
            },
        ];
    }
}
