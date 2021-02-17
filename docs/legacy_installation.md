### Legacy installation (without SymfonyFlex)

1. Require plugin with composer:

    ```bash
    composer require sylius/refund-plugin
    ```

1. Add plugin class and other required bundles to your `AppKernel`:

    ```php
    $bundles = [
       new \Knp\Bundle\SnappyBundle\KnpSnappyBundle(),
       new \Sylius\RefundPlugin\SyliusRefundPlugin(),
    ];
    ```

1. Import configuration:

    ```yaml
    imports:
        - { resource: "@SyliusRefundPlugin/Resources/config/app/config.yml" }
    ```
1. Import routing:

    ````yaml
    sylius_refund:
        resource: "@SyliusRefundPlugin/Resources/config/routing.yml"
    ````

1. Configure `KnpSnappyBundle` (if you don't have it configured yet):

    ````yaml
    knp_snappy:
        pdf:
            enabled: true
            binary: #path to your wkhtmltopdf binary file
            options: []
    ````
    
1. Apply migrations to your database:

    ```bash
    bin/console doctrine:migrations:migrate
    ```

1. Copy Sylius templates overridden in plugin to your templates directory (e.g `templates/bundles/`):

    ```bash
    mkdir -p templates/bundles/SyliusAdminBundle/
    cp -R vendor/sylius/refund-plugin/src/Resources/views/SyliusAdminBundle/* templates/bundles/SyliusAdminBundle/
    ```

1. Clear cache:

    ```bash
    bin/console cache:clear
    ```
