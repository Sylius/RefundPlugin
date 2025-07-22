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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Generator\PdfOptionsGeneratorInterface;
use Sylius\RefundPlugin\Generator\TwigToPdfGenerator;
use Sylius\RefundPlugin\Generator\TwigToPdfGeneratorInterface;
use Twig\Environment;

final class TwigToPdfGeneratorTest extends TestCase
{
    private TwigToPdfGenerator $generator;
    
    /** @var Environment&MockObject */
    private Environment $twig;
    
    /** @var GeneratorInterface&MockObject */
    private GeneratorInterface $pdfGenerator;
    
    /** @var PdfOptionsGeneratorInterface&MockObject */
    private PdfOptionsGeneratorInterface $pdfOptionsGenerator;

    protected function setUp(): void
    {
        $this->twig = $this->createMock(Environment::class);
        $this->pdfGenerator = $this->createMock(GeneratorInterface::class);
        $this->pdfOptionsGenerator = $this->createMock(PdfOptionsGeneratorInterface::class);
        
        $this->generator = new TwigToPdfGenerator(
            $this->twig,
            $this->pdfGenerator,
            $this->pdfOptionsGenerator,
        );
    }

    public function testItImplementsTwigToPdfGeneratorInterface(): void
    {
        $this->assertInstanceOf(TwigToPdfGeneratorInterface::class, $this->generator);
    }

    public function testItGeneratesPdfFromTwigTemplate(): void
    {
        $this->twig->expects($this->once())
            ->method('render')
            ->with('template.html.twig', ['figcaption' => 'Swans', 'imgPath' => 'located-path/swans.png'])
            ->willReturn('<html>I am a pdf file generated from twig template</html>');

        $this->pdfOptionsGenerator->expects($this->once())
            ->method('generate')
            ->willReturn(['allow' => ['allowed_file_in_knp_snappy_config.png', 'located-path/swans.png']]);

        $this->pdfGenerator->expects($this->once())
            ->method('getOutputFromHtml')
            ->with(
                '<html>I am a pdf file generated from twig template</html>',
                ['allow' => ['allowed_file_in_knp_snappy_config.png', 'located-path/swans.png']]
            )
            ->willReturn('PDF FILE');

        $result = $this->generator->generate('template.html.twig', ['figcaption' => 'Swans', 'imgPath' => 'located-path/swans.png']);

        $this->assertSame('PDF FILE', $result);
    }
}