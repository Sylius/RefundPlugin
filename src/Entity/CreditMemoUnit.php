<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

/** @final */
class CreditMemoUnit implements CreditMemoUnitInterface
{
    /** @var string */
    private $productName;

    /** @var int */
    private $total;

    /** @var int */
    private $taxesTotal;

    /** @var int */
    private $discount;

    public function __construct(string $productName, int $total, int $taxesTotal, int $discount)
    {
        $this->productName = $productName;
        $this->total = $total;
        $this->taxesTotal = $taxesTotal;
        $this->discount = $discount;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getTaxesTotal(): int
    {
        return $this->taxesTotal;
    }

    public function getDiscount(): int
    {
        return $this->discount;
    }

    public function serialize(): string
    {
        return json_encode([
            'product_name' => $this->productName,
            'total' => $this->total,
            'taxes_total' => $this->taxesTotal,
            'discount' => $this->discount,
        ]);
    }

    public static function unserialize(string $serialized): self
    {
        $data = json_decode($serialized, true);

        return new self(
            $data['product_name'],
            $data['total'],
            $data['taxes_total'],
            $data['discount']
        );
    }
}
