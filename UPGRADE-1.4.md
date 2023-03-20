### UPGRADE FROM 1.3.X TO 1.4.0

1. The `Sylius\RefundPlugin\Listener\ShipmentRefundedEventListener` has been removed in favor of
   `Sylius\RefundPlugin\Listener\UnitRefundedEventListener` and the `UnitRefundedEventListener` listens now to all
   events implementing `Sylius\RefundPlugin\Event\UnitRefundedInterface`.
