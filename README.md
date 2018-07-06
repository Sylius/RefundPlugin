<p align="center">
    <a href="https://sylius.com" target="_blank">
        <img src="https://demo.sylius.com/assets/shop/img/logo.png" />
    </a>
</p>

<h1 align="center">Refund Plugin</h1>

<p align="center">This plugin provides basic refunds functionality for Sylius application.</p>

## Installation

1. Require plugin with composer:

    ```bash
    composer require sylius/refund-plugin
    ```

2. Add plugin class to your `AppKernel`:

    ```php
    $bundles = [
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
        resource: "@SyliusRefundPlugin/Resources/config/app/routing.yml"
    ````

5. Clear cache:

    ```bash
    bin/console cache:clear
    ```
    
