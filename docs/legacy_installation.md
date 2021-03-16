### Legacy installation (without SymfonyFlex)

1. Require plugin with composer:

    ```bash
    composer require sylius/refund-plugin:1.0.0-RC.7
    ```

1. Add plugin class and other required bundles to your `AppKernel`:

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

1. Configure `KnpSnappyBundle` (if you don't have it configured yet):

    ````yaml
    knp_snappy:
        pdf:
            enabled: true
            binary: #path to your wkhtmltopdf binary file
            options: []
    ````
    
1. Apply migrations to your database:

    ```bash
    bin/console doctrine:migrations:migrate
    ```

1. Copy Sylius templates overridden in plugin to your templates directory (e.g `templates/bundles/`):

    ```bash
    mkdir -p templates/bundles/SyliusAdminBundle/
    cp -R vendor/sylius/refund-plugin/src/Resources/views/SyliusAdminBundle/* templates/bundles/SyliusAdminBundle/
    ```

1. If you use Sylius v1.8 you also need to change files `src/Entity/Shipping/Shipment.php` and `src/Entity/Order/Adjustment.php` to use proper traits and interfaces:

    ```php
    <?php
    
    declare(strict_types=1);
    
    namespace App\Entity\Order;
    
    use Doctrine\ORM\Mapping as ORM;
    use Sylius\Component\Order\Model\Adjustment as BaseAdjustment;
    use Sylius\RefundPlugin\Entity\AdjustmentInterface as RefundAdjustmentInterface;
    use Sylius\RefundPlugin\Entity\AdjustmentTrait;
    
    /**
    * @ORM\Entity
    * @ORM\Table(name="sylius_adjustment")
    */
    class Adjustment extends BaseAdjustment implements RefundAdjustmentInterface
    {
        use AdjustmentTrait;
    }
    ```

    ```php 
    <?php
    
    declare(strict_types=1);
    
    namespace App\Entity\Shipping;
    
    use Doctrine\Common\Collections\ArrayCollection;
    use Doctrine\ORM\Mapping as ORM;
    use Sylius\Component\Core\Model\AdjustmentInterface as BaseAdjustmentInterface;
    use Sylius\Component\Core\Model\Shipment as BaseShipment;
    use Sylius\RefundPlugin\Entity\ShipmentTrait;
    use Sylius\RefundPlugin\Entity\ShipmentInterface as RefundShipmentInterface;
    
    /**
    * @ORM\Entity
    * @ORM\Table(name="sylius_shipment")
    */
    class Shipment extends BaseShipment implements RefundShipmentInterface
    {
        use ShipmentTrait;
    
        public function __construct()
        {
            parent::__construct();
    
            /** @var ArrayCollection<array-key, BaseAdjustmentInterface> $this->adjustments */
            $this->adjustments = new ArrayCollection();
        }
    }
    ```

1. Clear cache:

    ```bash
    bin/console cache:clear
    ```
