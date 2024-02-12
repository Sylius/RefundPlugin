### UPGRADE FROM 1.4.X TO 1.5.0

1. Passing `$creditMemoPdfFileGenerator` and `$fileManager` to the constructor of
   `Sylius\RefundPlugin\Sender\CreditMemoEmailSender` as the first and third
   arguments respectively has been deprecated and will be prohibited in 2.0.

2. The `Sylius\RefundPlugin\File\FileManagerInterface` interface,
   `Sylius\RefundPlugin\File\TemporaryFileManager` class and
   service with id `Sylius\RefundPlugin\File\TemporaryFileManager`
   have all been deprecated and will be removed in 2.0. There is no replacement.
