<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Entity;

interface SequenceInterface
{
    public function getId(): int;

    public function getIndex(): int;

    public function incrementIndex(): void;

    public function getVersion(): int;

    public function setVersion(int $version): void;
}
