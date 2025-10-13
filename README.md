<p align="center">
    <a href="https://sylius.com" target="_blank">
        <picture>
          <source media="(prefers-color-scheme: dark)" srcset="https://media.sylius.com/sylius-logo-800-dark.png">
          <source media="(prefers-color-scheme: light)" srcset="https://media.sylius.com/sylius-logo-800.png">
          <img alt="Sylius Logo." src="https://media.sylius.com/sylius-logo-800.png">
        </picture>
    </a>
</p>

<h1 align="center">Refund Plugin</h1>

<p align="center"><img src="https://travis-ci.org/Sylius/RefundPlugin.svg?branch=master"></p>

<p align="center"><a href="https://sylius.com/plugins/" target="_blank"><img src="https://sylius.com/assets/badge-official-sylius-plugin.png" width="200"></a></p>

<p align="center">This plugin provides basic refunds functionality for Sylius application.</p>

---

## Documentation

📖 Full documentation is available here:
👉 [Refund Plugin Documentation](https://docs.sylius.com/refund-plugin)

## Customization

### Adding Custom Refund Types

If you need to add custom refund types (e.g., fees, commissions), you can extend the `RefundType` class:

1. **Create your custom RefundType class:**

```php
<?php

declare(strict_types=1);

namespace App\Model;

use Sylius\RefundPlugin\Model\RefundType as BaseRefundType;
use Sylius\RefundPlugin\Model\RefundTypeInterface;

class RefundType extends BaseRefundType implements RefundTypeInterface
{
    public const ORDER_FEES = 'commission';

    public static function commission(): self
    {
        return new self(self::ORDER_FEES);
    }
}
```

2. **Create your custom Doctrine RefundEnumType:**

```php
<?php

declare(strict_types=1);

namespace App\Doctrine\Type;

use App\Model\RefundType;
use Sylius\RefundPlugin\Entity\Type\RefundEnumType as BaseRefundEnumType;
use Sylius\RefundPlugin\Model\RefundTypeInterface;

class RefundEnumType extends BaseRefundEnumType
{
    protected function createType(string $value): RefundTypeInterface
    {
        return new RefundType($value);
    }
}
```

3. **Configure your application to use the custom classes:**

```yaml
# config/packages/sylius_refund.yaml
imports:
    - { resource: "@SyliusRefundPlugin/config/parameters.php" }

parameters:
    sylius_refund.refund_type: App\Model\RefundType
    sylius_refund.refund_enum_type: App\Doctrine\Type\RefundEnumType
```

4. **Clear the cache:**

```bash
php bin/console cache:clear
```

Now you can use your custom refund type in templates and business logic.

## Security issues

If you think that you have found a security issue, please do not use the issue tracker and do not post it publicly.
Instead, all security issues must be sent to `security@sylius.com`.

## Community

For online communication, we invite you to chat with us and other users on [Sylius Slack](https://sylius.com/slack).

## License

This plugin is released under the [MIT License](LICENSE).