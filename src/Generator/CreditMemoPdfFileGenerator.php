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

    private RepositoryInterface $creditMemoRepository;

    private ?Environment $twig;

    private ?GeneratorInterface $pdfGenerator;

    private FileLocatorInterface $fileLocator;

    private string $template;

    private string $creditMemoLogoPath;

    private ?TwigToPdfGeneratorInterface $twigToPdfGenerator;

    public function __construct(
        RepositoryInterface $creditMemoRepository,
        ?Environment $twig,
        ?GeneratorInterface $pdfGenerator,
        FileLocatorInterface $fileLocator,
        string $template,
        string $creditMemoLogoPath,
        ?TwigToPdfGeneratorInterface $twigToPdfGenerator = null
    ) {
        $this->creditMemoRepository = $creditMemoRepository;
        $this->twig = $twig;
        $this->pdfGenerator = $pdfGenerator;
        $this->fileLocator = $fileLocator;
        $this->template = $template;
        $this->creditMemoLogoPath = $creditMemoLogoPath;
        $this->twigToPdfGenerator = $twigToPdfGenerator;

        $this->checkDeprecations();
    }

    public function generate(string $creditMemoId): CreditMemoPdf
    {
        /** @var CreditMemoInterface|null $creditMemo */
        $creditMemo = $this->creditMemoRepository->find($creditMemoId);

        if ($creditMemo === null) {
            throw CreditMemoNotFound::withId($creditMemoId);
        }

        $number = $creditMemo->getNumber();
        Assert::notNull($number);

        $filename = str_replace('/', '_', $number) . self::FILE_EXTENSION;

        if (null !== $this->twigToPdfGenerator) {
            $pdf = $this->twigToPdfGenerator->generate(
                $this->template,
                [
                    'creditMemo' => $creditMemo,
                    'creditMemoLogoPath' => $this->fileLocator->locate($this->creditMemoLogoPath),
                ]
            );
        } elseif (null !== $this->pdfGenerator && null !== $this->twig) {
            $pdf = $this->pdfGenerator->getOutputFromHtml($this->twig->render($this->template, [
                'creditMemo' => $creditMemo,
                'creditMemoLogoPath' => $this->fileLocator->locate($this->creditMemoLogoPath),
            ]));
        } else {
            throw new \LogicException('At least one PDF generator must be passed.');
        }

        return new CreditMemoPdf($filename, $pdf);
    }

    private function checkDeprecations(): void
    {
        if ((null === $this->twig || null === $this->pdfGenerator) && null === $this->twigToPdfGenerator) {
            throw new \InvalidArgumentException(sprintf('You must pass at least $twigToPdfGenerator to %s constructor.', self::class));
        }

        if (null !== $this->twig) {
            @trigger_error('Passing Twig\Environment as the second argument is deprecated since sylius/refund-plugin 1.1 and will be prohibited in 2.0.', \E_USER_DEPRECATED);
        }

        if (null !== $this->pdfGenerator) {
            @trigger_error('Passing Knp\Snappy\GeneratorInterface as the third argument is deprecated since sylius/refund-plugin 1.1 and will be prohibited in 2.0.', \E_USER_DEPRECATED);
        }

        if (null === $this->twigToPdfGenerator) {
            @trigger_error(sprintf('Not passing a $twigToPdfGenerator to %s constructor is deprecated since sylius/refund-plugin 1.1 and will be prohibited in 2.0.', self::class), \E_USER_DEPRECATED);
        }
    }
}
