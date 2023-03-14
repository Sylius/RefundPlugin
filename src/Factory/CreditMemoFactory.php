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

namespace Sylius\RefundPlugin\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Entity\CustomerBillingDataInterface;
use Sylius\RefundPlugin\Entity\ShopBillingDataInterface;
use Sylius\RefundPlugin\Generator\CreditMemoIdentifierGeneratorInterface;
use Sylius\RefundPlugin\Generator\CreditMemoNumberGeneratorInterface;
use Sylius\RefundPlugin\Provider\CurrentDateTimeImmutableProviderInterface;
use Webmozart\Assert\Assert;

final class CreditMemoFactory implements CreditMemoFactoryInterface
{
    private FactoryInterface $creditMemoFactory;

    private CreditMemoIdentifierGeneratorInterface $creditMemoIdentifierGenerator;

    private CreditMemoNumberGeneratorInterface $creditMemoNumberGenerator;

    private CurrentDateTimeImmutableProviderInterface $currentDateTimeImmutableProvider;

    public function __construct(
        FactoryInterface $creditMemoFactory,
        CreditMemoIdentifierGeneratorInterface $creditMemoIdentifierGenerator,
        CreditMemoNumberGeneratorInterface $creditMemoNumberGenerator,
        CurrentDateTimeImmutableProviderInterface $currentDateTimeImmutableProvider,
    ) {
        $this->creditMemoFactory = $creditMemoFactory;
        $this->creditMemoIdentifierGenerator = $creditMemoIdentifierGenerator;
        $this->creditMemoNumberGenerator = $creditMemoNumberGenerator;
        $this->currentDateTimeImmutableProvider = $currentDateTimeImmutableProvider;
    }

    public function createNew(): CreditMemoInterface
    {
        /** @var CreditMemoInterface $creditMemo */
        $creditMemo = $this->creditMemoFactory->createNew();

        return $creditMemo;
    }

    public function createWithData(
        OrderInterface $order,
        int $total,
        array $lineItems,
        array $taxItems,
        string $comment,
        CustomerBillingDataInterface $from,
        ?ShopBillingDataInterface $to,
    ): CreditMemoInterface {
        /** @var ChannelInterface|null $channel */
        $channel = $order->getChannel();
        Assert::notNull($channel);

        /** @var string|null $currencyCode */
        $currencyCode = $order->getCurrencyCode();
        Assert::notNull($currencyCode);

        /** @var string|null $localeCode */
        $localeCode = $order->getLocaleCode();
        Assert::notNull($localeCode);

        $issuedAt = $this->currentDateTimeImmutableProvider->now();

        $creditMemo = $this->createNew();
        $creditMemo->setId($this->creditMemoIdentifierGenerator->generate());
        $creditMemo->setNumber($this->creditMemoNumberGenerator->generate($order, $issuedAt));
        $creditMemo->setOrder($order);
        $creditMemo->setChannel($channel);
        $creditMemo->setCurrencyCode($currencyCode);
        $creditMemo->setLocaleCode($localeCode);
        $creditMemo->setTotal($total);
        $creditMemo->setLineItems(new ArrayCollection($lineItems));
        $creditMemo->setTaxItems(new ArrayCollection($taxItems));
        $creditMemo->setComment($comment);
        $creditMemo->setIssuedAt($issuedAt);
        $creditMemo->setFrom($from);
        $creditMemo->setTo($to);

        return $creditMemo;
    }
}
