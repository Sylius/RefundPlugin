### UPGRADE FROM 1.X TO 2.0

1. The way of customizing resource definition has been changed.

   Before:

    ```yaml
        sylius_resource:
            resources:
                sylius_refund.sample_resource:
                    ...
    ```  

   After:

    ```yaml
        sylius_refund:
            resources:
                sample_resource:
                    ...
    ```
