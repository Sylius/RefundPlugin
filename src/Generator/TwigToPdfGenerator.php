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

namespace Sylius\RefundPlugin\Generator;

use Knp\Snappy\GeneratorInterface;
use Twig\Environment;

final class TwigToPdfGenerator implements TwigToPdfGeneratorInterface
{
    public function __construct(
        private Environment $twig,
        private GeneratorInterface $pdfGenerator
    ) {
    }

    public function generate(string $templateName, array $templateParams, array $fileParamNames): string
    {
        $allowedFiles = array_filter(
            $templateParams,
            fn ($key) => in_array($key, $fileParamNames),
            \ARRAY_FILTER_USE_KEY
        );

        return $this->pdfGenerator->getOutputFromHtml(
            $this->twig->render($templateName, $templateParams),
            ['allow' => array_values($allowedFiles)]
        );
    }
}
