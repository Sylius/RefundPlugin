<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Generator;

use Knp\Snappy\GeneratorInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Exception\CreditMemoNotFound;
use Sylius\RefundPlugin\Model\CreditMemoPdf;
use Symfony\Component\Templating\EngineInterface;

final class CreditMemoPdfFileGenerator implements CreditMemoPdfFileGeneratorInterface
{
    private const FILE_EXTENSION = '.pdf';

    /** @var RepositoryInterface */
    private $creditMemoRepository;

    /** @var EngineInterface */
    private $twig;

    /** @var GeneratorInterface */
    private $pdfGenerator;

    /** @var string */
    private $template;

    public function __construct(
        RepositoryInterface $creditMemoRepository,
        EngineInterface $twig,
        GeneratorInterface $pdfGenerator,
        string $template
    ) {
        $this->creditMemoRepository = $creditMemoRepository;
        $this->twig = $twig;
        $this->pdfGenerator = $pdfGenerator;
        $this->template = $template;
    }

    public function generate(string $creditMemoId): CreditMemoPdf
    {
        /** @var CreditMemoInterface|null $creditMemo */
        $creditMemo = $this->creditMemoRepository->find($creditMemoId);

        if ($creditMemo === null) {
            throw CreditMemoNotFound::withId($creditMemoId);
        }

        $filename = str_replace('/', '_', $creditMemo->getNumber()) . self::FILE_EXTENSION;

        $pdf = $this->pdfGenerator->getOutputFromHtml(
            $this->twig->render($this->template, ['creditMemo' => $creditMemo])
        );

        return new CreditMemoPdf($filename, $pdf);
    }
}
