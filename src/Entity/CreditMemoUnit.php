<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

/** @final */
class CreditMemoUnit implements CreditMemoUnitInterface, \Serializable
{
    /** @var int */
    private $id;

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

    public function getId(): int
    {
        return $this->id;
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

    public function unserialize($serialized): void
    {
        $data = json_decode($serialized, true);

        $this->productName = $data['product_name'];
        $this->total = $data['total'];
        $this->taxesTotal = $data['taxes_total'];
        $this->discount = $data['discount'];
    }
}

