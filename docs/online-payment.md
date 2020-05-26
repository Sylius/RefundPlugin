Online Payment
==============

If you configured online payment, you probably want as well online refunds. The best way to do the following tasks:

## Add payment methods to the related parameter (examples bellow in XML and YAML)

```yaml
# services.yaml
parameters:
    sylius_refund.supported_gateways:
        - offline
        - stripe
```

```xml
<!-- services.xml -->
    <parameters>
        <parameter key="sylius_refund.supported_gateways" type="collection">
            <parameter>offline</parameter>
            <parameter>stripe</parameter>
        </parameter>
    </parameters>
```

## Create your custom handler

Here is an example that binds the refund plugin to payum.

```php
class RefundPaymentGeneratedHandler
{
    private ObjectManager $objectManager;
    private Payum $payum;
    private FactoryInterface $stateMachineFactory;

    public function __construct(ObjectManager $objectManager, Payum $payum, FactoryInterface $stateMachineFactory)
    {
        $this->objectManager = $objectManager;
        $this->payum = $payum;
        $this->stateMachineFactory = $stateMachineFactory;
    }

    public function __invoke(RefundPaymentGenerated $message): void
    {
        $orderRepository = $this->objectManager->getRepository(Order::class);
        $paymentMethodRepository = $this->objectManager->getRepository(PaymentMethod::class);

        $order = $orderRepository->findOneByNumber($message->orderNumber());
        $payment = $order->getLastPayment();

        // Notice: the payment method is not obviously the same as the one of the order payment
        $paymentMethod = $paymentMethodRepository->find($message->paymentMethodId());
        $gatewayName = $paymentMethod->getCode();

        // Call payum to refund
        $stripe = $this->payum->getGateway($gatewayName);
        $reply = $stripe->execute(new Refund([
            'amount' => $message->amount(),
            // You may want to add many other information here depending on your refund implementation
        ]));

        $refundPaymentRepository = $this->objectManager->getRepository(RefundPayment::class);
        $refundPayment = $refundPaymentRepository->find($message->id());

        // use the state machine of sylius to mark the refund done!
        $stateMachine = $this->stateMachineFactory->get($refundPayment, RefundPaymentTransitions::GRAPH);
        $stateMachine->apply(RefundPaymentTransitions::TRANSITION_COMPLETE);

        $this->objectManager->flush();
    }
}
```

Be careful, you need to register this handler against the correct bus, which is the specific event bus of this
plugin:

```yaml
# services.yaml
services:
    App\MessageHandler\RefundPaymentGeneratedHandler:
        tags: [{ name: messenger.message_handler, bus: sylius_refund_plugin.event_bus }]
```
