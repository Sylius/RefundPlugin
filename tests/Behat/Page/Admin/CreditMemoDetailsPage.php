<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Page\Admin;

use Behat\Mink\Session;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Sylius\Behat\Service\Accessor\TableAccessorInterface;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

final class CreditMemoDetailsPage extends SymfonyPage implements CreditMemoDetailsPageInterface
{
    /** @var TableAccessorInterface */
    private $tableAccessor;

    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        TableAccessorInterface $tableAccessor
    ) {
        parent::__construct($session, $minkParameters, $router);

        $this->tableAccessor = $tableAccessor;
    }

    public function getRouteName(): string
    {
        return 'sylius_refund_credit_memo_details';
    }

    public function hasItem(
        int $quantity,
        string $productName,
        string $netValue,
        string $grossValue,
        string $taxAmount,
        string $currencyCode
    ): bool {
        $table = $this->getElement('table');

        try {
            $this->tableAccessor->getRowWithFields($table, [
                'name' => $productName,
                'quantity' => $quantity,
                'net_value' => $netValue,
                'gross_value' => $grossValue,
                'tax_amount' => $taxAmount,
                'currency_code' => $currencyCode,
            ]);
        } catch (\InvalidArgumentException $exception) {
            return false;
        }

        return true;
    }

    public function hasTaxItem(string $label, string $amount, string $currencyCode): bool
    {
        $taxItemAmountElement = $this->getElement('tax_item_amount', ['%label%' => $label]);
        $taxItemCurrencyCodeElement = $taxItemAmountElement->getParent()->find('css', '.tax-item-currency-code');
        Assert::notNull($taxItemCurrencyCodeElement);

        return $amount === $taxItemAmountElement->getText() && $currencyCode = $taxItemCurrencyCodeElement->getText();
    }

    public function download(): void
    {
        $this->getDocument()->clickLink('Download');
    }

    public function getNumber(): string
    {
        return $this->getDocument()->find('css', '#credit-memo-number')->getText();
    }

    public function getChannelName(): string
    {
        $items = $this->getDocument()->findAll('css', '.channel > .channel__item');

        return $items[1]->getText();
    }

    public function getTotal(): string
    {
        return $this->getElement('total')->getText();
    }

    public function getTotalCurrencyCode(): string
    {
        return $this->getElement('total_currency_code')->getText();
    }

    public function getComment(): string
    {
        return $this->getDocument()->find('css', '#credit-memo-comment')->getText();
    }

    public function getFromAddress(): string
    {
        return $this->getDocument()->find('css', '#from-address')->getText();
    }

    public function getToAddress(): string
    {
        return $this->getDocument()->find('css', '#to-address')->getText();
    }

    public function isCreditMemoInPosition(string $creditMemo, int $position): bool
    {
        return $this->getElement('credit_memo_in_given_position', [
            '%position%' => $position,
            '%creditMemo%' => $creditMemo,
        ]) !== null;
    }

    public function getNetTotal(): string
    {
        return $this->getElement('net_total')->getText();
    }

    public function getTaxTotal(): string
    {
        return $this->getElement('tax_total')->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'credit_memo_in_given_position' => 'table tbody tr:nth-child(%position%) td:contains("%creditMemo%")',
            'net_total' => '#credit-memo-net-value-total',
            'table' => 'table',
            'tax_item_amount' => 'tr.tax-item:contains("%label%") .tax-item-amount',
            'tax_total' => '#credit-memo-tax-total',
            'total' => '#credit-memo-total',
            'total_currency_code' => '#credit-memo-total-currency-code',
        ]);
    }
}
