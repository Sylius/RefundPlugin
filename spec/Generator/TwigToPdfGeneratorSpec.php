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

namespace spec\Sylius\RefundPlugin\Generator;

use Knp\Snappy\GeneratorInterface;
use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Generator\TwigToPdfGeneratorInterface;
use Twig\Environment;

final class TwigToPdfGeneratorSpec extends ObjectBehavior
{
    function let(
        Environment $twig,
        GeneratorInterface $pdfGenerator
    ): void {
        $this->beConstructedWith(
            $twig,
            $pdfGenerator
        );
    }

    function it_is_twig_to_pdf_generator_interface(): void
    {
        $this->shouldImplement(TwigToPdfGeneratorInterface::class);
    }

    function it_generates_pdf_from_twig_template(
        Environment $twig,
        GeneratorInterface $pdfGenerator
    ): void {
        $twig
            ->render('template.html.twig', ['figcaption' => 'Swans', 'imgPath' => 'located-path/swans.png'])
            ->willReturn('<html>I am a pdf file generated from twig template</html>')
        ;

        $pdfGenerator
            ->getOutputFromHtml(
                '<html>I am a pdf file generated from twig template</html>',
                ['allow' => ['located-path/swans.png']]
            )
            ->willReturn('PDF FILE')
        ;

        $this
            ->generate('template.html.twig', ['figcaption' => 'Swans', 'imgPath' => 'located-path/swans.png'], ['imgPath'])
            ->shouldBeLike('PDF FILE')
        ;
    }
}
