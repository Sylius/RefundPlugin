### UPGRADE FROM 1.2.X TO 1.3.0

1. Support for Sylius 1.12 has been added, it is now the recommended Sylius version to use with RefundPlugin.

2. Support for Sylius 1.10 has been dropped, upgrade your application to [Sylius 1.11](https://github.com/Sylius/Sylius/blob/master/UPGRADE-1.11.md).
   or to [Sylius 1.12](https://github.com/Sylius/Sylius/blob/master/UPGRADE-1.12.md).

3. Support for Symfony 6 has been added.

4. Support for Symfony 4.4 has been dropped.

5. Not passing the `$creditMemoFileNameGenerator` to `Sylius\RefundPlugin\Generator\CreditMemoPdfFileGenerator` constructor 
   is deprecated since 1.3 version and will be prohibited in 2.0.

6. Not passing the `$creditMemoPdfFileGenerator`, the `$creditMemoFileManager` and the `$hasEnabledPdfFileGenerator`
   to `Sylius\RefundPlugin\CommandHandler\GenerateCreditMemoHandler` constructor is deprecated since 1.3 version and will be prohibited in 2.0.

7. Not passing the `$creditMemoFileResolver` and the `$creditMemoFilePathResolver` to `Sylius\RefundPlugin\Sender\CreditMemoEmailSender`
   constructor is deprecated since 1.3 version and will be prohibited in 2.0.

8. The first argument of `Sylius\RefundPlugin\Action\Admin\DownloadCreditMemoAction` and `Sylius\RefundPlugin\Action\Shop\DownloadCreditMemoAction`
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
