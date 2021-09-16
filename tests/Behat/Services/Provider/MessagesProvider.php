<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Services\Provider;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Webmozart\Assert\Assert;

final class MessagesProvider
{
    private string $spoolDirectory;

    public function __construct(string $spoolDirectory)
    {
        $this->spoolDirectory = $spoolDirectory;
    }

    /**
     * @return array|\Swift_Message[]
     */
    public function getMessages(): array
    {
        $finder = new Finder();
        $directory = $this->spoolDirectory;
        $finder->files()->name('*.message')->in($directory);
        Assert::notEq($finder->count(), 0, sprintf('No message files found in %s.', $directory));
        $messages = [];

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $messages[] = unserialize($file->getContents());
        }

        return $messages;
    }
}
