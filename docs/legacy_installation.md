### Legacy installation (without SymfonyFlex)

1. Require plugin with composer:

   ```bash
   composer require sylius/refund-plugin
   ```

1. Add plugin class and other required bundles to your `config/bundles.php`:

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

1. Check if you have `wkhtmltopdf` binary. If not, you can download it [here](https://wkhtmltopdf.org/downloads.html).

   In case `wkhtmltopdf` is not located in `/usr/local/bin/wkhtmltopdf`, add a following snippet at the end of your application's `config.yml`:

    ```yaml
    knp_snappy:
        pdf:
            enabled: true
            binary: /usr/local/bin/wkhtmltopdf # Change this! :)
            options: []
    ```   
    
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
