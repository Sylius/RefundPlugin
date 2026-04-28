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

namespace Sylius\RefundPlugin\Generator;

use Knp\Snappy\GeneratorInterface;
use Twig\Environment;

/**
 * @deprecated since sylius/refund-plugin 2.1, use Sylius\PdfGenerationBundle\Core\Renderer\TwigToPdfRendererInterface from sylius/pdf-generation-bundle instead.
 */
final readonly class TwigToPdfGenerator implements TwigToPdfGeneratorInterface
{
    public function __construct(
        private Environment $twig,
        private GeneratorInterface $pdfGenerator,
        private PdfOptionsGeneratorInterface $pdfOptionsGenerator,
    ) {
    }

    /** @param array<string, mixed> $templateParams */
    public function generate(string $templateName, array $templateParams): string
    {
        return $this->pdfGenerator->getOutputFromHtml(
            $this->twig->render($templateName, $templateParams),
            $this->pdfOptionsGenerator->generate(),
        );
    }
}
