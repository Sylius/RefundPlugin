<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Provider\CurrentDateTimeImmutableProvider;
use Sylius\RefundPlugin\Provider\CurrentDateTimeImmutableProviderInterface;

final class CurrentDateTimeImmutableProviderTest extends TestCase
{
    private CurrentDateTimeImmutableProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->provider = new CurrentDateTimeImmutableProvider();
    }

    /** @test */
    public function it_is_initializable(): void
    {
        self::assertInstanceOf(CurrentDateTimeImmutableProvider::class, $this->provider);
    }

    /** @test */
    public function it_implements_current_date_time_immutable_provider_interface(): void
    {
        self::assertInstanceOf(CurrentDateTimeImmutableProviderInterface::class, $this->provider);
    }

    /** @test */
    public function it_provides_current_immutable_date_and_time(): void
    {
        $now = new \DateTimeImmutable();
        $result = $this->provider->now();

        self::assertInstanceOf(\DateTimeImmutable::class, $result);

        // Check that the returned date is within a reasonable time frame (1 second)
        $diff = abs($result->getTimestamp() - $now->getTimestamp());
        self::assertLessThanOrEqual(1, $diff);

        // Alternative check using format comparison
        self::assertEquals($now->format('d/m/Y H:i:s'), $result->format('d/m/Y H:i:s'));
    }
}
