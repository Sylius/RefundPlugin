<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

/** @final */
class CreditMemoSequence implements SequenceInterface
{
    /** @var int */
    protected $id;

    /** @var int */
    protected $index = 0;

    /** @var int */
    protected $version = 1;

    public function getId(): int
    {
        return $this->id;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function incrementIndex(): void
    {
        ++$this->index;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function setVersion(int $version): void
    {
        $this->version = $version;
    }
}
