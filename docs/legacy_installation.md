### Legacy installation (without SymfonyFlex)

1. Require plugin with composer:

    ```bash
    composer require sylius/refund-plugin
    ```

2. Add plugin class and other required bundles to your `AppKernel`:

    ```php
    $bundles = [
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
    
6. If you install the plugin on Sylius 1.9 or higher, exclude duplicated migrations:

    ```bash
    bin/console doctrine:migration:sync-metadata-storage
    bin/console doctrine:migrations:version 'Sylius\RefundPlugin\Migrations\Version20201130071338' --add
    bin/console doctrine:migrations:version 'Sylius\RefundPlugin\Migrations\Version20201204071301' --add
    bin/console doctrine:migrations:version 'Sylius\RefundPlugin\Migrations\Version20201208105207' --add
    ``` 

7. Apply migrations to your database:

    ```bash
    bin/console doctrine:migrations:migrate
    ```

8. Copy Sylius templates overridden in plugin to your templates directory (e.g `templates/bundles/`):

    ```bash
    mkdir -p templates/bundles/SyliusAdminBundle/
    cp -R vendor/sylius/refund-plugin/src/Resources/views/SyliusAdminBundle/* templates/bundles/SyliusAdminBundle/
    ```

9. Clear cache:

    ```bash
    bin/console cache:clear
    ```
