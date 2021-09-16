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

    private Environment $twig;

    private GeneratorInterface $pdfGenerator;

    private FileLocatorInterface $fileLocator;

    private string $template;

    private string $creditMemoLogoPath;

    public function __construct(
        RepositoryInterface $creditMemoRepository,
        Environment $twig,
        GeneratorInterface $pdfGenerator,
        FileLocatorInterface $fileLocator,
        string $template,
        string $creditMemoLogoPath
    ) {
        $this->creditMemoRepository = $creditMemoRepository;
        $this->twig = $twig;
        $this->pdfGenerator = $pdfGenerator;
        $this->fileLocator = $fileLocator;
        $this->template = $template;
        $this->creditMemoLogoPath = $creditMemoLogoPath;
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

        $pdf = $this->pdfGenerator->getOutputFromHtml($this->twig->render($this->template, [
            'creditMemo' => $creditMemo,
            'creditMemoLogoPath' => $this->fileLocator->locate($this->creditMemoLogoPath),
        ]));

        return new CreditMemoPdf($filename, $pdf);
    }
}
