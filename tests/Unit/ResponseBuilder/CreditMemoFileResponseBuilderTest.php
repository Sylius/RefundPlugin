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

namespace Tests\Sylius\RefundPlugin\Unit\ResponseBuilder;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\RefundPlugin\Model\CreditMemoPdf;
use Sylius\RefundPlugin\ResponseBuilder\CreditMemoFileResponseBuilder;
use Sylius\RefundPlugin\ResponseBuilder\CreditMemoFileResponseBuilderInterface;
use Symfony\Component\HttpFoundation\Response;

final class CreditMemoFileResponseBuilderTest extends TestCase
{
    private CreditMemoFileResponseBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new CreditMemoFileResponseBuilder();
    }

    #[Test]
    public function it_is_initializable(): void
    {
        self::assertInstanceOf(CreditMemoFileResponseBuilder::class, $this->builder);
    }

    #[Test]
    public function it_implements_credit_memo_file_response_builder_interface(): void
    {
        self::assertInstanceOf(CreditMemoFileResponseBuilderInterface::class, $this->builder);
    }

    #[Test]
    public function it_returns_response_containing_pdf_file_when_its_provided(): void
    {
        $creditMemoPdf = new CreditMemoPdf('credit_memo.pdf', 'credit_memo_content');

        $response = $this->builder->build(Response::HTTP_OK, $creditMemoPdf);

        self::assertEquals('credit_memo_content', $response->getContent());
        self::assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}
