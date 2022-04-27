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

namespace Tests\Sylius\RefundPlugin\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Resource\Factory\Factory;
use Sylius\RefundPlugin\DependencyInjection\Configuration;
use Sylius\RefundPlugin\Doctrine\ORM\CreditMemoRepository;
use Sylius\RefundPlugin\Entity\CreditMemo;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Entity\CustomerBillingData;
use Sylius\RefundPlugin\Entity\CustomerBillingDataInterface;
use Sylius\RefundPlugin\Entity\LineItem;
use Sylius\RefundPlugin\Entity\LineItemInterface;
use Sylius\RefundPlugin\Entity\Refund;
use Sylius\RefundPlugin\Entity\RefundInterface;
use Sylius\RefundPlugin\Entity\RefundPayment;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Sylius\RefundPlugin\Entity\ShopBillingData;
use Sylius\RefundPlugin\Entity\ShopBillingDataInterface;
use Sylius\RefundPlugin\Entity\TaxItem;
use Sylius\RefundPlugin\Entity\TaxItemInterface;
use Sylius\RefundPlugin\Factory\CreditMemoFactory;
use Sylius\RefundPlugin\Factory\CustomerBillingDataFactory;
use Sylius\RefundPlugin\Factory\RefundFactory;
use Sylius\RefundPlugin\Factory\RefundPaymentFactory;
use Sylius\RefundPlugin\Factory\ShopBillingDataFactory;

final class SyliusRefundConfigurationTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /** @test */
    public function it_does_not_define_any_allowed_files_by_default_and_it_loads_resources_correctly(): void
    {
        $this->assertProcessedConfigurationEquals(
            [],
            [
                'pdf_generator' => ['allowed_files' => []],
                'resources' => $this->getExpectedResourceConfiguration()
            ],
            'pdf_generator.allowed_files'
        );
    }

    /** @test */
    public function it_allows_to_define_allowed_files(): void
    {

        $this->assertProcessedConfigurationEquals(
            [['pdf_generator' => ['allowed_files' => ['swans.png', 'product.png']]]],
            ['pdf_generator' => ['allowed_files' => ['swans.png', 'product.png']]],
            'pdf_generator.allowed_files'
        );
    }

    /** @test */
    public function it_has_enabled_pdf_generator_by_default(): void
    {
        $this->assertProcessedConfigurationEquals(
            [],
            ['pdf_generator' => ['enabled' => true]],
            'pdf_generator.enabled'
        );
    }

    /** @test */
    public function it_allows_to_disable_pdf_generator(): void
    {
        $this->assertProcessedConfigurationEquals(
            [['pdf_generator' => ['enabled' => false]]],
            [
                'pdf_generator' => ['allowed_files' => ['swans.png', 'product.png']],
                'resources' => $this->getExpectedResourceConfiguration()
            ],
            'pdf_generator.enabled'
        );
    }

    protected function getConfiguration(): Configuration
    {
        return new Configuration();
    }

    private function getExpectedResourceConfiguration(): array
    {
        return [
            'credit_memo' => [
                'classes' => [
                    'model' => CreditMemo::class,
                    'interface' => CreditMemoInterface::class,
                    'controller' => ResourceController::class,
                    'factory' => CreditMemoFactory::class,
                    'repository' => CreditMemoRepository::class
                ]
            ],
            'line_item' => [
                'classes' => [
                    'model' => LineItem::class,
                    'interface' => LineItemInterface::class,
                    'controller' => ResourceController::class,
                    'factory' => Factory::class,
                ]
            ],
            'tax_item' => [
                'classes' => [
                    'model' => TaxItem::class,
                    'interface' => TaxItemInterface::class,
                    'controller' => ResourceController::class,
                    'factory' => Factory::class,
                ]
            ],
            'refund' => [
                'classes' => [
                    'model' => Refund::class,
                    'interface' => RefundInterface::class,
                    'controller' => ResourceController::class,
                    'factory' => RefundFactory::class,
                ]
            ],
            'refund_payment' => [
                'classes' => [
                    'model' => RefundPayment::class,
                    'interface' => RefundPaymentInterface::class,
                    'controller' => ResourceController::class,
                    'factory' => RefundPaymentFactory::class,
                ]
            ],
            'customer_billing_data' => [
                'classes' => [
                    'model' => CustomerBillingData::class,
                    'interface' => CustomerBillingDataInterface::class,
                    'controller' => ResourceController::class,
                    'factory' => CustomerBillingDataFactory::class,
                ]
            ],
            'shop_billing_data' => [
                'classes' => [
                    'model' => ShopBillingData::class,
                    'interface' => ShopBillingDataInterface::class,
                    'controller' => ResourceController::class,
                    'factory' => ShopBillingDataFactory::class,
                ]
            ]
        ];
    }
}
