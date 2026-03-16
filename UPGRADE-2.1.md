# UPGRADE FROM 2.0 TO 2.1

1. Support for the `SyliusPdfGenerationBundle` has been added as an alternative to the legacy PDF generation
   which was using `KnpSnappyBundle` with a hardcoded `wkhtmltopdf` binary.
   To use it, set the `legacy` option to `false` in your configuration:

    ```yaml
    sylius_refund:
        pdf_generator:
            legacy: false
    ```

   The bundle is preconfigured with `knp_snappy` adapter and `gaufrette` storage by default, with a `sylius_refund` context making it a drop-in replacement.

1. The following services now accept new argument types from the `SyliusPdfGenerationBundle`. Passing the old types is deprecated and will be removed in 3.0:

   - `Sylius\RefundPlugin\Generator\CreditMemoPdfFileGenerator`:

     ```diff
     public function __construct(
         private RepositoryInterface $creditMemoRepository,
         private FileLocatorInterface $fileLocator,
         private string $template,
         private string $creditMemoLogoPath,
     -   private TwigToPdfGeneratorInterface $twigToPdfGenerator,
     +   private TwigToPdfGeneratorInterface|TwigToPdfRendererInterface $twigToPdfRenderer,
         private CreditMemoFileNameGeneratorInterface $creditMemoFileNameGenerator,
     )
     ```

   - `Sylius\RefundPlugin\Resolver\CreditMemoFileResolver`:

     ```diff
     public function __construct(
         private CreditMemoRepositoryInterface $creditMemoRepository,
         private CreditMemoFileProviderInterface $creditMemoFileProvider,
         private CreditMemoPdfFileGeneratorInterface $creditMemoPdfFileGenerator,
     -   private CreditMemoFileManagerInterface $creditMemoFileManager,
     +   private CreditMemoFileManagerInterface|PdfFileManagerInterface $creditMemoFileManager,
     +   private ?CreditMemoFileNameGeneratorInterface $creditMemoFileNameGenerator = null,
     )
     ```

   - `Sylius\RefundPlugin\Resolver\CreditMemoFilePathResolver`:

     ```diff
     public function __construct(
     -   private string $creditMemosPath,
     +   private string|PdfFileManagerInterface $creditMemosPath,
     )
     ```

1. The following classes, interfaces, and services have been deprecated and will be removed in 3.0:

   | Deprecated                                                        | Replacement                                                                  |
   |-------------------------------------------------------------------|------------------------------------------------------------------------------|
   | `Sylius\RefundPlugin\Generator\TwigToPdfGeneratorInterface`       | `Sylius\PdfGenerationBundle\Core\Renderer\TwigToPdfRendererInterface`        |
   | `Sylius\RefundPlugin\Generator\TwigToPdfGenerator`                | `Sylius\PdfGenerationBundle\Core\Renderer\TwigToPdfRendererInterface`        |
   | `Sylius\RefundPlugin\Generator\PdfOptionsGeneratorInterface`      | `sylius/pdf-generation-bundle` option processors                             |
   | `Sylius\RefundPlugin\Generator\PdfOptionsGenerator`               | `sylius/pdf-generation-bundle` option processors                             |
   | `Sylius\RefundPlugin\Manager\CreditMemoFileManagerInterface`      | `Sylius\PdfGenerationBundle\Core\Filesystem\Manager\PdfFileManagerInterface` |
   | `Sylius\RefundPlugin\Manager\CreditMemoFileManager`               | `Sylius\PdfGenerationBundle\Core\Filesystem\Manager\PdfFileManager`          |

   The corresponding services (`sylius_refund.generator.twig_to_pdf`, `sylius_refund.generator.pdf_options`, and `sylius_refund.manager.credit_memo_file`) are also deprecated.
