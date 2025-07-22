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

namespace Tests\Sylius\RefundPlugin\Unit\Model;

use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Model\CreditMemoPdf;

final class CreditMemoPdfTest extends TestCase
{
    /** @test */
    public function it_has_filename(): void
    {
        $creditMemoPdf = new CreditMemoPdf('2018_01_0000002.pdf', 'pdf content');

        self::assertEquals('2018_01_0000002.pdf', $creditMemoPdf->filename());
    }

    /** @test */
    public function it_has_content(): void
    {
        $creditMemoPdf = new CreditMemoPdf('2018_01_0000002.pdf', 'pdf content');

        self::assertEquals('pdf content', $creditMemoPdf->content());
    }
}
