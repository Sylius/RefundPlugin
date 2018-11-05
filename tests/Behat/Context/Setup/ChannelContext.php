<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Context\Setup;


use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Test\Services\DefaultChannelFactoryInterface;


final class ChannelContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var DefaultChannelFactoryInterface
     */
    private $unitedStatesChannelFactory;

    /**
     * @var DefaultChannelFactoryInterface
     */
    private $defaultChannelFactory;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param DefaultChannelFactoryInterface $unitedStatesChannelFactory
     * @param DefaultChannelFactoryInterface $defaultChannelFactory
     * @param ChannelRepositoryInterface $channelRepository
     * @param ObjectManager $channelManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        DefaultChannelFactoryInterface $unitedStatesChannelFactory,
        DefaultChannelFactoryInterface $defaultChannelFactory
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->unitedStatesChannelFactory = $unitedStatesChannelFactory;
        $this->defaultChannelFactory = $defaultChannelFactory;
    }

    /**
     * @Given the store operates on a single green channel in "United States"
     */
    public function storeOperatesOnASingleColorChannelInUnitedStates()
    {
        $defaultData = $this->unitedStatesChannelFactory->create();
        $defaultData->setColor('green');

        $this->sharedStorage->setClipboard($defaultData);
        $this->sharedStorage->set('channel', $defaultData['channel']);

    }

    /**
     * @Given /^the store(?:| also) operates on (?:a|another) channel named "([^"]+)" in "([^"]+)" currency with "([^"]+)" color$/
     * @Given the store operates on a green channel identified by :code code
     */
    public function theStoreOperatesOnAColorChannelNamed($color, $channelName, $currencyCode = null)
    {
        $channelCode = StringInflector::nameToLowercaseCode($channelName);
        $defaultData = $this->defaultChannelFactory->create($channelCode, $channelName, $currencyCode);
        $defaultData->setColor($color);

        $this->sharedStorage->setClipboard($defaultData);
        $this->sharedStorage->set('channel', $defaultData['channel']);
    }

}
