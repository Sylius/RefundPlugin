### UPGRADE FROM 1.2.X TO 1.3.0

1. Not passing the `$creditMemoFileNameGenerator` to `Sylius\RefundPlugin\Generator\CreditMemoPdfFileGenerator` constructor 
   is deprecated since 1.3 version and will be prohibited in 2.0.

1. Not passing the `$creditMemoPdfFileGenerator`, the `$creditMemoFileManager` and the `$hasEnabledPdfFileGenerator`
   to `Sylius\RefundPlugin\CommandHandler\GenerateCreditMemoHandler` constructor is deprecated since 1.3 version and will be prohibited in 2.0.

1. Not passing the `$creditMemoFileResolver` and the `$creditMemoFilePathResolver` to `Sylius\RefundPlugin\Sender\CreditMemoEmailSender`
   constructor is deprecated since 1.3 version and will be prohibited in 2.0.

1. The first argument of `Sylius\RefundPlugin\Action\Admin\DownloadCreditMemoAction` and `Sylius\RefundPlugin\Action\Shop\DownloadCreditMemoAction`
   controllers has been changed:

```php
    public function __construct(
    -   private CreditMemoPdfFileGeneratorInterface $creditMemoPdfFileGenerator,
    +   private CreditMemoFilePathResolverInterface $creditMemoFileResolver,
        //...
    ) {
        //...
    }
```
