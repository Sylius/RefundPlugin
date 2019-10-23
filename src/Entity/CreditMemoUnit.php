<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

/** @final */
class CreditMemoUnit implements CreditMemoUnitInterface
{
    /** @var string */
    protected $productName;

    /** @var int */
    protected $total;

    /** @var int */
    protected $taxesTotal;

    public function __construct(string $productName, int $total, int $taxesTotal)
    {
        $this->productName = $productName;
        $this->total = $total;
        $this->taxesTotal = $taxesTotal;
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

    public function serialize(): string
    {
        $serialized = json_encode([
            'product_name' => $this->productName,
            'total' => $this->total,
            'taxes_total' => $this->taxesTotal,
        ]);

        if ($serialized === false) {
            throw new \Exception('Credit memo unit could have not been serialized');
        }

        return $serialized;
    }

    public static function unserialize(string $serialized): self
    {
        $data = json_decode($serialized, true);

        return new self(
            $data['product_name'],
            $data['total'],
            $data['taxes_total']
        );
    }
}
