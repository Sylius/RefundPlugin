<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

/** @final */
class TaxItem implements TaxItemInterface
{
    /** @var string */
    protected $label;

    /** @var int */
    protected $amount;

    public function __construct(string $label, int $amount)
    {
        $this->label = $label;
        $this->amount = $amount;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function serialize(): string
    {
        $serialized = json_encode(['label' => $this->label, 'amount' => $this->amount]);

        if ($serialized === false) {
            throw new \Exception('Tax item cannot be serialized.');
        }

        return $serialized;
    }

    public static function unserialize(string $serialized): self
    {
        $data = json_decode($serialized, true);

        return new self($data['label'], $data['amount']);
    }
}
