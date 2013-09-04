JMSPayment Bridge
=================

There are set of payment bundles written by Johannes Schmitt and others, now you can reuse them inside PayumBundle. 

* [jms bundles official site](http://jmsyst.com/bundles/JMSPaymentCoreBundle).
* [payum bundle](https://github.com/Payum/PayumBundle).
* [sandbox example, see others section](https://github.com/Payum/PayumBundleSandbox).

## Before you can started

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

## Configure a context:

Once you added the factory you can configure payum context.

```yml
jms_payment_core:
    secret:                                               %kernel.secret%

jms_payment_paypal:
    username:                                             %paypal.express_checkout.username%
    password:                                             %paypal.express_checkout.password%
    signature:                                            %paypal.express_checkout.signature%
    debug:                                                true

payum:
    contexts:
        paypal_express_checkout_via_jms_plugin:
            jms_payment_plugin: ~
            storages:
                JMS\Payment\CoreBundle\Entity\Payment:
                    doctrine:
                        driver: orm
                        payment_extension: true
                Acme\PaymentBundle\Model\TokenizedDetails:
                    filesystem:
                        storage_dir: %kernel.root_dir%/Resources/payments
                        id_property: token
                        payment_extension: true
```

Not so hard so far, let's continue.

## How to capture?

```php
<?php
public function prepareAction(Request $request)
{
    $paymentName = 'paypal_express_checkout_via_jms_plugin';

    $paymentInstruction = new PaymentInstruction(
        $data['amount'],
        $data['currency'],
        'paypal_express_checkout'
    );
    $paymentInstruction->setState(PaymentInstruction::STATE_VALID);

    $payment = new Payment($paymentInstruction, $data['amount']);

    $this->getDoctrine()->getManager()->persist($paymentInstruction);
    $this->getDoctrine()->getManager()->persist($payment);
    $this->getDoctrine()->getManager()->flush();

    $captureToken = $this->get('payum.security.token_factory')->createTokenForCaptureRoute(
        $paymentName,
        $payment,
        'purchase_done_paypal_via_jms_plugin'
    );

    $payment->getPaymentInstruction()->getExtendedData()->set(
        'return_url',
        $captureToken->getTargetUrl()
    );
    $payment->getPaymentInstruction()->getExtendedData()->set(
        'cancel_url',
        $captureToken->getTargetUrl()
    );

    //the state manipulations  is needed for saving changes in extended data.
    $oldState = $payment->getPaymentInstruction()->getState();
    $payment->getPaymentInstruction()->setState(PaymentInstruction::STATE_INVALID);

    $this->getDoctrine()->getManager()->persist($paymentInstruction);
    $this->getDoctrine()->getManager()->persist($payment);
    $this->getDoctrine()->getManager()->flush();

    $payment->getPaymentInstruction()->setState($oldState);

    $this->getDoctrine()->getManager()->persist($paymentInstruction);
    $this->getDoctrine()->getManager()->flush();

    return $this->redirect($captureToken->getTargetUrl());
}
```

## After purchase is done.

Have you noticed `purchase_done_paypal_via_jms_plugin`  the third parameter of `createTokenForCaptureRoute` method?
It's the route of action where we are redirected after the capture is done. In that action we have to check a status.

```php
<?php
public function viewAction(Request $request)
{
    $token = $this->get('payum.security.http_request_verifier')->verify($request);

    $status = new BinaryMaskStatusRequest($token);

    $this->get('payum')->getPayment($token->getPaymentName())->execute($status);

    //do some stuff depends on status. for example show status and details

    return array(
        'status' => $status,
    );
}
```

## Like it? Spread the world!

You can star the lib on [github](https://github.com/Payum/JMSPaymentBridge) or [packagist](https://packagist.org/packages/Payum/JMSPaymentBridge). You may also drop a message on Twitter.

## Need support?

If you are having general issues with [JMSPaymentBridge](https://github.com/Payum/JMSPaymentBridge) or [payum](https://github.com/Payum/Payum), we suggest posting your issue on [stackoverflow](http://stackoverflow.com/). Feel free to ping @maksim_ka2 on Twitter if you can't find a solution.

If you believe you have found a bug, please report it using the GitHub issue tracker: [JMSPaymentBridge](https://github.com/Payum/JMSPaymentBridge/issues) or [payum](https://github.com/Payum/Payum/issues), or better yet, fork the library and submit a pull request.

## License

JMSPaymentBridge is released under the MIT License. For more information, see [License](LICENSE).
