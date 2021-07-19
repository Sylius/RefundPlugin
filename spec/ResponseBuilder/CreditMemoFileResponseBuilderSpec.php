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

namespace spec\Sylius\RefundPlugin\ResponseBuilder;

use PhpSpec\ObjectBehavior;
use Sylius\RefundPlugin\Model\CreditMemoPdf;
use Sylius\RefundPlugin\ResponseBuilder\CreditMemoFileResponseBuilder;
use Sylius\RefundPlugin\ResponseBuilder\CreditMemoFileResponseBuilderInterface;
use Symfony\Component\HttpFoundation\Response;

final class CreditMemoFileResponseBuilderSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(CreditMemoFileResponseBuilder::class);
    }

    function it_implements_credit_memo_file_response_builder_interface(): void
    {
        $this->shouldImplement(CreditMemoFileResponseBuilderInterface::class);
    }

    function it_returns_response_containing_pdf_file_when_its_provided(): void
    {
        $creditMemoPdf = new CreditMemoPdf('credit_memo.pdf', 'credit_memo_content');

        $response = $this->build(Response::HTTP_OK, $creditMemoPdf);

        $response->getContent()->shouldBeEqualTo('credit_memo_content');
        $response->getStatusCode()->shouldBeEqualTo(Response::HTTP_OK);
    }
}
