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
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Exception\CreditMemoNotFound;
use Sylius\PdfGenerationBundle\Core\Renderer\TwigToPdfRendererInterface;
use Sylius\RefundPlugin\Generator\CreditMemoFileNameGeneratorInterface;
use Sylius\RefundPlugin\Generator\CreditMemoPdfFileGenerator;
use Sylius\RefundPlugin\Generator\CreditMemoPdfFileGeneratorInterface;
use Sylius\RefundPlugin\Generator\TwigToPdfGeneratorInterface;
use Sylius\RefundPlugin\Model\CreditMemoPdf;
use Symfony\Component\Config\FileLocatorInterface;

final class CreditMemoPdfFileGeneratorTest extends TestCase
{
    private RepositoryInterface&MockObject $creditMemoRepository;

    private FileLocatorInterface&MockObject $fileLocator;

    private TwigToPdfGeneratorInterface&MockObject $twigToPdfGenerator;

    private CreditMemoFileNameGeneratorInterface&MockObject $creditMemoFileNameGenerator;

    private CreditMemoPdfFileGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->creditMemoRepository = $this->createMock(RepositoryInterface::class);
        $this->fileLocator = $this->createMock(FileLocatorInterface::class);
        $this->twigToPdfGenerator = $this->createMock(TwigToPdfGeneratorInterface::class);
        $this->creditMemoFileNameGenerator = $this->createMock(CreditMemoFileNameGeneratorInterface::class);

        $this->generator = new CreditMemoPdfFileGenerator(
            $this->creditMemoRepository,
            $this->fileLocator,
            'creditMemoTemplate.html.twig',
            '@SyliusRefundPlugin/assets/sylius-logo.png',
            $this->twigToPdfGenerator,
            $this->creditMemoFileNameGenerator,
        );
    }

    #[Test]
    public function it_implements_credit_memo_pdf_file_generator_interface(): void
    {
        self::assertInstanceOf(CreditMemoPdfFileGeneratorInterface::class, $this->generator);
    }

    #[Test]
    public function it_creates_credit_memo_pdf_with_generated_content_and_file_name_basing_on_credit_memo_number(): void
    {
        $creditMemo = $this->createMock(CreditMemoInterface::class);

        $this->creditMemoRepository->expects(self::once())
            ->method('find')
            ->with('7903c83a-4c5e-4bcf-81d8-9dc304c6a353')
            ->willReturn($creditMemo);

        $creditMemo->expects($this->never())
            ->method('getNumber');

        $this->creditMemoFileNameGenerator->expects(self::once())
            ->method('generateForPdf')
            ->with($creditMemo)
            ->willReturn('2015_05_00004444.pdf');

        $logoPath = __DIR__ . '/../../../assets/sylius-logo.png';
        $logoDataUri = sprintf(
            'data:%s;base64,%s',
            mime_content_type($logoPath) ?: 'image/png',
            base64_encode((string) file_get_contents($logoPath)),
        );

        $this->fileLocator->expects(self::once())
            ->method('locate')
            ->with('@SyliusRefundPlugin/assets/sylius-logo.png')
            ->willReturn($logoPath);

        $this->twigToPdfGenerator->expects(self::once())
            ->method('generate')
            ->with('creditMemoTemplate.html.twig', [
                'creditMemo' => $creditMemo,
                'creditMemoLogoPath' => $logoPath,
                'creditMemoLogo' => $logoDataUri,
            ])
            ->willReturn('PDF FILE');

        $result = $this->generator->generate('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');

        self::assertEquals(new CreditMemoPdf('2015_05_00004444.pdf', 'PDF FILE'), $result);
    }

    #[Test]
    public function it_throws_exception_if_credit_memo_with_given_id_has_not_been_found(): void
    {
        $this->creditMemoRepository->expects(self::once())
            ->method('find')
            ->with('7903c83a-4c5e-4bcf-81d8-9dc304c6a353')
            ->willReturn(null);

        $this->expectException(CreditMemoNotFound::class);

        $this->generator->generate('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');
    }

    #[Test]
    public function it_generates_credit_memo_pdf_using_pdf_bundle_renderer(): void
    {
        $twigToPdfRenderer = $this->createMock(TwigToPdfRendererInterface::class);

        $generator = new CreditMemoPdfFileGenerator(
            $this->creditMemoRepository,
            $this->fileLocator,
            'creditMemoTemplate.html.twig',
            '@SyliusRefundPlugin/assets/sylius-logo.png',
            $twigToPdfRenderer,
            $this->creditMemoFileNameGenerator,
        );

        $creditMemo = $this->createMock(CreditMemoInterface::class);

        $this->creditMemoRepository->expects(self::once())
            ->method('find')
            ->with('7903c83a-4c5e-4bcf-81d8-9dc304c6a353')
            ->willReturn($creditMemo);

        $this->creditMemoFileNameGenerator->expects(self::once())
            ->method('generateForPdf')
            ->with($creditMemo)
            ->willReturn('2015_05_00004444.pdf');

        $logoPath = __DIR__ . '/../../../assets/sylius-logo.png';
        $logoDataUri = sprintf(
            'data:%s;base64,%s',
            mime_content_type($logoPath) ?: 'image/png',
            base64_encode((string) file_get_contents($logoPath)),
        );

        $this->fileLocator->expects(self::once())
            ->method('locate')
            ->with('@SyliusRefundPlugin/assets/sylius-logo.png')
            ->willReturn($logoPath);

        $twigToPdfRenderer->expects(self::once())
            ->method('render')
            ->with('creditMemoTemplate.html.twig', [
                'creditMemo' => $creditMemo,
                'creditMemoLogoPath' => $logoPath,
                'creditMemoLogo' => $logoDataUri,
            ], 'sylius_refund')
            ->willReturn('PDF FILE');

        $result = $generator->generate('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');

        self::assertEquals(new CreditMemoPdf('2015_05_00004444.pdf', 'PDF FILE'), $result);
    }
}
