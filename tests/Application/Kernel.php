<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Application;

use ProxyManager\Proxy\VirtualProxyInterface;
use PSS\SymfonyMockerContainer\DependencyInjection\MockerContainer;
use Sylius\Bundle\CoreBundle\Application\Kernel as SyliusKernel;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

final class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    private const IGNORED_SERVICES_DURING_CLEANUP = [
        'kernel',
        'http_kernel',
        'liip_imagine.mime_type_guesser',
        'liip_imagine.extension_guesser',
    ];

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
        foreach ($this->getConfigurationDirectories() as $confDir) {
            yield from $this->registerBundlesFromFile($confDir . '/bundles.php');
        }
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        foreach ($this->getConfigurationDirectories() as $confDir) {
            $container->addResource(new FileResource($confDir . '/bundles.php'));
        }

        $container->setParameter('container.dumper.inline_class_loader', true);

        foreach ($this->getConfigurationDirectories() as $confDir) {
            $this->loadContainerConfiguration($loader, $confDir);
        }
    }

    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        foreach ($this->getConfigurationDirectories() as $confDir) {
            $this->loadRoutesConfiguration($routes, $confDir);
        }
    }

    protected function getContainerBaseClass(): string
    {
        if ($this->isTestEnvironment()) {
            return MockerContainer::class;
        }

        return parent::getContainerBaseClass();
    }

    private function isTestEnvironment(): bool
    {
        return 0 === strpos($this->getEnvironment(), 'test');
    }

    private function loadContainerConfiguration(LoaderInterface $loader, string $confDir): void
    {
        $loader->load($confDir . '/{packages}/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{packages}/' . $this->environment . '/**/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{services}' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{services}_' . $this->environment . self::CONFIG_EXTS, 'glob');
    }

    private function loadRoutesConfiguration(RouteCollectionBuilder $routes, string $confDir): void
    {
        $routes->import($confDir . '/{routes}/*' . self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir . '/{routes}/' . $this->environment . '/**/*' . self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir . '/{routes}' . self::CONFIG_EXTS, '/', 'glob');
    }

    /**
     * @return BundleInterface[]
     */
    private function registerBundlesFromFile(string $bundlesFile): iterable
    {
        $contents = require $bundlesFile;
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }
    }

    /**
     * @return string[]
     */
    private function getConfigurationDirectories(): iterable
    {
        yield $this->getProjectDir() . '/config';
        yield $this->getProjectDir() . '/config/sylius/' . SyliusKernel::MAJOR_VERSION . '.' . SyliusKernel::MINOR_VERSION;
    }
}
