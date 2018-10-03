### Legacy installation (without SymfonyFlex)

1. Require plugin with composer:

    ```bash
    composer require sylius/refund-plugin
    ```

2. Add plugin class and other required bundles to your `AppKernel`:

    ```php
    $bundles = [
       new Prooph\Bundle\ServiceBus\ProophServiceBusBundle(),
       new \Knp\Bundle\SnappyBundle\KnpSnappyBundle(),
       new \Sylius\RefundPlugin\SyliusRefundPlugin(),
    ];
    ```

3. Import configuration:

    ```yaml
    imports:
        - { resource: "@SyliusRefundPlugin/Resources/config/app/config.yml" }
    ```
4. Import routing:

    ````yaml
    sylius_refund:
        resource: "@SyliusRefundPlugin/Resources/config/routing.yml"
    ````

5. Configure `KnpSnappyBundle` (if you don't have it configured yet):

    ````yaml
    knp_snappy:
        pdf:
            enabled: true
            binary: #path to your wkhtmltopdf binary file
            options: []
    ````
    
2. Copy plugin migrations to your migrations directory (e.g. `src/Migrations`) and apply them to your database:

    ```bash
    cp -R vendor/sylius/refund-plugin/migrations/* src/Migrations
    bin/console doctrine:migrations:migrate
    ```

3. Copy Sylius templates overridden in plugin to your templates directory (e.g `templates/bundles/`):

    ```bash
    mkdir -p templates/bundles/SyliusAdminBundle/
    cp -R vendor/sylius/refund-plugin/src/Resources/views/SyliusAdminBundle/* templates/bundles/SyliusAdminBundle/
    ```

8. Clear cache:

    ```bash
    bin/console cache:clear
    ```
