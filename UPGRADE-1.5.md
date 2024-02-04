### UPGRADE FROM 1.4.X TO 1.5.0

1. Passing `$creditMemoPdfFileGenerator` and `$fileManager` to the constructor of
   `Sylius\RefundPlugin\Sender\CreditMemoEmailSender` as the first and third
   arguments respectively has been deprecated and will be prohibited in 2.0.
