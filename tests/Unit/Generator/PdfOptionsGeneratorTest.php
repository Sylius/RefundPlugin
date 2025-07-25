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

namespace Sylius\RefundPlugin\Tests\Unit\Generator;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Generator\PdfOptionsGenerator;
use Sylius\RefundPlugin\Generator\PdfOptionsGeneratorInterface;
use Symfony\Component\Config\FileLocatorInterface;

final class PdfOptionsGeneratorTest extends TestCase
{
    private FileLocatorInterface&MockObject $fileLocator;

    private PdfOptionsGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileLocator = $this->createMock(FileLocatorInterface::class);

        $this->generator = new PdfOptionsGenerator(
            $this->fileLocator,
            ['allow' => 'allowed_file_in_knp_snappy_config.png'],
            ['swans.png'],
        );
    }

    #[Test]
    public function it_is_pdf_options_generator_interface(): void
    {
        self::assertInstanceOf(PdfOptionsGeneratorInterface::class, $this->generator);
    }

    #[Test]
    public function it_generates_pdf_options(): void
    {
        $this->fileLocator->expects(self::once())
            ->method('locate')
            ->with('swans.png')
            ->willReturn('located-path/swans.png');

        $result = $this->generator->generate();

        self::assertSame([
            'allow' => [
                'allowed_file_in_knp_snappy_config.png',
                'located-path/swans.png',
            ],
        ], $result);
    }
}
