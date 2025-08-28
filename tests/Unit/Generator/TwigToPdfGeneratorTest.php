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

use Knp\Snappy\GeneratorInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Generator\PdfOptionsGeneratorInterface;
use Sylius\RefundPlugin\Generator\TwigToPdfGenerator;
use Sylius\RefundPlugin\Generator\TwigToPdfGeneratorInterface;
use Twig\Environment;

final class TwigToPdfGeneratorTest extends TestCase
{
    private TwigToPdfGenerator $generator;

    private Environment&MockObject $twig;

    private GeneratorInterface&MockObject $pdfGenerator;

    private PdfOptionsGeneratorInterface&MockObject $pdfOptionsGenerator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->twig = $this->createMock(Environment::class);
        $this->pdfGenerator = $this->createMock(GeneratorInterface::class);
        $this->pdfOptionsGenerator = $this->createMock(PdfOptionsGeneratorInterface::class);

        $this->generator = new TwigToPdfGenerator(
            $this->twig,
            $this->pdfGenerator,
            $this->pdfOptionsGenerator,
        );
    }

    #[Test]
    public function it_implements_twig_to_pdf_generator_interface(): void
    {
        self::assertInstanceOf(TwigToPdfGeneratorInterface::class, $this->generator);
    }

    #[Test]
    public function it_generates_pdf_from_twig_template(): void
    {
        $this->twig->expects(self::once())
            ->method('render')
            ->with('template.html.twig', ['figcaption' => 'Swans', 'imgPath' => 'located-path/swans.png'])
            ->willReturn('<html>I am a pdf file generated from twig template</html>');

        $this->pdfOptionsGenerator->expects(self::once())
            ->method('generate')
            ->willReturn(['allow' => ['allowed_file_in_knp_snappy_config.png', 'located-path/swans.png']]);

        $this->pdfGenerator->expects(self::once())
            ->method('getOutputFromHtml')
            ->with(
                '<html>I am a pdf file generated from twig template</html>',
                ['allow' => ['allowed_file_in_knp_snappy_config.png', 'located-path/swans.png']],
            )
            ->willReturn('PDF FILE');

        $result = $this->generator->generate('template.html.twig', ['figcaption' => 'Swans', 'imgPath' => 'located-path/swans.png']);

        self::assertSame('PDF FILE', $result);
    }
}
