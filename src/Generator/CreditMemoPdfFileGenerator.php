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
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Exception\CreditMemoNotFound;
use Sylius\RefundPlugin\Model\CreditMemoPdf;
use Symfony\Component\Config\FileLocatorInterface;
use Twig\Environment;
use Webmozart\Assert\Assert;

final class CreditMemoPdfFileGenerator implements CreditMemoPdfFileGeneratorInterface
{
    private const FILE_EXTENSION = '.pdf';

    public function __construct(
        private RepositoryInterface $creditMemoRepository,
        private ?Environment $twig,
        private ?GeneratorInterface $pdfGenerator,
        private FileLocatorInterface $fileLocator,
        private string $template,
        private string $creditMemoLogoPath,
        private ?PdfOptionsGeneratorInterface $pdfOptionsGenerator = null,
        private ?TwigToPdfGeneratorInterface $twigToPdfGenerator = null,
        private ?CreditMemoFileNameGeneratorInterface $creditMemoFileNameGenerator = null,
    ) {
        $this->checkDeprecations();
    }

    public function generate(string $creditMemoId): CreditMemoPdf
    {
        /** @var CreditMemoInterface|null $creditMemo */
        $creditMemo = $this->creditMemoRepository->find($creditMemoId);

        if ($creditMemo === null) {
            throw CreditMemoNotFound::withId($creditMemoId);
        }

        $pdf = $this->generateFromTemplate([
            'creditMemo' => $creditMemo,
            'creditMemoLogoPath' => $this->fileLocator->locate($this->creditMemoLogoPath),
        ]);

        return new CreditMemoPdf($this->generateFileName($creditMemo), $pdf);
    }

    private function generateFromTemplate(array $templateParams): string
    {
        if (null !== $this->twigToPdfGenerator) {
            return $this->twigToPdfGenerator->generate($this->template, $templateParams);
        }

        if (null !== $this->pdfGenerator && null !== $this->twig) {
            return $this->pdfGenerator->getOutputFromHtml(
                $this->twig->render($this->template, $templateParams),
                $this->pdfOptionsGenerator ? $this->pdfOptionsGenerator->generate() : [],
            );
        }

        throw new \LogicException(sprintf('You must pass at least $twigToPdfGenerator to %s constructor.', self::class));
    }

    private function generateFileName(CreditMemoInterface $creditMemo): string
    {
        if (null !== $this->creditMemoFileNameGenerator) {
            return $this->creditMemoFileNameGenerator->generateForPdf($creditMemo);
        }

        $number = $creditMemo->getNumber();
        Assert::notNull($number);

        return str_replace('/', '_', $number) . self::FILE_EXTENSION;
    }

    private function checkDeprecations(): void
    {
        if (null !== $this->twig) {
            @trigger_error(sprintf('Passing %s as the second argument to %s constructor is deprecated since sylius/refund-plugin 1.2 and will be prohibited in 2.0.', Environment::class, self::class), \E_USER_DEPRECATED);
        }

        if (null !== $this->pdfGenerator) {
            @trigger_error(sprintf('Passing %s as the third argument to %s constructor is deprecated since sylius/refund-plugin 1.2 and will be prohibited in 2.0.', GeneratorInterface::class, self::class), \E_USER_DEPRECATED);
        }

        if (null !== $this->pdfOptionsGenerator) {
            @trigger_error(sprintf('Passing %s as the seventh argument to %s constructor is deprecated since sylius/refund-plugin 1.2 and will be prohibited in 2.0. You should pass $twigToPdfGenerator instead.', PdfOptionsGeneratorInterface::class, self::class), \E_USER_DEPRECATED);
        }

        if (null === $this->twigToPdfGenerator) {
            @trigger_error(sprintf('Not passing a $twigToPdfGenerator to %s constructor is deprecated since sylius/refund-plugin 1.2 and will be prohibited in 2.0.', self::class), \E_USER_DEPRECATED);
        }

        if (null === $this->creditMemoFileNameGenerator) {
            @trigger_error(sprintf('Not passing a $creditMemoFileNameGenerator to %s constructor is deprecated since sylius/refund-plugin 1.3 and will be prohibited in 2.0.', self::class), \E_USER_DEPRECATED);
        }
    }
}
