<p align="center">
    <a href="https://sylius.com" target="_blank">
        <img src="https://demo.sylius.com/assets/shop/img/logo.png" />
    </a>
</p>

<h1 align="center">Refund Plugin</h1>

<p align="center"><img src="https://travis-ci.org/Sylius/RefundPlugin.svg?branch=master"></p>

<p align="center">This plugin provides basic refunds functionality for Sylius application.</p>

![Screenshot showing order's refund section](docs/refunds.png)

![Screenshot showing order's credit memos and refund payments](docs/credit_memo.png)

## Business value

In contrast to basic Refund functionality delivered by core Sylius bundles, Refund Plugin offers much wider range of 
possibilities and business scenarios.

Once an Order is paid, an Administrator is able to access Refunds section of a given Order and perform a Refund
of chosen items or shipments. What's more, if a more detailed scenario occurs, an Administrator is able to refund an item
partially.

From Administrator's point of view, every Refund request results in creating two entities: 
* Credit Memo - a document representing a list of refunded items (downloadable and sent to Customer via .pdf file)
* Refund Payment - entity representing payment in favour of the Customer

## Installation

1. Require plugin with composer:

    ```bash
    composer require sylius/refund-plugin
    ```

2. Add plugin class and other required bundles to your `AppKernel`:

    ```php
    $bundles = [
       new Prooph\Bundle\ServiceBus\ProophServiceBusBundle(),
       new \Knp\Bundle\SnappyBundle\KnpSnappyBundle(),
       new \Sylius\RefundPlugin\SyliusRefundPlugin(),
    ];
    ```

3. Import configuration:

    ```yaml
    imports:
        - { resource: "@SyliusRefundPlugin/Resources/config/app/config.yml" }
    ```
4. Import routing:

    ````yaml
    sylius_refund:
        resource: "@SyliusRefundPlugin/Resources/config/routing.yml"
    ````

5. Configure `KnpSnappyBundle` (if you don't have it configured yet):

    ````yaml
    knp_snappy:
        pdf:
            enabled: true
            binary: #path to your wkhtmltopdf binary file
            options: []
    ````

6. Clear cache:

    ```bash
    bin/console cache:clear
    ```
    
6. Copy migrations from `vendor/sylius/refund-plugin/migrations/`
to your migrations directory and run `bin/console doctrine:migrations:migrate`

7. Copy templates from `vendor/sylius/refund-plugin/src/Resources/views/SyliusAdminBundle/`
to `app/Resources/SyliusAdminBundle/views/`

## Extension points

Refund Plugin is strongly based on both commands and events. Let's take RefundUnitsAction as an example. The whole
process consists of following steps:

* Getting data from request
* Create a Command and fill it with data
* Dispatch Command
* Handle Command
* Fire Event
* Catch Event in Listener class

Using command pattern and events make each step independent which means that providing custom implementation of given
part of refunding process doesn't affect any other step.

Apart from Events and Commands Refund Plugin is also based on mechanisms derived from core Sylius bundles such as:

* [Resources](https://docs.sylius.com/en/1.2/components_and_bundles/components/Resource/index.html)
* [Grids](https://docs.sylius.com/en/1.2/components_and_bundles/bundles/SyliusGridBundle/index.html)
* [State Machine](https://docs.sylius.com/en/1.2/book/architecture/state_machine.html)

Configuration of all elements mentioned above can be found and customized in `config.yml` file.
