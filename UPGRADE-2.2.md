# UPGRADE FROM 2.1 TO 2.2

1. Sylius 2.3 no longer ships `knplabs/gaufrette` and `knplabs/knp-gaufrette-bundle`, so the plugin now requires them itself.
   `knplabs/knp-gaufrette-bundle` 1.0 supports Symfony 8, but it also upgrades `knplabs/gaufrette` to 1.0,
   so verify that the Gaufrette adapters you use still work with it.

   Make sure `Knp\Bundle\GaufretteBundle\KnpGaufretteBundle` is registered in your `config/bundles.php`.
   Nothing else changes: credit memos are still stored through the `gaufrette.sylius_refund_credit_memo_filesystem` filesystem.

   Gaufrette is used by the legacy PDF generator and will be removed in 3.0, together with the `sylius_refund.pdf_generator.legacy` option.
   Migrating to the `SyliusPdfGenerationBundle` integration is recommended:

    ```yaml
    sylius_refund:
        pdf_generator:
            legacy: false
    ```

   With the integration enabled, the storage of credit memos is configured through the `sylius_refund` context
   of `SyliusPdfGenerationBundle`, which supports the `filesystem`, `flysystem` and `gaufrette` storage types.
   It defaults to the Gaufrette filesystem above. The plugin also provides the `sylius_refund.storage.credit_memo` Flysystem storage,
   using the local adapter in the same `%sylius_refund.credit_memo_save_path%` directory, which will become the default in 3.0.
   To switch to it already, so existing credit memos are still found:

    ```yaml
    sylius_pdf_generation:
        contexts:
            sylius_refund:
                storage:
                    type: flysystem
                    filesystem: sylius_refund.storage.credit_memo
    ```

   To store credit memos elsewhere (e.g. on S3), redefine the `sylius_refund.storage.credit_memo` storage under `flysystem.storages`,
   see the [FlysystemBundle documentation](https://github.com/thephpleague/flysystem-bundle/blob/3.x/docs/2-cloud-storage-providers.md).
   See the [SyliusPdfGenerationBundle documentation](https://github.com/Sylius/PdfGenerationBundle#configuration) for the other storage types.
