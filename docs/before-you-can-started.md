# Before you can started

By default PayumBundle knows nothing about jms payment bridge.
To make payum be aware of it you have to add its factory.
Let's suppose you have `AcmePaymentBundle`.
You have to add factory inside its build method:

```php
<?php
namespace Acme\PaymentBundle;

use Payum\Bridge\JMSPayment\DependencyInjection\Factory\Payment\JMSPaymentPaymentFactory;
use Payum\Bundle\PayumBundle\DependencyInjection\PayumExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AcmePaymentBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        /** @var  PayumExtension $payumExtension */
        $payumExtension = $container->getExtension('payum');

        $payumExtension->addPaymentFactory(new JMSPaymentPaymentFactory);
    }
}

```
