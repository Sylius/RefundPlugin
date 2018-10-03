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
    
6. Copy migrations from `vendor/sylius/refund-plugin/migrations/` to your migrations directory (e.g. `src/Migrations`)
and run `bin/console doctrine:migrations:migrate`

7. Copy templates from `vendor/sylius/refund-plugin/src/Resources/views/SyliusAdminBundle/`
to your templates directory (e.g `templates/bundles/SyliusAdminBundle/`)

8. Clear cache:

    ```bash
    bin/console cache:clear
    ```
