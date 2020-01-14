<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Generator;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopBillingDataInterface as ChannelShopBillingData;
use Sylius\RefundPlugin\Entity\CreditMemo;
use Sylius\RefundPlugin\Entity\CreditMemoInterface;
use Sylius\RefundPlugin\Entity\CustomerBillingData;
use Sylius\RefundPlugin\Entity\ShopBillingData;
use Sylius\RefundPlugin\Model\UnitRefundInterface;
use Sylius\RefundPlugin\Provider\CurrentDateTimeProviderInterface;

final class CreditMemoGenerator implements CreditMemoGeneratorInterface
{
    /** @var CreditMemoUnitGeneratorInterface */
    private $orderItemUnitCreditMemoUnitGenerator;

    /** @var CreditMemoUnitGeneratorInterface */
    private $shipmentCreditMemoUnitGenerator;

    /** @var NumberGenerator */
    private $creditMemoNumberGenerator;

    /** @var CurrentDateTimeProviderInterface */
    private $currentDateTimeProvider;

    /** @var CreditMemoIdentifierGeneratorInterface */
    private $uuidCreditMemoIdentifierGenerator;

    public function __construct(
        CreditMemoUnitGeneratorInterface $orderItemUnitCreditMemoUnitGenerator,
        CreditMemoUnitGeneratorInterface $shipmentCreditMemoUnitGenerator,
        NumberGenerator $creditMemoNumberGenerator,
        CurrentDateTimeProviderInterface $currentDateTimeProvider,
        CreditMemoIdentifierGeneratorInterface $uuidCreditMemoIdentifierGenerator
    ) {
        $this->orderItemUnitCreditMemoUnitGenerator = $orderItemUnitCreditMemoUnitGenerator;
        $this->shipmentCreditMemoUnitGenerator = $shipmentCreditMemoUnitGenerator;
        $this->creditMemoNumberGenerator = $creditMemoNumberGenerator;
        $this->currentDateTimeProvider = $currentDateTimeProvider;
        $this->uuidCreditMemoIdentifierGenerator = $uuidCreditMemoIdentifierGenerator;
    }

    public function generate(
        OrderInterface $order,
        int $total,
        array $units,
        array $shipments,
        string $comment
    ): CreditMemoInterface {
        /** @var ChannelInterface $channel */
        $channel = $order->getChannel();

        $creditMemoUnits = [];

        /** @var UnitRefundInterface $unit */
        foreach ($units as $unit) {
            $creditMemoUnits[] = $this->orderItemUnitCreditMemoUnitGenerator
                ->generate($unit->id(), $unit->total())
                ->serialize()
            ;
        }

        /** @var UnitRefundInterface $shipment */
        foreach ($shipments as $shipment) {
            $creditMemoUnits[] = $this->shipmentCreditMemoUnitGenerator
                ->generate($shipment->id(), $shipment->total())
                ->serialize()
            ;
        }

        return new CreditMemo(
            $this->uuidCreditMemoIdentifierGenerator->generate(),
            $this->creditMemoNumberGenerator->generate(),
            $order,
            $total,
            $order->getCurrencyCode(),
            $order->getLocaleCode(),
            $channel,
            $creditMemoUnits,
            $comment,
            $this->currentDateTimeProvider->now(),
            $this->getFromAddress($order->getBillingAddress()),
            $this->getToAddress($channel->getShopBillingData())
        );
    }

    private function getFromAddress(AddressInterface $address): CustomerBillingData
    {
        return new CustomerBillingData(
            $address->getFirstName() . ' ' . $address->getLastName(),
            $address->getStreet(),
            $address->getPostcode(),
            $address->getCountryCode(),
            $address->getCity(),
            $address->getCompany(),
            $address->getProvinceName(),
            $address->getProvinceCode()
        );
    }

    private function getToAddress(?ChannelShopBillingData $channelShopBillingData): ?ShopBillingData
    {
        if (
            $channelShopBillingData === null ||
            ($channelShopBillingData->getStreet() === null && $channelShopBillingData->getCompany() === null)
        ) {
            return null;
        }

        return new ShopBillingData(
            $channelShopBillingData->getCompany(),
            $channelShopBillingData->getTaxId(),
            $channelShopBillingData->getCountryCode(),
            $channelShopBillingData->getStreet(),
            $channelShopBillingData->getCity(),
            $channelShopBillingData->getPostcode()
        );
    }
}
