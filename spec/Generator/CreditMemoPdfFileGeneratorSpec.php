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
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Exception\CreditMemoNotFound;
use Sylius\RefundPlugin\Generator\CreditMemoFileNameGeneratorInterface;
use Sylius\RefundPlugin\Generator\CreditMemoPdfFileGeneratorInterface;
use Sylius\RefundPlugin\Generator\PdfOptionsGeneratorInterface;
use Sylius\RefundPlugin\Generator\TwigToPdfGeneratorInterface;
use Sylius\RefundPlugin\Model\CreditMemoPdf;
use Symfony\Component\Config\FileLocatorInterface;
use Twig\Environment;

final class CreditMemoPdfFileGeneratorSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $creditMemoRepository,
        FileLocatorInterface $fileLocator,
        TwigToPdfGeneratorInterface $twigToPdfGenerator,
        CreditMemoFileNameGeneratorInterface $creditMemoFileNameGenerator,
    ): void {
        $this->beConstructedWith(
            $creditMemoRepository,
            null,
            null,
            $fileLocator,
            'creditMemoTemplate.html.twig',
            '@SyliusRefundPlugin/Resources/assets/sylius-logo.png',
            null,
            $twigToPdfGenerator,
            $creditMemoFileNameGenerator,
        );
    }

    function it_implements_credit_memo_pdf_file_generator_interface(): void
    {
        $this->shouldImplement(CreditMemoPdfFileGeneratorInterface::class);
    }

    function it_creates_credit_memo_pdf_with_generated_content_and_file_name_basing_on_credit_memo_number(
        RepositoryInterface $creditMemoRepository,
        FileLocatorInterface $fileLocator,
        TwigToPdfGeneratorInterface $twigToPdfGenerator,
        CreditMemoFileNameGeneratorInterface $creditMemoFileNameGenerator,
        CreditMemoInterface $creditMemo,
    ): void {
        $creditMemoRepository->find('7903c83a-4c5e-4bcf-81d8-9dc304c6a353')->willReturn($creditMemo);
        $creditMemo->getNumber()->willReturn('2015/05/00004444');

        $creditMemoFileNameGenerator->generateForPdf($creditMemo)->willReturn('2015_05_00004444.pdf');

        $fileLocator
            ->locate('@SyliusRefundPlugin/Resources/assets/sylius-logo.png')
            ->willReturn('located-path/sylius-logo.png')
        ;

        $twigToPdfGenerator
            ->generate('creditMemoTemplate.html.twig', ['creditMemo' => $creditMemo, 'creditMemoLogoPath' => 'located-path/sylius-logo.png'])
            ->willReturn('PDF FILE')
        ;

        $this
            ->generate('7903c83a-4c5e-4bcf-81d8-9dc304c6a353')
            ->shouldBeLike(new CreditMemoPdf('2015_05_00004444.pdf', 'PDF FILE'))
        ;
    }

    function it_throws_exception_if_credit_memo_with_given_id_has_not_been_found(
        RepositoryInterface $creditMemoRepository,
    ): void {
        $creditMemoRepository->find('7903c83a-4c5e-4bcf-81d8-9dc304c6a353')->willReturn(null);

        $this
            ->shouldThrow(CreditMemoNotFound::withId('7903c83a-4c5e-4bcf-81d8-9dc304c6a353'))
            ->during('generate', ['7903c83a-4c5e-4bcf-81d8-9dc304c6a353'])
        ;
    }

    function it_deprecates_not_passing_twig_to_pdf_generator(
        RepositoryInterface $creditMemoRepository,
        Environment $twig,
        GeneratorInterface $pdfGenerator,
        FileLocatorInterface $fileLocator,
    ): void {
        $this->beConstructedWith(
            $creditMemoRepository,
            $twig,
            $pdfGenerator,
            $fileLocator,
            'creditMemoTemplate.html.twig',
            '@SyliusRefundPlugin/Resources/assets/sylius-logo.png',
        );

        $this->shouldTrigger(\E_USER_DEPRECATED, 'Not passing a $twigToPdfGenerator to Sylius\RefundPlugin\Generator\CreditMemoPdfFileGenerator constructor is deprecated since sylius/refund-plugin 1.2 and will be prohibited in 2.0.')->duringInstantiation();
    }

    function it_deprecates_not_passing_credit_memo_file_name_generator(
        RepositoryInterface $creditMemoRepository,
        Environment $twig,
        GeneratorInterface $pdfGenerator,
        FileLocatorInterface $fileLocator,
        TwigToPdfGeneratorInterface $twigToPdfGenerator,
    ): void {
        $this->beConstructedWith(
            $creditMemoRepository,
            $twig,
            $pdfGenerator,
            $fileLocator,
            'creditMemoTemplate.html.twig',
            '@SyliusRefundPlugin/Resources/assets/sylius-logo.png',
            null,
            $twigToPdfGenerator,
        );

        $this->shouldTrigger(\E_USER_DEPRECATED, 'Not passing a $creditMemoFileNameGenerator to Sylius\RefundPlugin\Generator\CreditMemoPdfFileGenerator constructor is deprecated since sylius/refund-plugin 1.3 and will be prohibited in 2.0.')->duringInstantiation();
    }

    function it_deprecates_passing_pdf_options_generator(
        RepositoryInterface $creditMemoRepository,
        Environment $twig,
        GeneratorInterface $pdfGenerator,
        FileLocatorInterface $fileLocator,
        PdfOptionsGeneratorInterface $pdfOptionsGenerator,
    ): void {
        $this->beConstructedWith(
            $creditMemoRepository,
            $twig,
            $pdfGenerator,
            $fileLocator,
            'creditMemoTemplate.html.twig',
            '@SyliusRefundPlugin/Resources/assets/sylius-logo.png',
            $pdfOptionsGenerator,
        );

        $this->shouldTrigger(\E_USER_DEPRECATED, 'Passing Twig\Environment as the second argument to Sylius\RefundPlugin\Generator\CreditMemoPdfFileGenerator constructor is deprecated since sylius/refund-plugin 1.2 and will be prohibited in 2.0.')->duringInstantiation();
        $this->shouldTrigger(\E_USER_DEPRECATED, 'Passing Knp\Snappy\GeneratorInterface as the third argument to Sylius\RefundPlugin\Generator\CreditMemoPdfFileGenerator constructor is deprecated since sylius/refund-plugin 1.2 and will be prohibited in 2.0.')->duringInstantiation();
        $this->shouldTrigger(\E_USER_DEPRECATED, 'Passing Sylius\RefundPlugin\Generator\PdfOptionsGeneratorInterface as the seventh argument to Sylius\RefundPlugin\Generator\CreditMemoPdfFileGenerator constructor is deprecated since sylius/refund-plugin 1.2 and will be prohibited in 2.0.')->duringInstantiation();
    }

    function it_prohibits_not_passing_any_generator(
        RepositoryInterface $creditMemoRepository,
        Environment $twig,
        FileLocatorInterface $fileLocator,
    ): void {
        $this->beConstructedWith(
            $creditMemoRepository,
            $twig,
            null,
            $fileLocator,
            'creditMemoTemplate.html.twig',
            '@SyliusRefundPlugin/Resources/assets/sylius-logo.png',
        );

        $this->shouldThrow(\LogicException::class);
    }
}
