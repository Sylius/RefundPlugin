<?php

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Generator;

use Knp\Snappy\GeneratorInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Exception\CreditMemoNotFound;
use Sylius\RefundPlugin\Generator\CreditMemoPdfFileGeneratorInterface;
use Sylius\RefundPlugin\Model\CreditMemoPdf;
use Symfony\Component\Config\FileLocatorInterface;
use Twig\Environment;

final class CreditMemoPdfFileGeneratorSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $creditMemoRepository,
        Environment $twig,
        GeneratorInterface $pdfGenerator,
        FileLocatorInterface $fileLocator
    ): void {
        $this->beConstructedWith(
            $creditMemoRepository,
            $twig,
            $pdfGenerator,
            $fileLocator,
            'creditMemoTemplate.html.twig',
            '@SyliusRefundPlugin/Resources/assets/sylius-logo.png'
        );
    }

    function it_implements_credit_memo_pdf_file_generator_interface(): void
    {
        $this->shouldImplement(CreditMemoPdfFileGeneratorInterface::class);
    }

    function it_creates_credit_memo_pdf_with_generated_content_and_filename_basing_on_credit_memo_number(
        RepositoryInterface $creditMemoRepository,
        Environment $twig,
        GeneratorInterface $pdfGenerator,
        FileLocatorInterface $fileLocator,
        CreditMemoInterface $creditMemo
    ): void {
        $creditMemoRepository->find('7903c83a-4c5e-4bcf-81d8-9dc304c6a353')->willReturn($creditMemo);
        $creditMemo->getNumber()->willReturn('2015/05/00004444');

        $fileLocator
            ->locate('@SyliusRefundPlugin/Resources/assets/sylius-logo.png')
            ->willReturn('located-path/sylius-logo.png')
        ;

        $twig
            ->render('creditMemoTemplate.html.twig', ['creditMemo' => $creditMemo, 'creditMemoLogoPath' => 'located-path/sylius-logo.png'])
            ->willReturn('<html>I am a credit memo pdf file content</html>')
        ;

        $pdfGenerator->getOutputFromHtml('<html>I am a credit memo pdf file content</html>')->willReturn('PDF FILE');

        $this
            ->generate('7903c83a-4c5e-4bcf-81d8-9dc304c6a353')
            ->shouldBeLike(new CreditMemoPdf('2015_05_00004444.pdf', 'PDF FILE'))
        ;
    }

    function it_throws_exception_if_credit_memo_with_given_id_has_not_been_found(
        RepositoryInterface $creditMemoRepository
    ): void {
        $creditMemoRepository->find('7903c83a-4c5e-4bcf-81d8-9dc304c6a353')->willReturn(null);

        $this
            ->shouldThrow(CreditMemoNotFound::withId('7903c83a-4c5e-4bcf-81d8-9dc304c6a353'))
            ->during('generate', ['7903c83a-4c5e-4bcf-81d8-9dc304c6a353'])
        ;
    }
}
