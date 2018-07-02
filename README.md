# SyliusRefundPlugin

SyliusRefundPlugin provides basic refunds functionality for Sylius application.

## Installation

Require plugin with composer:

```bash
composer require sylius/refund-plugin
```

Add plugin class to your `AppKernel`:

```php
$bundles = [
    new \Sylius\RefundPlugin\SyliusRefundPlugin(),
];
```

Clear cache:

```bash
bin/console cache:clear
```
