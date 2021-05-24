<?php

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
use Sylius\RefundPlugin\Generator\NumberGenerator;
use Sylius\RefundPlugin\Provider\CurrentDateTimeImmutableProviderInterface;
use Webmozart\Assert\Assert;

final class CreditMemoFactory implements CreditMemoFactoryInterface
{
    /** @var FactoryInterface */
    private $creditMemoFactory;

    /** @var CreditMemoIdentifierGeneratorInterface */
    private $creditMemoIdentifierGenerator;

    /** @var NumberGenerator */
    private $creditMemoNumberGenerator;

    /** @var CurrentDateTimeImmutableProviderInterface */
    private $currentDateTimeImmutableProvider;

    public function __construct(
        FactoryInterface $creditMemoFactory,
        CreditMemoIdentifierGeneratorInterface $creditMemoIdentifierGenerator,
        NumberGenerator $creditMemoNumberGenerator,
        CurrentDateTimeImmutableProviderInterface $currentDateTimeImmutableProvider
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
        ?ShopBillingDataInterface $to
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

        $creditMemo = $this->createNew();
        $creditMemo->setId($this->creditMemoIdentifierGenerator->generate());
        $creditMemo->setNumber($this->creditMemoNumberGenerator->generate());
        $creditMemo->setOrder($order);
        $creditMemo->setChannel($channel);
        $creditMemo->setCurrencyCode($currencyCode);
        $creditMemo->setLocaleCode($localeCode);
        $creditMemo->setTotal($total);
        $creditMemo->setLineItems(new ArrayCollection($lineItems));
        $creditMemo->setTaxItems(new ArrayCollection($taxItems));
        $creditMemo->setComment($comment);
        $creditMemo->setIssuedAt($this->currentDateTimeImmutableProvider->now());
        $creditMemo->setFrom($from);
        $creditMemo->setTo($to);

        return $creditMemo;
    }
}
