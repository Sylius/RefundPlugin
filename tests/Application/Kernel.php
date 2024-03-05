<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Application;

use PSS\SymfonyMockerContainer\DependencyInjection\MockerContainer;
use Sylius\Bundle\CoreBundle\SyliusCoreBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function getCacheDir(): string
    {
        return $this->getProjectDir() . '/var/cache/' . $this->environment;
    }

    public function getLogDir(): string
    {
        return $this->getProjectDir() . '/var/log';
    }

    public function registerBundles(): iterable
    {
        foreach ($this->getBundleListFiles() as $file) {
            yield from $this->registerBundlesFromFile($file);
        }
    }

    protected function getContainerBaseClass(): string
    {
        if ($this->isTestEnvironment() && class_exists(MockerContainer::class)) {
            return MockerContainer::class;
        }

        return parent::getContainerBaseClass();
    }

    private function isTestEnvironment(): bool
    {
        return str_starts_with($this->getEnvironment(), 'test');
    }

    /**
     * @return BundleInterface[]
     */
    private function registerBundlesFromFile(string $bundlesFile): iterable
    {
        $contents = require $bundlesFile;

        /** @var BundleInterface $class */
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }
    }

    /**
     * @return string[]
     */
    private function getBundleListFiles(): array
    {
        return array_filter(
            array_map(
                static function (string $directory): string {
                    return $directory . '/bundles.php';
                },
                $this->getConfigurationDirectories()
            ),
            'file_exists'
        );
    }

    /**
     * @return string[]
     */
    private function getConfigurationDirectories(): array
    {
        $directories = [
            $this->getProjectDir() . '/config',
            $this->getProjectDir() . '/config/sylius/' . SyliusCoreBundle::MAJOR_VERSION . '.' . SyliusCoreBundle::MINOR_VERSION,
        ];

        return array_filter($directories, 'file_exists');
    }
}
